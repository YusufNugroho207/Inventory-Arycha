<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangMasuk extends Model
{
    use HasFactory;

    protected $table = 'barang_masuk';

    protected $guarded = [];

    protected static function booted(): void
    {
        static::deleted(function ($barangMasuk) {
            $barang = $barangMasuk->barang;

            if ($barang) {
                $barang->decrement('stok', $barangMasuk->jumlah);

                if ($barang->stok < 0) {
                    $barang->stok = 0;
                    $barang->save();
                }
            }
        });
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }
}
