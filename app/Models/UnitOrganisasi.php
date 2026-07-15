<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnitOrganisasi extends Model
{
    use HasFactory;

    protected $table = 'unit_organisasi';
    protected $fillable = ['kode', 'nama', 'perangkat_daerah_id', 'unor_atasan'];

    public function perangkatDaerah(): BelongsTo
    {
        return $this->belongsTo(PerangkatDaerah::class);
    }

    public function jabatan(): HasMany
    {
        return $this->hasMany(Jabatan::class);
    }

    /**
     * BARU: Relasi ke unit induk (parent)
     */
    public function parentUnit(): BelongsTo
    {
        return $this->belongsTo(UnitOrganisasi::class, 'unor_atasan', 'id');
    }

    /**
     * BARU: Relasi ke unit anak (children)
     */
    public function childUnits(): HasMany
    {
        return $this->hasMany(UnitOrganisasi::class, 'unor_atasan', 'id');
    }

    /**
     * BARU: Helper untuk hitung level berdasarkan kode (dot notation)
     */
    public function getCodeLevel(): int
    {
        if (!$this->kode) return 0;
        return count(array_filter(explode('.', trim($this->kode))));
    }
}