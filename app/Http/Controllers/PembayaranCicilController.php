<?php
namespace App\Http\Controllers;

use App\Models\PembayaranCicil;
use Illuminate\Http\Request;

class PembayaranCicilController extends Controller
{
    public function index()
    {
        $data = PembayaranCicil::with('tagihan', 'metodeBayar')->get();
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ID_CICILAN'  => 'required|unique:PEMBAYARAN_CICIL,ID_CICILAN|max:20',
            'ID_TAGIHAN'  => 'required|exists:TAGIHAN,ID_TAGIHAN',
            'ID_METODE'   => 'required|exists:METODE_BAYAR,ID_METODE',
            'TGL_BAYAR'   => 'required|date',
            'JUMLAH_BAYAR'=> 'required|numeric',
        ]);

        $data = PembayaranCicil::create($request->all());

        // Update status tagihan jika lunas
        $tagihan = \App\Models\Tagihan::find($request->ID_TAGIHAN);
        $totalBayar = PembayaranCicil::where('ID_TAGIHAN', $request->ID_TAGIHAN)
                        ->sum('JUMLAH_BAYAR');

        if ($totalBayar >= $tagihan->TOTAL_TAGIHAN) {
            $tagihan->update(['STATUS_BAYAR' => 'LUNAS']);

            // Update status aktif mahasiswa
            $keuangan = \App\Models\KeuanganMahasiswa::find($tagihan->ID_KEUANGAN_MHS);
            $keuangan->update(['STATUS_AKTIF' => 'AKTIF']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil dicatat',
            'data'    => $data
        ], 201);
    }

    public function show($id)
    {
        $data = PembayaranCicil::with('tagihan', 'metodeBayar')->find($id);
        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function update(Request $request, $id)
    {
        $data = PembayaranCicil::find($id);
        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        $request->validate([
            'ID_METODE'    => 'sometimes|exists:METODE_BAYAR,ID_METODE',
            'TGL_BAYAR'    => 'sometimes|date',
            'JUMLAH_BAYAR' => 'sometimes|numeric',
        ]);

        $data->update($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil diupdate',
            'data'    => $data
        ]);
    }

    public function destroy($id)
    {
        $data = PembayaranCicil::find($id);
        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }
        $data->delete();
        return response()->json(['success' => true, 'message' => 'Pembayaran berhasil dihapus']);
    }
}