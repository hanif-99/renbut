<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisJabatan extends Model
{
    use HasFactory;

    protected $table = 'jenis_jabatan';
    protected $fillable = ['nama'];

    public function jabatan(): HasMany
    {
        return $this->hasMany(Jabatan::class);
    }
}