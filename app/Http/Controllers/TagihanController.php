<?php
namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\KeuanganMahasiswa;
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
            'ID_TAGIHAN'      => 'required|unique:TAGIHAN,ID_TAGIHAN|max:20',
            'ID_KEUANGAN_MHS' => 'required|exists:KEUANGAN_MAHASISWA,ID_KEUANGAN_MHS',
            'NO_INVOICE'      => 'nullable|max:50',
            'NAMA_TAGIHAN'    => 'nullable|max:50',
            'NOMOR_CICILAN'   => 'nullable|integer',
            'TOTAL_CICILAN'   => 'nullable|integer',
            'NOMINAL_CICILAN' => 'nullable|numeric',
            'POTONGAN'        => 'nullable|numeric',
            'TOTAL_TAGIHAN'   => 'required|numeric',
            'TGL_JATUH_TEMPO' => 'nullable|date',
            'TGL_TAGIHAN'     => 'nullable|date',
            'STATUS_BAYAR'    => 'required|max:20',
            'TGL_BAYAR'   => 'nullable|date',
        ]);

        // Hitung potongan beasiswa otomatis
        $keuangan = KeuanganMahasiswa::with('kategoriUkt')
                    ->find($request->ID_KEUANGAN_MHS);

        $potongan = $request->POTONGAN ?? 0;
        $totalTagihan = $request->TOTAL_TAGIHAN - $potongan;
        $totalCicilan = $request->TOTAL_CICILAN ?? 1;
        $nominalCicilan = $totalCicilan > 0 ? $totalTagihan / $totalCicilan : $totalTagihan;
        $statusBayar = $totalTagihan <= 0 ? 'LUNAS' : $request->STATUS_BAYAR;

        $data = Tagihan::create(array_merge($request->all(), [
            'POTONGAN'        => $potongan,
            'NOMINAL_CICILAN' => $nominalCicilan,
            'TOTAL_TAGIHAN'   => $totalTagihan,
            'STATUS_BAYAR'    => $statusBayar,
        ]));

        if ($statusBayar === 'LUNAS' && $keuangan) {
            $keuangan->update(['STATUS_AKTIF' => 'AKTIF']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tagihan berhasil dibuat',
            'data'    => $data
        ], 201);
    }

    public function show($id)
    {
        $data = Tagihan::with('keuanganMahasiswa')->find($id);
        if (!$data) return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function update(Request $request, $id)
    {
        $data = Tagihan::find($id);
        if (!$data) return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);

        $request->validate([
            'NO_INVOICE'      => 'sometimes|max:50',
            'NAMA_TAGIHAN'    => 'sometimes|max:50',
            'NOMOR_CICILAN'   => 'sometimes|integer',
            'TOTAL_CICILAN'   => 'sometimes|integer',
            'NOMINAL_CICILAN' => 'sometimes|numeric',
            'POTONGAN'        => 'sometimes|numeric',
            'TOTAL_TAGIHAN'   => 'sometimes|numeric',
            'TGL_JATUH_TEMPO' => 'sometimes|date',
            'TGL_TAGIHAN'     => 'sometimes|date',
            'STATUS_BAYAR'    => 'sometimes|max:20',
            'TGL_BAYAR'   => 'sometimes|date',
        ]);

        $data->update($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Tagihan berhasil diupdate',
            'data'    => $data
        ]);
    }

    public function destroy($id)
    {
        $data = Tagihan::find($id);
        if (!$data) return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        $data->delete();
        return response()->json(['success' => true, 'message' => 'Tagihan berhasil dihapus']);
    }

    public function generateTagihan()
    {
        $dataKeuangan = KeuanganMahasiswa::with('kategoriUkt')->get();

        $inserted = 0;
        $skip = 0;

        $no = Tagihan::count() + 1;

        foreach ($dataKeuangan as $km) {

            $cek = Tagihan::where(
                'ID_KEUANGAN_MHS',
                $km->ID_KEUANGAN_MHS
            )->first();

            if ($cek) {
                $skip++;
                continue;
            }
        

            Tagihan::create([
                'ID_TAGIHAN'      => 'TG' . str_pad($no, 3, '0', STR_PAD_LEFT),
                'ID_KEUANGAN_MHS' => $km->ID_KEUANGAN_MHS,

                'NO_INVOICE'      => 'INV' . str_pad($no, 5, '0', STR_PAD_LEFT),

                'NAMA_TAGIHAN'    => 'UKT Semester 1',

                'NOMOR_CICILAN'   => 1,
                'TOTAL_CICILAN'   => 1,

                'NOMINAL_CICILAN' => $km->kategoriUkt->NOMINAL_UKT,

                'POTONGAN'        => 0,

                'TOTAL_TAGIHAN'   => $km->kategoriUkt->NOMINAL_UKT,

                'TGL_TAGIHAN'     => now()->toDateString(),

                'TGL_JATUH_TEMPO' => now()
                                        ->addDays(30)
                                        ->toDateString(),

                'STATUS_BAYAR'    => 'Belum Bayar',

                'TGL_BAYAR'       => null,
            ]);

            $inserted++;
            $no++;
        }

    return response()->json([
        'success' => true,
        'inserted' => $inserted,
        'skip'  => $skip,
    ]);
        
    }

}