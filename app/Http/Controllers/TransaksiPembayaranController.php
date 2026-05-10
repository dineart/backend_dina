<?php
namespace App\Http\Controllers;
use App\Models\TransaksiPembayaran;
use App\Models\Tagihan;
use App\Models\KeuanganMahasiswa;
use Illuminate\Http\Request;

class TransaksiPembayaranController extends Controller
{
    public function index()
    {
        $data = TransaksiPembayaran::with('tagihan', 'metodeBayar')->get();
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ID_TRANSAKSI'      => 'required|unique:TRANSAKSI_PEMBAYARAN,ID_TRANSAKSI|max:20',
            'ID_TAGIHAN'        => 'required|exists:TAGIHAN,ID_TAGIHAN',
            'ID_METODE'         => 'required|exists:METODE_BAYAR,ID_METODE',
            'NO_VIRTUAL_ACCOUNT'=> 'nullable|max:50',
            'JUMLAH_TAGIHAN'    => 'required|numeric',
            'BIAYA_ADMIN'       => 'nullable|numeric',
            'TOTAL_BAYAR'       => 'required|numeric',
            'TGL_TRANSAKSI'     => 'nullable|date',
            'TGL_KADALUWARSA'   => 'nullable|date',
            'STATUS_TRANSAKSI'  => 'required|max:20',
        ]);

        $data = TransaksiPembayaran::create($request->all());

        // Update status tagihan & mahasiswa otomatis
        if ($request->STATUS_TRANSAKSI === 'SUKSES') {
            $tagihan = Tagihan::find($request->ID_TAGIHAN);
            if ($tagihan) {
                $tagihan->update(['STATUS_BAYAR' => 'LUNAS']);

                $keuangan = KeuanganMahasiswa::find($tagihan->ID_KEUANGAN_MHS);
                if ($keuangan) {
                    $keuangan->update(['STATUS_AKTIF' => 'AKTIF']);
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Transaksi pembayaran berhasil dicatat', 'data' => $data], 201);
    }

    public function show($id)
    {
        $data = TransaksiPembayaran::with('tagihan', 'metodeBayar')->find($id);
        if (!$data) return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function update(Request $request, $id)
    {
        $data = TransaksiPembayaran::find($id);
        if (!$data) return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);

        $request->validate([
            'NO_VIRTUAL_ACCOUNT' => 'sometimes|max:50',
            'JUMLAH_TAGIHAN'     => 'sometimes|numeric',
            'BIAYA_ADMIN'        => 'sometimes|numeric',
            'TOTAL_BAYAR'        => 'sometimes|numeric',
            'TGL_TRANSAKSI'      => 'sometimes|date',
            'TGL_KADALUWARSA'    => 'sometimes|date',
            'STATUS_TRANSAKSI'   => 'sometimes|max:20',
        ]);

        $data->update($request->all());
        return response()->json(['success' => true, 'message' => 'Transaksi berhasil diupdate', 'data' => $data]);
    }

    public function destroy($id)
    {
        $data = TransaksiPembayaran::find($id);
        if (!$data) return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        $data->delete();
        return response()->json(['success' => true, 'message' => 'Transaksi berhasil dihapus']);
    }
}