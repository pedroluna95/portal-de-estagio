<?php

namespace App\Models;

class Contratante extends Usuario
{
    protected $table = 'contratantes';
    protected $primaryKey = 'contratante_ID';
    public $timestamps = true;
    protected $fillable = ['user_ID', 'empresa', 'cargo'];
}
