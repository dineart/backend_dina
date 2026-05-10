<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class KategoriUkt extends Model
{
    protected $table = 'KATEGORI_UKT';
    protected $primaryKey = 'ID_KATEGORI';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'ID_KATEGORI',
        'ID_PRODI',
        'JENJANG',
        'GOLONGAN_UKT',
        'NOMINAL_UKT',
    ];

    public function keuanganMahasiswa()
    {
        return $this->hasMany(KeuanganMahasiswa::class, 'ID_KATEGORI', 'ID_KATEGORI');
    }
}