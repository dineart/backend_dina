<?php
namespace App\Http\Controllers;

use App\Models\KeuanganMahasiswa;
use App\Models\KategoriUkt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class KeuanganMahasiswaController extends Controller
{
    public function index()
    {
        $data = KeuanganMahasiswa::with('kategoriUkt')->get();
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ID_KEUANGAN_MHS' => 'required|unique:KEUANGAN_MAHASISWA,ID_KEUANGAN_MHS|max:20',
            'ID_KATEGORI'     => 'required|exists:KATEGORI_UKT,ID_KATEGORI',
            'ID_MAHASISWA'    => 'required|max:36',
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
        $data = KeuanganMahasiswa::with('kategoriUkt', 'tagihan')->find($id);
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
            'ID_MAHASISWA' => 'sometimes|max:20',
            'SEMESTER'     => 'sometimes|max:15',
            'BEASISWA'     => 'nullable|max:20',
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

    public function testGolongan(Request $request)
    {
        $hasil = $this->tentukanGolongan(
            $request->penghasilan,
            $request->pekerjaan
        );

        return response()->json([
            'success' => true,
            'data' => $hasil
        ]);
    }

    public function generateDummy()
    {
        $response = Http::acceptJson()->get(
            'https://api-mahasiswa-4a.akufarish.my.id:8874/api/mahasiswa'
        );

        $mahasiswa = $response->json('data');

        //Debug sementara
        return response()->json(
            $response->json()
        );
        
        $no = 1;

        foreach ($mahasiswa as $mhs) {

            $hasil = $this->tentukanGolongan(
                $mhs['orang_tua_wali']['penghasilan'],
                $mhs['orang_tua_wali']['pekerjaan']
            );

            $kategori = KategoriUkt::where(
                'ID_PRODI',
                $mhs['prodi_id']
            )
            ->where(
                'GOLONGAN_UKT',
                $hasil['golongan']
            )
            ->first();

            if (!$kategori) {
                continue;
            }

            $cek = KeuanganMahasiswa::where(
                'ID_MAHASISWA',
                $mhs['id_mahasiswa']
            )->first();

            if ($cek) {
                continue;
            }

            KeuanganMahasiswa::create([
                'ID_KEUANGAN_MHS' => 'KM' . str_pad($no, 3, '0', STR_PAD_LEFT),
                'ID_MAHASISWA' => $mhs['id_mahasiswa'],
                'ID_KATEGORI' => $kategori->ID_KATEGORI,
                'SEMESTER' => 1,
                'BEASISWA' => $hasil['beasiswa'],
                'STATUS_AKTIF' => 'Tidak Aktif',
            ]);

            $no++;
        }

    return response()->json([
        'success' => true,
        'message' => 'Data dummy berhasil dibuat'
    ]);
}

    public function statusAktif($id_mahasiswa)
    {
        $data = KeuanganMahasiswa::with('kategoriUkt')
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
                'BEASISWA'     => $data->BEASISWA,
                'KATEGORI_UKT' => $data->kategoriUkt->GOLONGAN_UKT ?? null,
                'NOMINAL_UKT'  => $data->kategoriUkt->NOMINAL_UKT ?? null,
            ]
        ]);
    }

    private function tentukanGolongan($penghasilan, $pekerjaan)
    {
        $penghasilan = trim((string)$penghasilan);
        $pekerjaan = strtolower(trim((string)$pekerjaan));

        // Golongan 1 + KIPK
        if ($penghasilan == 'Kurang dari/Sama dengan 500.000') {
            return [
                'golongan' => 'Golongan 1',
                'beasiswa' => 'KIPK',
            ];
        }

        // Golongan 2
        if ($penghasilan == '500.000 - 2.000.000') {
            return [
                'golongan' => 'Golongan 2',
                'beasiswa' => null,
            ];
        }

        // Golongan 3,4,5
        if ($penghasilan == '2.000.000 - 5.000.000') {

            // IRT / Tidak Bekerja
            if (
                $pekerjaan == 'ibu rumah tangga' ||
                $pekerjaan == 'tidak bekerja'
            ) {
                return [
                    'golongan' => 'Golongan 3',
                    'beasiswa' => null,
                ];
            }

            // PNS
            if ($pekerjaan == 'pns') {
                return [
                    'golongan' => 'Golongan 5',
                    'beasiswa' => null,
                ];
            }

            // Bekerja, Wiraswasta, dll
            return [
                'golongan' => 'Golongan 4',
                'beasiswa' => null,
            ];
        }

        // Diatas 5 juta
        if ($penghasilan == 'Diatas 5.000.000') {
            return [
                'golongan' => 'Golongan 5',
                'beasiswa' => null,
            ];
        }

        // Fallback
        return [
            'golongan' => 'Golongan 5',
            'beasiswa' => null,
        ];
    }

}