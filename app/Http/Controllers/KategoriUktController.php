<?php
namespace App\Http\Controllers;
use App\Models\KategoriUkt;
use Illuminate\Http\Request;

class KategoriUktController extends Controller
{
    public function index()
    {
        return response()->json(['success' => true, 'data' => KategoriUkt::all()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ID_KATEGORI'  => 'required|unique:KATEGORI_UKT,ID_KATEGORI|max:20',
            'ID_PRODI'     => 'required|max:20',
            'JENJANG'      => 'required|max:20',
            'GOLONGAN_UKT' => 'required|max:15',
            'NOMINAL_UKT'  => 'required|numeric',
        ]);

        $data = KategoriUkt::create($request->all());
        return response()->json(['success' => true, 'message' => 'Kategori UKT berhasil ditambahkan', 'data' => $data], 201);
    }

    public function show($id)
    {
        $data = KategoriUkt::find($id);
        if (!$data) return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function update(Request $request, $id)
    {
        $data = KategoriUkt::find($id);
        if (!$data) return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);

        $request->validate([
            'ID_PRODI'     => 'sometimes|max:20',
            'JENJANG'      => 'sometimes|max:20',
            'GOLONGAN_UKT' => 'sometimes|max:15',
            'NOMINAL_UKT'  => 'sometimes|numeric',
        ]);

        $data->update($request->all());
        return response()->json(['success' => true, 'message' => 'Kategori UKT berhasil diupdate', 'data' => $data]);
    }

    public function destroy($id)
    {
        $data = KategoriUkt::find($id);
        if (!$data) return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        $data->delete();
        return response()->json(['success' => true, 'message' => 'Kategori UKT berhasil dihapus']);
    }
}