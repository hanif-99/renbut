<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormasiAsn extends Model
{
    use HasFactory;

    protected $table = 'formasi_asn';
    protected $fillable = [
        'jabatan_id',
        'tahun',
        'jpt',
        'adm_pengawas',
        'mutasi',
        'cpns',
        'pppk'
    ];

    protected $casts = [
        'tahun' => 'integer',
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