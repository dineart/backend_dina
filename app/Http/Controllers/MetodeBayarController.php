<?php
namespace App\Http\Controllers;

use App\Models\MetodeBayar;
use Illuminate\Http\Request;

class MetodeBayarController extends Controller
{
    public function index()
    {
        $data = MetodeBayar::all();
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ID_METODE'   => 'required|unique:METODE_BAYAR,ID_METODE|max:20',
            'NAMA_METODE' => 'required|max:50',
            'NO_REKENING' => 'required|max:50',
        ]);

        $data = MetodeBayar::create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Metode bayar berhasil ditambahkan',
            'data'    => $data
        ], 201);
    }

    public function show($id)
    {
        $data = MetodeBayar::find($id);
        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function update(Request $request, $id)
    {
        $data = MetodeBayar::find($id);
        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        $request->validate([
            'NAMA_METODE' => 'sometimes|max:50',
            'NO_REKENING' => 'sometimes|max:50',
        ]);

        $data->update($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Metode bayar berhasil diupdate',
            'data'    => $data
        ]);
    }

    public function destroy($id)
    {
        $data = MetodeBayar::find($id);
        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }
        $data->delete();
        return response()->json(['success' => true, 'message' => 'Metode bayar berhasil dihapus']);
    }
}