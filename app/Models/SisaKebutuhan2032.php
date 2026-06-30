<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SisaKebutuhan2032 extends Model
{
    use HasFactory;

    protected $table = 'sisa_kebutuhan_2032';
    protected $fillable = [
        'jabatan_id',
        'jpt',
        'adm_pengawas',
        'mutasi',
        'cpns',
        'pppk'
    ];

    protected $casts = [
        'jpt' => 'integer',
        'adm_pengawas' => 'integer',
        'mutasi' => 'integer',
        'cpns' => 'integer',
        'pppk' => 'integer',
    ];

    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function getTotalAttribute()
    {
        return $this->jpt + $this->adm_pengawas + $this->mutasi + $this->cpns + $this->pppk;
    }
}