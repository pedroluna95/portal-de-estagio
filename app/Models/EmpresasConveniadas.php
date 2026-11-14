<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmpresasConveniadas extends Model
{
    protected $table = 'empresas_conveniadas';

    protected $fillable = [
        'empresa',
        'cnpj',
        'vig_inicial',
        'vig_final',
        'municipio',
        'uf',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'natureza_juridica',
        'localizacao_verificada',
        'localizacao_verificada_em',
    ];

    protected $casts = [
        'vig_inicial'               => 'date',
        'vig_final'                 => 'date',
        'localizacao_verificada'    => 'boolean',
        'localizacao_verificada_em' => 'datetime',
    ];

    /**
     * Verifica se o convênio está ativo/vigente com base na data final.
     */
    public function isVigente(): bool
    {
        return ! $this->vig_final || $this->vig_final->isFuture() || $this->vig_final->isToday();
    }

    /**
     * Retorna o CNPJ formatado no padrão 00.000.000/0000-00.
     */
    public function getCnpjFormatadoAttribute(): string
    {
        $cnpjLimpo = preg_replace('/\D/', '', (string) $this->cnpj);
        if (strlen($cnpjLimpo) === 14) {
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cnpjLimpo);
        }

        return (string) $this->cnpj;
    }

    /**
     * Scope para filtrar por termo de busca (nome ou CNPJ).
     */
    public function scopeBusca($query, ?string $termo)
    {
        if (! $termo) {
            return $query;
        }

        $termoLimpo = trim($termo);
        $cnpjLimpo = preg_replace('/\D/', '', $termoLimpo);

        return $query->where(function ($q) use ($termoLimpo, $cnpjLimpo) {
            $q->where('empresa', 'like', "%{$termoLimpo}%");
            if (! empty($cnpjLimpo)) {
                $q->orWhere('cnpj', 'like', "%{$cnpjLimpo}%");
            }
            $q->orWhere('municipio', 'like', "%{$termoLimpo}%");
        });
    }

    /**
     * Scope para convênios vigentes.
     */
    public function scopeVigentes($query)
    {
        return $query->whereDate('vig_final', '>=', today()->toDateString());
    }

    /**
     * Scope para convênios expirados/vencidos.
     */
    public function scopeExpirados($query)
    {
        return $query->whereDate('vig_final', '<', today()->toDateString());
    }

    /**
     * Scope para filtrar por UF (estado).
     */
    public function scopePorUf($query, ?string $uf)
    {
        if (! $uf) {
            return $query;
        }

        return $query->where('uf', strtoupper(trim($uf)));
    }

    /**
     * Scope para empresas com localização já verificada via CNPJ.
     */
    public function scopeComLocalizacao($query)
    {
        return $query->where('localizacao_verificada', true);
    }

    /**
     * Scope para empresas ainda sem localização verificada.
     */
    public function scopeSemLocalizacao($query)
    {
        return $query->where('localizacao_verificada', false);
    }

    /**
     * Retorna a localização formatada (Município/UF ou traço).
     */
    public function getLocalizacaoFormatadaAttribute(): string
    {
        if ($this->municipio && $this->uf) {
            return "{$this->municipio} / {$this->uf}";
        }

        if ($this->municipio) {
            return $this->municipio;
        }

        return '';
    }

    /**
     * Retorna a paleta visual (bg e texto) para o card da empresa.
     */
    public function getCorCardAttribute(): array
    {
        $paleta = [
            ['bg' => 'bg-blue-100', 'txt' => 'text-blue-700'],
            ['bg' => 'bg-green-100', 'txt' => 'text-green-700'],
            ['bg' => 'bg-purple-100', 'txt' => 'text-purple-700'],
            ['bg' => 'bg-orange-100', 'txt' => 'text-orange-700'],
            ['bg' => 'bg-indigo-100', 'txt' => 'text-indigo-700'],
            ['bg' => 'bg-teal-100', 'txt' => 'text-teal-700'],
        ];

        return $paleta[$this->id % count($paleta)];
    }
}
