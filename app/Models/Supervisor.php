<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supervisor extends Usuario
{
    protected $table = 'supervisores';
    protected $primaryKey = 'supervisor_ID';
    public $timestamps = true;
    protected $fillable = ['user_ID', 'cargo', 'empresa'];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'user_ID', 'user_ID');
    }

    public function estagios(): HasMany
    {
        return $this->hasMany(Estagio::class, 'supervisor_id', 'supervisor_ID');
    }
}
