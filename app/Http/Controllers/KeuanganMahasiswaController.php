<?php
namespace App\Http\Controllers;

use App\Models\KeuanganMahasiswa;
use Illuminate\Http\Request;

class KeuanganMahasiswaController extends Controller
{
    public function index()
    {
        $data = KeuanganMahasiswa::with('kategoriUkt', 'beasiswa')->get();
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ID_KEUANGAN_MHS' => 'required|unique:KEUANGAN_MAHASISWA,ID_KEUANGAN_MHS|max:20',
            'ID_KATEGORI'     => 'required|exists:KATEGORI_UKT,ID_KATEGORI',
            'ID_MAHASISWA'    => 'required|max:25',
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
        $data = KeuanganMahasiswa::with('kategoriUkt', 'beasiswa', 'tagihan')->find($id);
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
            'ID_MAHASISWA' => 'sometimes|max:25',
            'SEMESTER'     => 'sometimes|max:15',
            'BEASISWA'     => 'sometimes|max:20',
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

    public function statusAktif($id_mahasiswa)
    {
        $data = KeuanganMahasiswa::with('kategoriUkt', 'beasiswa')
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
                'KATEGORI_UKT' => $data->kategoriUkt->GOLONGAN_UKT ?? null,
                'NOMINAL_UKT'  => $data->kategoriUkt->NOMINAL_UKT ?? null,
                'BEASISWA'     => $data->beasiswa->NAMA_BEASISWA ?? null,
            ]
        ]);
    }
}