<?php
namespace App\Http\Controllers;
use App\Models\Tagihan;
use Illuminate\Http\Request;

class TagihanController extends Controller
{
    public function index()
    {
        $data = Tagihan::with('keuanganMahasiswa')->get();
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ID_TAGIHAN'       => 'required|unique:TAGIHAN,ID_TAGIHAN|max:20',
            'ID_KEUANGAN_MHS'  => 'required|exists:KEUANGAN_MAHASISWA,ID_KEUANGAN_MHS',
            'NO_INVOICE'       => 'nullable|max:50',
            'NAMA_TAGIHAN'     => 'nullable|max:100',
            'NOMOR_CICILAN'    => 'nullable|integer',
            'TOTAL_CICILAN'    => 'nullable|integer',
            'NOMINAL_CICILAN'  => 'nullable|numeric',
            'DENDA'            => 'nullable|numeric',
            'POTONGAN'         => 'nullable|numeric',
            'TOTAL_TAGIHAN'    => 'required|numeric',
            'TGL_JATUH_TEMPO'  => 'nullable|date',
            'TGL_TAGIHAN'      => 'nullable|date',
            'STATUS_BAYAR'     => 'required|max:25',
        ]);

        $data = Tagihan::create($request->all());
        return response()->json(['success' => true, 'message' => 'Tagihan berhasil dibuat', 'data' => $data], 201);
    }

    public function show($id)
    {
        $data = Tagihan::with('keuanganMahasiswa', 'transaksiPembayaran')->find($id);
        if (!$data) return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function update(Request $request, $id)
    {
        $data = Tagihan::find($id);
        if (!$data) return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);

        $request->validate([
            'NO_INVOICE'      => 'sometimes|max:50',
            'NAMA_TAGIHAN'    => 'sometimes|max:100',
            'NOMOR_CICILAN'   => 'sometimes|integer',
            'TOTAL_CICILAN'   => 'sometimes|integer',
            'NOMINAL_CICILAN' => 'sometimes|numeric',
            'DENDA'           => 'sometimes|numeric',
            'POTONGAN'        => 'sometimes|numeric',
            'TOTAL_TAGIHAN'   => 'sometimes|numeric',
            'TGL_JATUH_TEMPO' => 'sometimes|date',
            'TGL_TAGIHAN'     => 'sometimes|date',
            'STATUS_BAYAR'    => 'sometimes|max:25',
        ]);

        $data->update($request->all());
        return response()->json(['success' => true, 'message' => 'Tagihan berhasil diupdate', 'data' => $data]);
    }

    public function destroy($id)
    {
        $data = Tagihan::find($id);
        if (!$data) return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        $data->delete();
        return response()->json(['success' => true, 'message' => 'Tagihan berhasil dihapus']);
    }
}