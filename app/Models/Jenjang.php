<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jenjang extends Model
{
    use HasFactory;

    protected $table = 'jenjang';
    protected $fillable = ['nama', 'kode'];

    public function jabatan(): HasMany
    {
        return $this->hasMany(Jabatan::class);
    }
}