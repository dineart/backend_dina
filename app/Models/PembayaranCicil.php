<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PembayaranCicil extends Model
{
    protected $table = 'pembayaran_cicil';
    protected $primaryKey = 'id_cicilan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_cicilan',
        'id_tagihan',
        'id_metode',
        'tgl_bayar',
        'jumlah_bayar',
    ];

    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class, 'id_tagihan', 'id_tagihan');
    }

    public function metodeBayar()
    {
        return $this->belongsTo(MetodeBayar::class, 'id_metode', 'id_metode');
    }
}
