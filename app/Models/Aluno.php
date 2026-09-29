<?php

namespace App\Models;

class Aluno extends Usuario
{
    protected $table = 'alunos';
    protected $primaryKey = 'aluno_ID';
    public $timestamps = true;
    protected $fillable = ['user_ID', 'matricula', 'curso'];
}
