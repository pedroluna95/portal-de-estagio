<?php

namespace App\Models;

class Administrador extends Usuario
{
    protected $fillable = ['Nome', 'Email', 'Senha', 'cpf', 'atribuicao_id'];
}
