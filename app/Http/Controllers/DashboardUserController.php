<?php

namespace App\Http\Controllers;

use App\Models\absen_d;
use App\Models\absen_h;
use App\Models\Office;
use App\Models\onoff_tg;
use App\Models\pegawai;
use App\Models\presensi;
use App\Models\TglLibur;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class DashboardUserController extends Controller
{
    public function index()
    {
        $offices = Office::get();
        // dd($office);

        $datatdkmsk = $this->tdkmsk(Auth::user()->no_payroll);

        $items = reset($datatdkmsk);

        $rekap = [];

        foreach ($items as $item) {
            $jenis = $item['jns_absen'];
            $tgl   = $item['tgl_absen'];
            $hari  = \Carbon\Carbon::parse($tgl)->translatedFormat('l'); // nama hari lokal

            if (!isset($rekap[$jenis])) {
                $rekap[$jenis] = [
                    'jumlah' => 0,
                    'tanggal' => []
                ];
            }

            $rekap[$jenis]['jumlah']++;
            $rekap[$jenis]['tanggal'][] = [
                'tgl'  => $tgl,
                'hari' => $hari
            ];
        }

        return view('user.dashboard', compact('offices', 'rekap'));
    }




    public function tdkmsk($noPayroll = null)
    {
        set_time_limit(500);

        $tahun = Carbon::now()->format('Y');

        // Gunakan noPayroll yang dikirim, jika kosong pakai user login
        $noPayroll = $noPayroll ?? Auth::user()->no_payroll;

        // Ambil data pegawai
        $pgw = User::where('no_payroll', $noPayroll)->first();
        if (!$pgw) {
            return []; // Tidak ada pegawai
        }

        // pastikan tgl_masuk pegawai Carbon juga

        // pastikan tgl_masuk pegawai Carbon dan valid
        $tgl_awal = Carbon::createFromDate($tahun, 1, 1)->startOfDay();
        $tgl_akhir = Carbon::createFromDate($tahun, 12, 31)->startOfDay(); // ubah endOfDay() ke startOfDay()

        $tgl_masuk = $pgw->tgl_masuk ? Carbon::parse($pgw->tgl_masuk) : null;
        // pastikan tgl_masuk pegawai Carbon dan valid
        if ($pgw->tgl_masuk) {
            $tgl_masuk = Carbon::parse($pgw->tgl_masuk)->startOfDay();
            if ($tgl_awal->lt($tgl_masuk)) {
                $tgl_awal = $tgl_masuk->copy();
            }
        }

        // hitung jumlah hari (positif)
        $jumlah_hari = $tgl_awal->diffInDays($tgl_akhir); // hasil pasti integer

        // dd($tgl_awal->toDateString(), $tgl_akhir->toDateString(), $jumlah_hari);


        for ($i = 0; $i <= $jumlah_hari; $i++) {
            $daftar_tanggal[] = $tgl_awal->copy()->addDays($i)->format('Y-m-d');
        }
        // dd($daftar_tanggal);
        // Ambil daftar pegawai yang relevan
        $pegawaiQuery = User::where(function ($query) {
            $query->whereNull('tgl_keluar')->orWhere('tgl_keluar', '');
        })
            ->where('bagian', '!=', 'DIREKSI')
            ->orderBy('no_payroll', 'asc');

        if ($noPayroll) {
            $pegawaiQuery->where('no_payroll', $noPayroll);
        }

        $pegawaiList = $pegawaiQuery->get();
        $noRegistrasiPegawai = $pegawaiList->pluck('no_payroll')->toArray();

        // Ambil data presensi
        $absen = Presensi::whereIn('no_reg', $noRegistrasiPegawai)
            ->whereIn('tanggal', $daftar_tanggal)
            ->orderBy('tanggal', 'asc')
            ->get();

        $allPegawaiData = [];

        foreach ($pegawaiList as $peg) {

            // Ambil presensi pegawai ini
            $pegawaiAbsenData = [];
            foreach ($absen as $absenData) {
                if ($absenData->no_reg === $peg->no_payroll) {
                    $tanggal = $absenData->tanggal;
                    // Ambil data terbaru berdasarkan timestamp
                    if (!isset($pegawaiAbsenData[$tanggal]) || $absenData->timestamp > $pegawaiAbsenData[$tanggal]->timestamp) {
                        $pegawaiAbsenData[$tanggal] = $absenData;
                    }
                }
            }

            $pegawaiAbsen = collect($pegawaiAbsenData)
                ->sortBy('tanggal')
                ->values();

            // Cek tanggal yang tidak ada absen
            $missing_tanggal = array_diff($daftar_tanggal, $pegawaiAbsen->pluck('tanggal')->toArray());

            // Data on/off
            $onoff_tg = onoff_tg::whereIn('tgl_off', $missing_tanggal)
                ->orWhereIn('tgl_on', $missing_tanggal)
                ->pluck('tgl_off')
                ->toArray();

            // Data absen dengan keterangan
            $absenDataL = absen_d::whereIn('tgl_absen', $daftar_tanggal)
                ->join('absen_hs', 'absen_hs.int_absen', '=', 'absen_ds.int_absen')
                ->where('absen_hs.no_payroll', $peg->no_payroll)
                ->whereIn('jns_absen', ['IPC', 'ITU', 'ICB', 'IC', 'SD', 'I', 'SK', 'H1', 'H2'])
                ->select('tgl_absen', 'jns_absen')
                ->get()
                ->toArray();

            // Tanggal dengan keterangan absen
            $tgl_ket = absen_d::whereIn('tgl_absen', $daftar_tanggal)
                ->join('absen_hs', 'absen_hs.int_absen', '=', 'absen_ds.int_absen')
                ->where('absen_hs.no_payroll', $peg->no_payroll)
                ->whereNotNull('jns_absen')
                ->pluck('tgl_absen')
                ->toArray();
            // Libur
            $tgl_lbr = TglLibur::whereIn('tgl_libur', $daftar_tanggal)
                ->pluck('tgl_libur')
                ->toArray();

            // Tentukan tanggal mangkir
            $today = date('Y-m-d');
            $tanggal_tidak_masuk = [];
            foreach ($missing_tanggal as $tgl) {
                if (date('N', strtotime($tgl)) != 6 && date('N', strtotime($tgl)) != 7 && strtotime($tgl) < strtotime($today)) {
                    if (!in_array($tgl, $tgl_ket) && !in_array($tgl, $tgl_lbr) && !in_array($tgl, $onoff_tg)) {
                        $tanggal_tidak_masuk[] = [
                            'tgl_absen' => $tgl,
                            'jns_absen' => 'Mangkir',
                        ];
                    }
                }
            }

            // Gabungkan absen dengan mangkir
            $gabungData = array_merge($absenDataL, $tanggal_tidak_masuk);
            usort($gabungData, function ($a, $b) {
                return strtotime($a['tgl_absen']) - strtotime($b['tgl_absen']);
            });
            // dd($gabungData);

            // Simpan data per pegawai
            $allPegawaiData[$peg->no_payroll] = $gabungData;
        }

        return $allPegawaiData;
    }

    public function ipamdt()
    {
        $user = Auth::user();
        $offices = Office::get();

        set_time_limit(500);
        $noPayroll = $user->no_payroll;
        $peg = pegawai::where('no_payroll', $noPayroll)->first();
        // MENGATUR WAKTU AWAL SMAPAI AKHIR DARI REQUEST SESUAI TAHUN
        // dd($peg);
        $tgl_awal = Carbon::now()->startOfYear()->format('Y-m-d'); // awal tahun ini
        $tgl_akhir = Carbon::yesterday()->format('Y-m-d');        // kemarin

        $taw = $tgl_awal;
        $tak = $tgl_akhir;

        $on = onoff_tg::whereBetween('tgl_on', [$taw, $tak])->pluck('tgl_on')->toArray();
        // dd($on);
        $absenDataL = Presensi::selectRaw(
            '*, 
            CASE
                WHEN masuk IS NULL THEN "MDT"
                WHEN masuk > norm_m THEN "MDT"
                WHEN keluar IS NULL THEN "IPA"
                WHEN keluar < norm_k THEN "IPA"
                ELSE "Keterangan Lainnya"
            END as keterangan,
            GREATEST(ROUND((CASE
                WHEN masuk IS NOT NULL AND norm_m IS NOT NULL THEN (TIME_TO_SEC(masuk) - TIME_TO_SEC(norm_m)) / 60
                ELSE 0
            END)), 0) as mnt_ipa,
            GREATEST(ROUND((CASE
                WHEN keluar IS NOT NULL AND norm_k IS NOT NULL THEN (TIME_TO_SEC(norm_k) - TIME_TO_SEC(keluar)) / 60
                ELSE 0
            END)), 0) as mnt_dt'
        )
            ->whereBetween('tanggal', [$taw, $tak])
            ->where('no_reg', $noPayroll)
            ->where(function ($query) use ($on) {
                $query->where(function ($q) use ($on) {
                    $q->whereNotIn(DB::raw('DAYOFWEEK(tanggal)'), [7, 1]) // Bukan Sabtu atau Minggu
                        ->orWhereIn('tanggal', $on); // KECUALI jika tanggalnya ada di $on
                })->orWhereNull('tanggal');
            })
            ->get()
            ->filter(function ($presensi) {
                return ($presensi->keterangan === 'IPA' && $presensi->mnt_ipa !== null) ||
                    ($presensi->keterangan === 'MDT' && $presensi->mnt_dt !== null);
            });

        // Rekap
        $jumlah_mnt_ipa = $absenDataL->sum('mnt_ipa');
        $jumlah_mnt_dt = $absenDataL->sum('mnt_dt');
        $jumlah_hari_dt = $absenDataL->where('keterangan', 'IPA')->count();
        $jumlah_hari_ipa = $absenDataL->where('keterangan', 'MDT')->count();

        // dd($jumlah_mnt_ipa);

        return view('user.ipamdt', compact('tgl_awal', 'tgl_akhir', 'absenDataL', 'peg', 'jumlah_mnt_ipa', 'jumlah_mnt_dt', 'jumlah_hari_ipa', 'jumlah_hari_dt'));
    }

    public function shift()
    {
        $data = [
            'Non Shift' => [
                'masuk' => '08:00',
                'pulang' => '16:30',
            ],
            'Shift 1' => [
                'masuk' => '07:00',
                'pulang' => '15:30',
            ],
            'Shift 2' => [
                'masuk' => '15:00',
                'pulang' => '23:30',
            ],
            'Shift 3' => [
                'masuk' => '23:00',
                'pulang' => '07:30',
            ],
        ];

        return view('user.shift', compact('data'));
    }
}
