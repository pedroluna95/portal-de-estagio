<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Estagio extends Model
{
    public const STATUS_PENDENTE = 'pendente';
    public const STATUS_ATIVO = 'ativo';
    public const STATUS_FINALIZADO = 'finalizado';
    public const STATUS_CANCELADO = 'cancelado';

    protected $table = 'estagios';
    protected $fillable = [
        'aluno_id',
        'orientador_id',
        'supervisor_id',
        'local',
        'data_inicio',
        'data_fim',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'data_inicio' => 'date',
            'data_fim' => 'date',
        ];
    }

    public function aluno(): BelongsTo
    {
        return $this->belongsTo(Aluno::class, 'aluno_id', 'user_id');
    }

    public function orientador(): BelongsTo
    {
        return $this->belongsTo(Orientador::class, 'orientador_id', 'orientador_ID');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(Supervisor::class, 'supervisor_id', 'supervisor_ID');
    }

    public function jornada(): HasOne
    {
        return $this->hasOne(JornadaEstagio::class, 'estagio_id');
    }

    public function presencas(): HasMany
    {
        return $this->hasMany(Presenca::class, 'estagio_id');
    }

    /**
     * Retorna o status efetivo considerando a data de término.
     */
    public function getStatusEfetivoAttribute(): string
    {
        if ($this->status === self::STATUS_ATIVO && $this->data_fim && $this->data_fim->isPast() && ! $this->data_fim->isToday()) {
            return self::STATUS_FINALIZADO;
        }

        return (string) $this->status;
    }

    /**
     * Retorna as classes Tailwind para o badge de status.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status_efetivo) {
            self::STATUS_ATIVO => 'bg-green-100 text-green-700',
            self::STATUS_FINALIZADO => 'bg-gray-100 text-gray-700',
            self::STATUS_CANCELADO => 'bg-red-100 text-red-700',
            default => 'bg-amber-100 text-amber-700',
        };
    }
}