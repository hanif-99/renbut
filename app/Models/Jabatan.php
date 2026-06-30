<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jabatan extends Model
{
    use HasFactory;

    protected $table = 'jabatan';
    protected $fillable = [
        'kode', 
        'nama', 
        'unit_organisasi_id', 
        'jenis_jabatan_id', 
        'jenjang_id', 
        'kj', 
        'b', 
        'k'
    ];

    protected $casts = [
        'b' => 'integer',
        'k' => 'integer',
    ];

    public function unitOrganisasi(): BelongsTo
    {
        return $this->belongsTo(UnitOrganisasi::class);
    }

    public function jenisJabatan(): BelongsTo
    {
        return $this->belongsTo(JenisJabatan::class);
    }

    public function jenjang(): BelongsTo
    {
        return $this->belongsTo(Jenjang::class);
    }

    public function formasiAsn(): HasMany
    {
        return $this->hasMany(FormasiAsn::class);
    }

    public function sisaKebutuhan2032(): HasMany
    {
        return $this->hasMany(SisaKebutuhan2032::class);
    }

    public function getGapAttribute()
    {
        return $this->b - $this->k;
    }
}