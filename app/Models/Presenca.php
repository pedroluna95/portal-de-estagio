<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presenca extends Model
{
    public const STATUS_PENDENTE = 'pendente';
    public const STATUS_APROVADA = 'aprovada';
    public const STATUS_RECUSADA = 'recusada';
    public const STATUS_PESSOAL = 'pessoal';
    public const MODO_OFICIAL = 'oficial';
    public const MODO_PESSOAL = 'pessoal';

    protected $table = 'presencas';
    protected $fillable = [
        'estagio_id',
        'data',
        'entrada',
        'saida',
        'modo',
        'status',
        'observacao',
        'parecer_orientador',
        'parecer_supervisor',
    ];

    protected function casts(): array
    {
        return ['data' => 'date'];
    }

    public function estagio(): BelongsTo
    {
        return $this->belongsTo(Estagio::class, 'estagio_id');
    }

    public function getMinutosAttribute(): int
    {
        $entrada = $this->getAttributes()['entrada'] ?? null;
        $saida = $this->getAttributes()['saida'] ?? null;
        if (! $entrada || ! $saida) {
            return 0;
        }

        [$horaEntrada, $minutoEntrada] = array_map('intval', array_slice(explode(':', $entrada), 0, 2));
        [$horaSaida, $minutoSaida] = array_map('intval', array_slice(explode(':', $saida), 0, 2));

        return max(0, ($horaSaida * 60 + $minutoSaida) - ($horaEntrada * 60 + $minutoEntrada));
    }

    public function getHorasAttribute(): float
    {
        return round($this->minutos / 60, 2);
    }
}