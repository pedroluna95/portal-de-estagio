<?php

namespace App\Models;

use App\Services\UsuarioService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

class Usuario extends Model
{
    protected $table = 'usuarios';
    protected $primaryKey = 'user_ID';
    public $timestamps = false;
    protected $fillable = ['Nome', 'Email', 'Senha', 'cpf', 'atribuicao_id', 'ativo'];
    protected $casts = [
        'ativo' => 'boolean',
    ];
    protected $appends = ['tipo', 'atribuicao', 'matricula', 'siape', 'curso', 'status_texto', 'ativo_badge_class'];

    public function aluno(): HasOne
    {
        return $this->hasOne(Aluno::class, 'user_id', 'user_ID');
    }

    public function orientador(): HasOne
    {
        return $this->hasOne(Orientador::class, 'user_ID', 'user_ID');
    }

    public function supervisor(): HasOne
    {
        return $this->hasOne(Supervisor::class, 'user_ID', 'user_ID');
    }

    public function getTipoAttribute(): string
    {
        $tipo = DB::table('atribuicoes')
            ->where('Atribuicao_ID', $this->atribuicao_id)
            ->value('Nome_atribuicao');

        return strtolower((string) ($tipo ?? 'aluno'));
    }

    public function getAtribuicaoAttribute(): string
    {
        return $this->tipo;
    }

    public function getMatriculaAttribute(): ?string
    {
        return DB::table('alunos')
            ->where('user_id', $this->user_ID)
            ->value('matricula');
    }

    public function getCursoAttribute(): ?string
    {
        return DB::table('alunos')
            ->where('user_id', $this->user_ID)
            ->value('curso');
    }

    public function getSiapeAttribute(): ?string
    {
        return DB::table('orientadores')
            ->where('user_ID', $this->user_ID)
            ->value('siape');
    }

    public function getStatusTextoAttribute(): string
    {
        return ($this->ativo ?? true) ? 'Ativo' : 'Inativo';
    }

    public function getAtivoBadgeClassAttribute(): string
    {
        return ($this->ativo ?? true)
            ? 'bg-green-100 text-green-700 dark:bg-green-950 dark:text-green-300'
            : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400';
    }

    public function setCpfAttribute($value): void
    {
        if ($value === null || $value === '') {
            $this->attributes['cpf'] = null;
            return;
        }

        $formatted = UsuarioService::formatCpf((string) $value);
        $this->attributes['cpf'] = $formatted ?? (string) $value;
    }
}
