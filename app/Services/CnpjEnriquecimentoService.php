<?php

namespace App\Services;

use App\Models\EmpresasConveniadas;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Serviço que consulta a BrasilAPI para enriquecer os dados
 * de localização das empresas conveniadas a partir do CNPJ.
 */
class CnpjEnriquecimentoService
{
    private const BRASIL_API_URL = 'https://brasilapi.com.br/api/cnpj/v1/';
    private const TIMEOUT_SEGUNDOS = 10;
    private const DELAY_ENTRE_REQUESTS_MS = 350; // ~3 req/s

    /**
     * Enriquece um único registro pelo CNPJ.
     * Retorna array com 'sucesso', 'mensagem' e 'dados' (opcional).
     */
    public function enriquecerEmpresa(EmpresasConveniadas $empresa): array
    {
        $cnpjLimpo = preg_replace('/\D/', '', (string) $empresa->cnpj);

        if (strlen($cnpjLimpo) !== 14) {
            $empresa->localizacao_verificada = true;
            $empresa->localizacao_verificada_em = now();
            $empresa->save();

            return [
                'sucesso' => false,
                'mensagem' => "CNPJ inválido (tamanho incorreto): {$empresa->cnpj}",
            ];
        }

        try {
            $response = Http::timeout(self::TIMEOUT_SEGUNDOS)
                ->withHeaders(['Accept' => 'application/json'])
                ->get(self::BRASIL_API_URL . $cnpjLimpo);

            if ($response->successful()) {
                $dados = $response->json();

                $empresa->municipio              = $dados['municipio'] ?? null;
                $empresa->uf                     = $dados['uf'] ?? null;
                $empresa->cep                    = $dados['cep'] ?? null;
                $empresa->logradouro             = $dados['logradouro'] ?? null;
                $empresa->numero                 = $dados['numero'] ?? null;
                $empresa->complemento            = $dados['complemento'] ?? null;
                $empresa->bairro                 = $dados['bairro'] ?? null;
                $empresa->natureza_juridica      = $dados['natureza_juridica'] ?? null;
                $empresa->localizacao_verificada    = true;
                $empresa->localizacao_verificada_em = now();
                $empresa->save();

                return [
                    'sucesso'  => true,
                    'mensagem' => 'OK',
                    'dados'    => [
                        'municipio' => $empresa->municipio,
                        'uf'        => $empresa->uf,
                    ],
                ];
            }

            if ($response->status() === 404 || $response->status() === 400) {
                // CNPJ inexistente ou inválido na Receita: marca como verificado para não reprocessar indefinidamente
                $empresa->localizacao_verificada = true;
                $empresa->localizacao_verificada_em = now();
                $empresa->save();

                $msg = $response->status() === 404
                    ? 'CNPJ não encontrado na Receita Federal.'
                    : 'CNPJ inválido segundo a Receita Federal.';

                return [
                    'sucesso'  => false,
                    'mensagem' => $msg,
                ];
            }

            if ($response->status() === 429) {
                // Rate limit: não marca como verificado para tentar depois
                sleep(2);
                return [
                    'sucesso'  => false,
                    'mensagem' => 'Limite de requisições da BrasilAPI atingido (aguardando liberação).',
                ];
            }

            return [
                'sucesso'  => false,
                'mensagem' => "BrasilAPI retornou status {$response->status()}.",
            ];
        } catch (\Throwable $e) {
            Log::warning('Falha ao enriquecer empresa via BrasilAPI', [
                'cnpj'  => $cnpjLimpo,
                'erro'  => $e->getMessage(),
            ]);

            return [
                'sucesso'  => false,
                'mensagem' => $e->getMessage(),
            ];
        }
    }

    /**
     * Enriquece um lote de empresas ainda não verificadas.
     * Retorna estatísticas do processamento.
     *
     * @param int $limite Máximo de empresas processadas por chamada.
     */
    public function enriquecerLote(int $limite = 50): array
    {
        $empresas = EmpresasConveniadas::where('localizacao_verificada', false)
            ->orderBy('id')
            ->limit($limite)
            ->get();

        $total      = $empresas->count();
        $sucessos   = 0;
        $falhas     = 0;
        $erros      = [];

        foreach ($empresas as $empresa) {
            $resultado = $this->enriquecerEmpresa($empresa);

            if ($resultado['sucesso']) {
                $sucessos++;
            } else {
                $falhas++;
                $erros[] = "[{$empresa->cnpj}] {$resultado['mensagem']}";
            }

            // Respeita o rate limit da BrasilAPI (~3 req/s)
            usleep(self::DELAY_ENTRE_REQUESTS_MS * 1000);
        }

        $pendentes = EmpresasConveniadas::where('localizacao_verificada', false)->count();

        return [
            'processadas' => $total,
            'sucessos'    => $sucessos,
            'falhas'      => $falhas,
            'pendentes'   => $pendentes,
            'erros'       => array_slice($erros, 0, 10), // limita os erros exibidos
        ];
    }
}
