<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TransaksiPembayaran extends Model
{
    protected $table = 'TRANSAKSI_PEMBAYARAN';
    protected $primaryKey = 'ID_TRANSAKSI';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'ID_TRANSAKSI',
        'ID_TAGIHAN',
        'ID_METODE',
        'NO_VIRTUAL_ACCOUNT',
        'JUMLAH_TAGIHAN',
        'BIAYA_ADMIN',
        'TOTAL_BAYAR',
        'TGL_TRANSAKSI',
        'TGL_KADALUWARSA',
        'STATUS_TRANSAKSI',
    ];

    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class, 'ID_TAGIHAN', 'ID_TAGIHAN');
    }

    public function metodeBayar()
    {
        return $this->belongsTo(MetodeBayar::class, 'ID_METODE', 'ID_METODE');
    }
}