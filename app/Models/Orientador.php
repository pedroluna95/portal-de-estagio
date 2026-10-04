<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Orientador extends Usuario
{
    protected $table = 'orientadores';
    protected $primaryKey = 'orientador_ID';
    public $timestamps = true;
    protected $fillable = ['user_ID', 'siape', 'area'];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'user_ID', 'user_ID');
    }

    public function estagios(): HasMany
    {
        return $this->hasMany(Estagio::class, 'orientador_id', 'orientador_ID');
    }
}
