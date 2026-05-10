<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    protected $table = 'TAGIHAN';
    protected $primaryKey = 'ID_TAGIHAN';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'ID_TAGIHAN',
        'ID_KEUANGAN_MHS',
        'NO_INVOICE',
        'NAMA_TAGIHAN',
        'NOMOR_CICILAN',
        'TOTAL_CICILAN',
        'NOMINAL_CICILAN',
        'DENDA',
        'POTONGAN',
        'TOTAL_TAGIHAN',
        'TGL_JATUH_TEMPO',
        'TGL_TAGIHAN',
        'STATUS_BAYAR',
    ];

    public function keuanganMahasiswa()
    {
        return $this->belongsTo(KeuanganMahasiswa::class, 'ID_KEUANGAN_MHS', 'ID_KEUANGAN_MHS');
    }

    public function transaksiPembayaran()
    {
        return $this->hasMany(TransaksiPembayaran::class, 'ID_TAGIHAN', 'ID_TAGIHAN');
    }
}