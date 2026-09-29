<?php

namespace App\Models;

class Orientador extends Usuario
{
    protected $table = 'orientadores';
    protected $primaryKey = 'orientador_ID';
    public $timestamps = true;
    protected $fillable = ['user_ID', 'siape', 'area'];
}
