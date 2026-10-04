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
}