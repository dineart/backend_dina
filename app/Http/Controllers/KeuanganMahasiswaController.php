<?php
namespace App\Http\Controllers;

use App\Models\KeuanganMahasiswa;
use Illuminate\Http\Request;

class KeuanganMahasiswaController extends Controller
{
    public function index()
    {
        $data = KeuanganMahasiswa::with('kategoriUkt')->get();
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ID_KEUANGAN_MHS' => 'required|unique:KEUANGAN_MAHASISWA,ID_KEUANGAN_MHS|max:20',
            'ID_KATEGORI'     => 'required|exists:KATEGORI_UKT,ID_KATEGORI',
            'ID_MAHASISWA'    => 'required|max:36',
            'SEMESTER'        => 'required|max:15',
            'BEASISWA'        => 'nullable|max:20',
            'STATUS_AKTIF'    => 'required|max:20',
        ]);

        $data = KeuanganMahasiswa::create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Data keuangan mahasiswa berhasil ditambahkan',
            'data'    => $data
        ], 201);
    }

    public function show($id)
    {
        $data = KeuanganMahasiswa::with('kategoriUkt', 'tagihan')->find($id);
        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function update(Request $request, $id)
    {
        $data = KeuanganMahasiswa::find($id);
        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        $request->validate([
            'ID_KATEGORI'  => 'sometimes|exists:KATEGORI_UKT,ID_KATEGORI',
            'ID_MAHASISWA' => 'sometimes|max:20',
            'SEMESTER'     => 'sometimes|max:15',
            'BEASISWA'     => 'nullable|max:20',
            'STATUS_AKTIF' => 'sometimes|max:20',
        ]);

        $data->update($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Data keuangan mahasiswa berhasil diupdate',
            'data'    => $data
        ]);
    }

    public function destroy($id)
    {
        $data = KeuanganMahasiswa::find($id);
        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }
        $data->delete();
        return response()->json(['success' => true, 'message' => 'Data keuangan mahasiswa berhasil dihapus']);
    }

    public function testGolongan(Request $request)
    {
        $hasil = $this->tentukanGolongan(
            $request->penghasilan,
            $request->pekerjaan
        );

        return response()->json([
            'success' => true,
            'data' => $hasil
        ]);
    }

    public function statusAktif($id_mahasiswa)
    {
        $data = KeuanganMahasiswa::with('kategoriUkt')
                ->where('ID_MAHASISWA', $id_mahasiswa)
                ->first();

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'ID_MAHASISWA' => $data->ID_MAHASISWA,
                'STATUS_AKTIF' => $data->STATUS_AKTIF,
                'SEMESTER'     => $data->SEMESTER,
                'BEASISWA'     => $data->BEASISWA,
                'KATEGORI_UKT' => $data->kategoriUkt->GOLONGAN_UKT ?? null,
                'NOMINAL_UKT'  => $data->kategoriUkt->NOMINAL_UKT ?? null,
            ]
        ]);
    }

    private function tentukanGolongan($penghasilan, $pekerjaan)
    {
    \Log::info('DEBUG UKT', [
        'penghasilan' => $penghasilan,
        'pekerjaan_awal' => $pekerjaan
    ]);

    $pekerjaan = strtolower(trim((string)$pekerjaan));

    \Log::info('DEBUG UKT SETELAH LOWERCASE', [
        'pekerjaan' => $pekerjaan
    ]);

    if ($penghasilan == 'Lebih dari 2.000.000 - 5.000.000') {

        \Log::info('MASUK BLOK 2-5 JUTA');

        if (
            $pekerjaan == 'ibu rumah tangga' ||
            $pekerjaan == 'tidak bekerja'
        ) {
            \Log::info('GOLONGAN 3');
            return [
                'golongan' => 'Golongan 3',
                'beasiswa' => null,
            ];
        }

        if ($pekerjaan == 'pns') {
            \Log::info('GOLONGAN 5');
            return [
                'golongan' => 'Golongan 5',
                'beasiswa' => null,
            ];
        }

        \Log::info('GOLONGAN 4');
        return [
            'golongan' => 'Golongan 4',
            'beasiswa' => null,
        ];
    }

    return [
        'golongan' => 'Golongan 5',
        'beasiswa' => null,
    ];
}

}