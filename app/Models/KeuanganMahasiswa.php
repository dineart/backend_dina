<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class KeuanganMahasiswa extends Model
{
    protected $table = 'KEUANGAN_MAHASISWA';
    protected $primaryKey = 'ID_KEUANGAN_MHS';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'ID_KEUANGAN_MHS',
        'ID_KATEGORI',
        'ID_BEASISWA',
        'ID_MAHASISWA',
        'SEMESTER',
        'BEASISWA',
        'STATUS_AKTIF',
    ];

    public function kategoriUkt()
    {
        return $this->belongsTo(KategoriUkt::class, 'ID_KATEGORI', 'ID_KATEGORI');
    }

    public function tagihan()
    {
        return $this->hasMany(Tagihan::class, 'ID_KEUANGAN_MHS', 'ID_KEUANGAN_MHS');
    }
}