<?php

namespace App\Models;

class Supervisor extends Usuario
{
    protected $table = 'supervisores';
    protected $primaryKey = 'supervisor_ID';
    public $timestamps = true;
    protected $fillable = ['user_ID', 'cargo', 'empresa'];
}
