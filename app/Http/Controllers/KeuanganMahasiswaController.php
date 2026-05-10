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
            'ID_BEASISWA'     => 'nullable|exists:BEASISWA,ID_BEASISWA',
            'ID_MAHASISWA'    => 'required|max:25',
            'STATUS_AKTIF'    => 'required|max:15',
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
            'ID_BEASISWA'  => 'nullable|exists:BEASISWA,ID_BEASISWA',
            'ID_MAHASISWA' => 'sometimes|max:25',
            'STATUS_AKTIF' => 'sometimes|max:15',
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
}