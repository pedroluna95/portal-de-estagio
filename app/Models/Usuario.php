<?php

namespace App\Models;

use App\Services\UsuarioService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Usuario extends Model
{
    protected $table = 'usuarios';
    protected $primaryKey = 'user_ID';
    public $timestamps = false;
    protected $fillable = ['Nome', 'Email', 'Senha', 'cpf', 'atribuicao_id'];
    protected $appends = ['tipo', 'atribuicao', 'matricula', 'siape'];

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
            ->where('user_ID', $this->user_ID)
            ->value('matricula');
    }

    public function getSiapeAttribute(): ?string
    {
        return DB::table('orientadores')
            ->where('user_ID', $this->user_ID)
            ->value('siape');
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
