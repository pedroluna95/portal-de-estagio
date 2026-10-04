<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JornadaEstagio extends Model
{
    protected $table = 'jornadas_estagio';
    protected $fillable = [
        'estagio_id',
        'horas_diarias',
        'segunda',
        'terca',
        'quarta',
        'quinta',
        'sexta',
        'sabado',
        'domingo',
    ];

    protected function casts(): array
    {
        return [
            'horas_diarias' => 'float',
            'segunda' => 'boolean',
            'terca' => 'boolean',
            'quarta' => 'boolean',
            'quinta' => 'boolean',
            'sexta' => 'boolean',
            'sabado' => 'boolean',
            'domingo' => 'boolean',
        ];
    }

    public function estagio(): BelongsTo
    {
        return $this->belongsTo(Estagio::class, 'estagio_id');
    }

    public function cargaSemanal(): float
    {
        $diasSelecionados = collect([
            $this->segunda,
            $this->terca,
            $this->quarta,
            $this->quinta,
            $this->sexta,
            $this->sabado,
            $this->domingo,
        ])->filter()->count();

        return round((float) $this->horas_diarias * $diasSelecionados, 2);
    }

    public function trabalhaNoDia(int $diaIso): bool
    {
        $dia = [
            1 => 'segunda',
            2 => 'terca',
            3 => 'quarta',
            4 => 'quinta',
            5 => 'sexta',
            6 => 'sabado',
            7 => 'domingo',
        ][$diaIso] ?? null;

        return $dia !== null && (bool) $this->{$dia};
    }
}