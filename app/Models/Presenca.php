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
        'horas',
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
        return [
            'data' => 'date',
            'horas' => 'float',
        ];
    }

    public function estagio(): BelongsTo
    {
        return $this->belongsTo(Estagio::class, 'estagio_id');
    }

    public function getMinutosAttribute(): int
    {
        $horas = $this->getAttributes()['horas'] ?? null;
        if ($horas !== null) {
            return (int) round((float) $horas * 60);
        }

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
        $horas = $this->getAttributes()['horas'] ?? null;
        if ($horas !== null) {
            return (float) $horas;
        }

        return round($this->minutos / 60, 2);
    }

    /**
     * Retorna a cor de texto para o status/modo da presença.
     */
    public function getCorStatusAttribute(): string
    {
        if ($this->modo === self::MODO_PESSOAL) {
            return 'text-blue-700';
        }

        return match ($this->status) {
            self::STATUS_APROVADA => 'text-green-700',
            self::STATUS_RECUSADA => 'text-red-700',
            default => 'text-amber-700',
        };
    }

    /**
     * Retorna as classes Tailwind para o badge do registro de presença.
     */
    public function getBadgeClassAttribute(): string
    {
        if ($this->modo === self::MODO_PESSOAL) {
            return 'bg-blue-100 text-blue-700';
        }

        return match ($this->status) {
            self::STATUS_APROVADA => 'bg-green-100 text-green-700',
            self::STATUS_RECUSADA => 'bg-red-100 text-red-700',
            default => 'bg-amber-100 text-amber-700',
        };
    }
}