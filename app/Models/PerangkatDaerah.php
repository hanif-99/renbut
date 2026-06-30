<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PerangkatDaerah extends Model
{
    use HasFactory;

    protected $table = 'perangkat_daerah';
    protected $fillable = ['kode', 'nama'];

    public function unitOrganisasi(): HasMany
    {
        return $this->hasMany(UnitOrganisasi::class);
    }
}