<?php
namespace App\Http\Controllers;

use App\Models\Beasiswa;
use Illuminate\Http\Request;

class BeasiswaController extends Controller
{
    public function index()
    {
        $data = Beasiswa::all();
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ID_BEASISWA'     => 'required|unique:BEASISWA,ID_BEASISWA|max:20',
            'NAMA_BEASISWA'   => 'required|max:100',
            'PERSEN_POTONGAN' => 'required|numeric',
        ]);

        $data = Beasiswa::create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Beasiswa berhasil ditambahkan',
            'data'    => $data
        ], 201);
    }

    public function show($id)
    {
        $data = Beasiswa::find($id);
        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function update(Request $request, $id)
    {
        $data = Beasiswa::find($id);
        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        $request->validate([
            'NAMA_BEASISWA'   => 'sometimes|max:100',
            'PERSEN_POTONGAN' => 'sometimes|numeric',
        ]);

        $data->update($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Beasiswa berhasil diupdate',
            'data'    => $data
        ]);
    }

    public function destroy($id)
    {
        $data = Beasiswa::find($id);
        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }
        $data->delete();
        return response()->json(['success' => true, 'message' => 'Beasiswa berhasil dihapus']);
    }
}