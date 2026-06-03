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
    // Buang data tambahan dari middleware
    $payload = $request->except('auth_user');

    // Cek apakah request berupa array data
    if (array_is_list($payload)) {

        $inserted = [];

        foreach ($payload as $item) {

            validator($item, [
                'ID_KATEGORI'  => 'required|unique:KATEGORI_UKT,ID_KATEGORI|max:20',
                'ID_PRODI'     => 'required|max:20',
                'JENJANG'      => 'required|max:20',
                'GOLONGAN_UKT' => 'required|max:15',
                'NOMINAL_UKT'  => 'required|numeric',
            ])->validate();

            $inserted[] = KategoriUkt::create($item);
        }

        return response()->json([
            'success' => true,
            'message' => count($inserted) . ' data kategori UKT berhasil ditambahkan',
            'data'    => $inserted
        ], 201);
    }

    // Single insert
    $request->validate([
        'ID_KATEGORI'  => 'required|unique:KATEGORI_UKT,ID_KATEGORI|max:20',
        'ID_PRODI'     => 'required|max:20',
        'JENJANG'      => 'required|max:20',
        'GOLONGAN_UKT' => 'required|max:15',
        'NOMINAL_UKT'  => 'required|numeric',
    ]);

    $data = KategoriUkt::create($payload);

    return response()->json([
        'success' => true,
        'message' => 'Kategori UKT berhasil ditambahkan',
        'data'    => $data
    ], 201);
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

    public function storeMany(Request $request)
{
    $data = $request->all();

    // Validasi setiap item
    foreach ($data as $item) {
        validator($item, [
            'ID_KATEGORI'  => 'required|max:20',
            'ID_PRODI'     => 'required|max:20',
            'JENJANG'      => 'required|max:20',
            'GOLONGAN_UKT' => 'required|max:15',
            'NOMINAL_UKT'  => 'required|numeric',
        ])->validate();
    }

    $inserted = [];
    foreach ($data as $item) {
        $inserted[] = KategoriUkt::updateOrCreate(
            ['ID_KATEGORI' => $item['ID_KATEGORI']],
            $item
        );
    }

    return response()->json([
        'success' => true,
        'message' => count($inserted) . ' data kategori UKT berhasil ditambahkan',
        'data'    => $inserted
    ], 201);
}
}