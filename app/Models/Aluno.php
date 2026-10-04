<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Aluno extends Usuario
{
    protected $table = 'alunos';
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    public $timestamps = true;
    protected $fillable = ['user_id', 'nome', 'matricula', 'curso', 'orientador'];

    public function estagios(): HasMany
    {
        return $this->hasMany(Estagio::class, 'aluno_id', 'user_id');
    }

    public function getMatriculaAttribute(): ?string
    {
        return $this->getRawOriginal('matricula');
    }

    public function getCursoAttribute(): ?string
    {
        return $this->getRawOriginal('curso');
    }
}
