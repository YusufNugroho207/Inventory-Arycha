<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'supplier';

    protected $guarded = [];

    public function barang()
    {
        return $this->hasMany(Barang::class, 'supplier_id', 'id');
    }

    public function barangMasuk()
    {
        return $this->hasMany(barangMasuk::class, 'supplier_id', 'id');
    }

    public function barangkeluar()
    {
        return $this->hasMany(barangkeluar::class, 'supplier_id', 'id');
    }
}
