<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangKeluar extends Model
{
    use HasFactory;

    protected $table = 'barang_keluar';

    protected $guarded = [];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    protected static function booted(): void
    {
        static::updating(function ($model) {
            if ($model->getOriginal('status') !== 'selesai' && $model->status === 'selesai') {
                $barang = \App\Models\Barang::find($model->barang_id);
                if ($barang) {
                    $barang->stok += $model->jumlah;
                    $barang->save();
                }
            }
        });
    }
}
