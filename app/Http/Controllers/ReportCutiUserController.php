<?php

namespace App\Http\Controllers;

use App\Models\absen_d;
use App\Models\ct_besar;
use App\Models\onoff_tg;
use App\Models\pegawai;
use App\Models\presensi;
use App\Models\Pt_gaji;
use App\Models\TglLibur;
use Auth;
use Carbon\Carbon;
use DateTime;
use DB;
use Illuminate\Http\Request;

class ReportCutiUserController extends Controller
{
    public function user_cuti(Request $request)
    {
        $bulanAwal  = date('n', strtotime('first day of january')); // 1
        $bulanAkhir = date('n'); // bulan sekarang, misal 9 untuk September

        $tahun      = date('Y');       // Tahun sekarang
        $noPayroll = Auth::user()->no_payroll;
        $thn_inp = $tahun;
        // dd($peg->toArray());
        // Mencari H
        $absen = pegawai::where('no_payroll', $noPayroll)->first();

        $bulanIndonesia = [
            1 => 'JANUARI',
            2 => 'FEBRUARI',
            3 => 'MARET',
            4 => 'APRIL',
            5 => 'MEI',
            6 => 'JUNI',
            7 => 'JULI',
            8 => 'AGUSTUS',
            9 => 'SEPTEMBER',
            10 => 'OKTOBER',
            11 => 'NOVEMBER',
            12 => 'DESEMBER',
        ];

        $bulanAwalNama = $bulanIndonesia[$bulanAwal];
        $bulanAkhirNama = $bulanIndonesia[$bulanAkhir];

        $rangeBulan = [];
        for ($i = $bulanAwal; $i <= $bulanAkhir; $i++) {
            $rangeBulan[] = $bulanIndonesia[$i];
        }

        $saving = $bulanAkhir;

        // dd($saving);
        if ($noPayroll) {
            $peg = pegawai::where('no_payroll', $noPayroll)->first();

            $bulanMasuk = Carbon::parse($peg->tgl_masuk)->format('n');
            $tahunMasuk = Carbon::parse($peg->tgl_masuk)->format('Y');
            $bulanAkhir = $bulanAkhir;

            // dd($bulanAkhir);
            if ($thn_inp <= $tahunMasuk) {
                if ($bulanMasuk > $bulanAwal) {
                    # code...
                    $bulanIndonesia = [
                        1 => 'JANUARI',
                        2 => 'FEBRUARI',
                        3 => 'MARET',
                        4 => 'APRIL',
                        5 => 'MEI',
                        6 => 'JUNI',
                        7 => 'JULI',
                        8 => 'AGUSTUS',
                        9 => 'SEPTEMBER',
                        10 => 'OKTOBER',
                        11 => 'NOVEMBER',
                        12 => 'DESEMBER',
                    ];

                    // dd($bulanIndonesia[$bulanMasuk]);
                    $bulanAwalNama = $bulanIndonesia[$bulanMasuk];
                    $bulanAkhirNama = $bulanIndonesia[$bulanAkhir];
                    $rangeBulan = [];
                    for ($i = $bulanAwal; $i <= $bulanAkhir; $i++) {
                        $rangeBulan[] = $bulanIndonesia[$i];
                    }
                    $bulanAwal = $bulanMasuk;
                    // $tahun = $tahunMasuk;
                    $saving = $bulanAkhir - $bulanMasuk;
                    // dd($saving);
                }
            }

            $absen_d_query = absen_d::join('absen_hs', 'absen_hs.int_absen', '=', 'absen_ds.int_absen')->where('absen_hs.no_payroll', $noPayroll)->where('absen_ds.thn_absen', $tahun)->whereIn('absen_ds.bln_absen', $rangeBulan);
            $absen_counts = $absen_d_query
                ->selectRaw(
                    '
                        SUM(CASE WHEN jns_absen = "SD" THEN 1 ELSE 0 END) as SD,
                        SUM(CASE WHEN jns_absen = "IPC" THEN 1 ELSE 0 END) as IPC,
                        SUM(CASE WHEN jns_absen = "IC" THEN 1 ELSE 0 END) as IC,
                        SUM(CASE WHEN jns_absen IN ("H1", "H2") THEN 1 ELSE 0 END) as H,
                        SUM(CASE WHEN jns_absen = "SK" THEN 1 ELSE 0 END) as SK,
                        SUM(CASE WHEN jns_absen = "I" THEN 1 ELSE 0 END) as I
                    ',
                )
                ->first();

            $SD = $absen_counts->SD;
            $IPC = $absen_counts->IPC;
            $IC = $absen_counts->IC;
            $H = $absen_counts->H;
            $SK = $absen_counts->SK;
            $I = $absen_counts->I;

            // dd($bulanAkhir);
            // Mencari M ============================================================================================================

            $M = 0;
            $tanggalAwalPertama = '01-' . str_pad($bulanAwal, 2, '0', STR_PAD_LEFT) . '-' . $tahun;
            $tanggalAwalPertama = Carbon::createFromFormat('d-m-Y', $tanggalAwalPertama)->format('Y-m-d');
            $tanggalAkhirTerakhir = date('t-m-Y', strtotime('01-' . str_pad($bulanAkhir, 2, '0', STR_PAD_LEFT) . '-' . $tahun));
            $tanggalAkhirTerakhir = Carbon::createFromFormat('d-m-Y', $tanggalAkhirTerakhir)->format('Y-m-d');
            $tanggalHariIni = Carbon::now();
            $tanggalHariIni = $tanggalHariIni->subDay();

            $tglMasuk = Carbon::parse($peg->tgl_masuk);

            if ($tanggalHariIni->gte($tanggalAkhirTerakhir)) {
                $tanggalAkhirTerakhir = $tanggalAkhirTerakhir;
            } else {
                $tanggalAkhirTerakhir = $tanggalHariIni->format('Y-m-d');
            }

            if ($tglMasuk->gte($tanggalAwalPertama)) {
                $tanggalAwalPertama = $tglMasuk;
            } else {
                $tanggalAwalPertama = $tanggalAwalPertama;
            }

            // dd($tanggalAkhirTerakhir);

            $tgl_list = [];
            $currentDate = strtotime($tanggalAwalPertama);
            $endDate = strtotime($tanggalAkhirTerakhir);
            while ($currentDate <= $endDate) {
                $currentDayOfWeek = date('N', $currentDate);
                if ($currentDayOfWeek < 6) {
                    $tgl_list[] = date('Y-m-d', $currentDate);
                }
                $currentDate = strtotime('+1 day', $currentDate);
            }

            // dd($currentDate);
            $noPayroll = $absen->no_payroll;
            $tglabs = absen_d::join('absen_hs', 'absen_hs.int_absen', '=', 'absen_ds.int_absen')
                ->where('absen_hs.no_payroll', $noPayroll)
                ->where(function ($query) {
                    $query->whereNotNull('jns_absen')->where('jns_absen', '!=', '');
                })
                ->whereBetween('tgl_absen', [$tanggalAwalPertama, $tanggalAkhirTerakhir])
                ->pluck('tgl_absen')
                ->toArray();
            $prese = DB::table('presensis')
                ->where('no_reg', $noPayroll)
                ->whereBetween('tanggal', [$tanggalAwalPertama, $tanggalAkhirTerakhir])
                ->pluck('tanggal')
                ->toArray();
            $tglon = onoff_tg::whereBetween('tgl_on', [$tanggalAwalPertama, $tanggalAkhirTerakhir])
                ->pluck('tgl_on')
                ->toArray();
            $tgloff = onoff_tg::whereBetween('tgl_off', [$tanggalAwalPertama, $tanggalAkhirTerakhir])
                ->pluck('tgl_off')
                ->toArray();
            $tglLibur = TglLibur::whereBetween('tgl_libur', [$tanggalAwalPertama, $tanggalAkhirTerakhir])
                ->pluck('tgl_libur')
                ->toArray();
            $onoff = array_merge($tglon, $tgloff);
            $jmlh = array_diff(array_diff(array_diff(array_diff($tgl_list, $prese), $tglabs), $tglLibur), $onoff);
            // dd($jmlh);
            $M = count($jmlh);
            // SELESAI MENCARI M ====================================================================================================
            // dd($M);
            // LMBT(X)
            $lmbtx = presensi::where('no_reg', $absen->no_payroll)
                ->whereNotNull('norm_m')
                ->whereNotNull('norm_k')
                ->whereNotNull('masuk')
                ->whereNotNull('keluar')
                ->whereColumn('masuk', '>', 'norm_m')
                ->where(function ($query) use ($bulanAwal, $bulanAkhir, $tahun) {
                    $query->whereRaw('MONTH(tanggal) BETWEEN ? AND ?', [$bulanAwal, $bulanAkhir])->whereYear('tanggal', $tahun);
                })
                ->where(function ($query) {
                    $query
                        ->whereRaw('DAYOFWEEK(tanggal) NOT IN (1, 7)') // Tanggal bukan hari Sabtu (7) atau Minggu (1)
                        ->orWhereExists(function ($subquery) {
                            $subquery->from('onoff_tgs')->whereColumn('onoff_tgs.tgl_on', '=', 'presensis.tanggal');
                        })
                        ->whereNotExists(function ($subquery) {
                            $subquery->from('tgl_liburs')->whereColumn('tgl_liburs.tgl_libur', '=', 'presensis.tanggal')->whereNotNull('tgl_liburs.keterangan'); // Tanggal yang memiliki keterangan tidak dihitung
                        });
                })
                ->count();
            // dd($lmbtx->toArray());

            // LMBT(M)
            $lmbt = Presensi::where('no_reg', $absen->no_payroll)
                ->whereNotNull('norm_m') // Kolom 'norm_m' tidak boleh NULL
                ->whereNotNull('norm_k') // Kolom 'norm_k' tidak boleh NULL
                ->whereNotNull('masuk') // Kolom 'masuk' tidak boleh NULL
                ->whereNotNull('keluar') // Kolom 'keluar' tidak boleh NULL
                ->where('norm_m', '<>', '') // Tidak sama dengan string kosong
                ->where('norm_k', '<>', '') // Tidak sama dengan string kosong
                ->where('masuk', '<>', '') // Tidak sama dengan string kosong
                ->where('keluar', '<>', '') // Tidak sama dengan string kosong
                ->whereColumn('masuk', '>', 'norm_m')
                ->where(function ($query) use ($bulanAwal, $bulanAkhir, $tahun) {
                    $query->whereRaw('MONTH(tanggal) BETWEEN ? AND ?', [$bulanAwal, $bulanAkhir])->whereYear('tanggal', $tahun);
                })
                ->where(function ($query) {
                    $query
                        ->whereRaw('DAYOFWEEK(tanggal) NOT IN (1, 7)') // Tanggal bukan hari Sabtu (7) atau Minggu (1)
                        ->orWhereExists(function ($subquery) {
                            $subquery->from('onoff_tgs')->whereColumn('onoff_tgs.tgl_on', '=', 'presensis.tanggal');
                        })
                        ->whereNotExists(function ($subquery) {
                            $subquery->from('tgl_liburs')->whereColumn('tgl_liburs.tgl_libur', '=', 'presensis.tanggal')->whereNotNull('tgl_liburs.keterangan'); // Tanggal yang memiliki keterangan tidak dihitung
                        });
                })
                ->get();

            $totalKeterlambatan = 0; // Inisialisasi total keterlambatan

            foreach ($lmbt as $presensi) {
                $masuk = strtotime($presensi->masuk); // Mengubah masuk menjadi timestamp
                $norm_m = strtotime($presensi->norm_m); // Mengubah norm_m menjadi timestamp

                $selisihDetik = $masuk - $norm_m; // Menghitung selisih dalam detik
                $selisihJam = $selisihDetik / 3600; // Menghitung selisih dalam jam

                // Jika selisih jam negatif (keterlambatan), tambahkan ke total keterlambatan
                if ($selisihJam > 0) {
                    $totalKeterlambatan += $selisihJam;
                }
            }

            // Anda juga dapat menyimpan total keterlambatan dalam variabel $lmbtx
            $totalKeterlambatanInMinutes = $totalKeterlambatan * 60; // Convert hours to minutes
            $lmbtjm = intval($totalKeterlambatanInMinutes, 2); // Tanpa pemisah desimal

            // IPA x
            $ipax = Presensi::where('no_reg', $absen->no_payroll)
                ->whereNotNull('norm_m')
                ->whereNotNull('norm_k')
                ->whereNotNull('masuk')
                ->whereNotNull('keluar')
                ->where('norm_m', '<>', '') // Tidak sama dengan string kosong
                ->where('norm_k', '<>', '') // Tidak sama dengan string kosong
                ->where('masuk', '<>', '') // Tidak sama dengan string kosong
                ->where('keluar', '<>', '') // Tidak sama dengan string kosong
                ->whereColumn('keluar', '<', 'norm_k')
                ->where(function ($query) use ($bulanAwal, $bulanAkhir, $tahun) {
                    $query->whereRaw('MONTH(tanggal) BETWEEN ? AND ?', [$bulanAwal, $bulanAkhir])->whereYear('tanggal', $tahun);
                })
                ->where(function ($query) {
                    $query
                        ->whereRaw('DAYOFWEEK(tanggal) NOT IN (1, 7)') // Tanggal bukan hari Sabtu (7) atau Minggu (1)
                        ->orWhereExists(function ($subquery) {
                            $subquery->from('onoff_tgs')->whereColumn('onoff_tgs.tgl_on', '=', 'presensis.tanggal');
                        })
                        ->whereNotExists(function ($subquery) {
                            $subquery->from('tgl_liburs')->whereColumn('tgl_liburs.tgl_libur', '=', 'presensis.tanggal')->whereNotNull('tgl_liburs.keterangan'); // Tanggal yang memiliki keterangan tidak dihitung
                        });
                })
                ->count();

            // IPA Jam
            $ipaj = Presensi::where('no_reg', $absen->no_payroll)
                ->whereNotNull('norm_m') // Kolom 'norm_m' tidak boleh NULL
                ->whereNotNull('norm_k') // Kolom 'norm_k' tidak boleh NULL
                ->whereNotNull('masuk') // Kolom 'masuk' tidak boleh NULL
                ->whereNotNull('keluar') // Kolom 'keluar' tidak boleh NULL
                ->where('norm_m', '<>', '') // Tidak sama dengan string kosong
                ->where('norm_k', '<>', '') // Tidak sama dengan string kosong
                ->where('masuk', '<>', '') // Tidak sama dengan string kosong
                ->where('keluar', '<>', '') // Tidak sama dengan string kosong
                ->whereColumn('keluar', '<', 'norm_k')
                ->where(function ($query) use ($bulanAwal, $bulanAkhir, $tahun) {
                    $query->whereRaw('MONTH(tanggal) BETWEEN ? AND ?', [$bulanAwal, $bulanAkhir])->whereYear('tanggal', $tahun);
                })
                ->where(function ($query) {
                    $query
                        ->whereRaw('DAYOFWEEK(tanggal) NOT IN (1, 7)') // Tanggal bukan hari Sabtu (7) atau Minggu (1)
                        ->orWhereExists(function ($subquery) {
                            $subquery->from('onoff_tgs')->whereColumn('onoff_tgs.tgl_on', '=', 'presensis.tanggal');
                        })
                        ->whereNotExists(function ($subquery) {
                            $subquery->from('tgl_liburs')->whereColumn('tgl_liburs.tgl_libur', '=', 'presensis.tanggal')->whereNotNull('tgl_liburs.keterangan'); // Tanggal yang memiliki keterangan tidak dihitung
                        });
                })
                ->get();

            $totalplngawal = 0; // Inisialisasi total keterlambatan

            foreach ($ipaj as $presensi) {
                $keluar = strtotime($presensi->keluar); // Mengubah masuk menjadi timestamp
                $norm_k = strtotime($presensi->norm_k); // Mengubah norm_m menjadi timestamp

                $selisihDetik = $norm_k - $keluar; // Menghitung selisih dalam detik
                $selisihJam = $selisihDetik / 3600; // Menghitung selisih dalam jam

                // Jika selisih jam negatif (keterlambatan), tambahkan ke total keterlambatan
                if ($selisihJam > 0) {
                    $totalplngawal += $selisihJam;
                }
            }

            // Anda juga dapat menyimpan total keterlambatan dalam variabel $ipajam
            $totalplngawalInMinutes = $totalplngawal * 60; // Convert hours to minutes
            $ipajam = intval($totalplngawalInMinutes, 2);

            // DL
            $dl = absen_d::join('absen_hs', 'absen_hs.int_absen', '=', 'absen_ds.int_absen')
                ->where('absen_hs.no_payroll', $noPayroll)
                ->whereIn('absen_ds.jns_absen', ['DL', 'DL1', 'DL2', 'DL3'])
                ->where('absen_ds.thn_jns', $tahun)
                ->whereIn('absen_ds.bln_absen', $rangeBulan)
                ->count();

            // ICB

            $icb = absen_d::join('absen_hs', 'absen_hs.int_absen', '=', 'absen_ds.int_absen')->where('absen_hs.no_payroll', $noPayroll)->where('jns_absen', 'ICB')->count();

            // dd($icb);

            // mencari sctbts ===============================================================================================================================
            $tahun_masuk = Carbon::parse($peg->tgl_masuk)->year;
            // dd($thn_inp - $tahun_masuk);
            if ($thn_inp - $tahun_masuk == 2) {
                //MENGHITUNG CUTIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIII--=-=-=-
                $inputBulan = 12; // Ganti dengan bulan yang diinginkan, misalnya 10 untuk Oktober
                $thn_inp = $thn_inp - 1; // Tahun saat ini
                // dd($thn_inp);

                if (array_key_exists($inputBulan, $bulanIndonesia)) {
                    $tanggalAkhirBulan = date('Y-m-t', strtotime("$thn_inp-{$inputBulan}-01"));
                    $blncoba = new DateTime($tanggalAkhirBulan); // Konversi ke objek DateTime
                } else {
                    // echo "Bulan tidak valid.";
                }
                // dd($blncoba);
                $masuk = new DateTime($peg->tgl_masuk); // Konversi ke objek DateTime
                // dd($masuk);
                $interval = $masuk->diff($blncoba);
                $selisihBulan = $interval->y * 12 + $interval->m;

                $selisihTahun = $blncoba->diff($masuk)->y;
                // dd($selisihBulan);
                if ($selisihBulan < 12) {
                    $cutits = 0;
                } elseif ($blncoba->format('m') != 1 && $selisihBulan >= 12) {
                    $cutits = min(12, $selisihBulan - 11); // Menghitung cutits hingga akhir tahun
                } else {
                    $cutits = 12; // Awal tahun langsung dapat cuti 12
                }

                // dd($cutits);
                // cari IC dan IPC
                $absen_d_query = absen_d::join('absen_hs', 'absen_hs.int_absen', '=', 'absen_ds.int_absen')->where('absen_hs.no_payroll', $noPayroll)->where('absen_ds.thn_absen', $thn_inp);
                // ->whereIn('absen_ds.bln_absen', $rangeBulan);
                $absen_counts = $absen_d_query
                    ->selectRaw(
                        '
                            SUM(CASE WHEN jns_absen = "SD" THEN 1 ELSE 0 END) as SD,
                            SUM(CASE WHEN jns_absen = "IPC" THEN 1 ELSE 0 END) as IPC,
                            SUM(CASE WHEN jns_absen = "IC" THEN 1 ELSE 0 END) as IC,
                            SUM(CASE WHEN jns_absen IN ("H1", "H2") THEN 1 ELSE 0 END) as H,
                            SUM(CASE WHEN jns_absen = "SK" THEN 1 ELSE 0 END) as SK,
                            SUM(CASE WHEN jns_absen = "I" THEN 1 ELSE 0 END) as I
                        ',
                    )
                    ->first();

                $IPCts = $absen_counts->IPC;
                $SDts = $absen_counts->SD;
                $ICts = $absen_counts->IC;
                $Hts = $absen_counts->H;
                $SKts = $absen_counts->SK;
                $Its = $absen_counts->I;

                // dd($ICts);

                // cari mangkir
                // $M = 0;
                $tahun01 = $thn_inp;
                $tgl_list = [];

                // Get the current date and yesterday's date
                $currentDate = Carbon::now();
                $yesterday = Carbon::yesterday();

                // Iterasi setiap hari dalam tahun $tahun01
                for ($bulan = 1; $bulan <= 12; $bulan++) {
                    for ($hari = 1; $hari <= 31; $hari++) {
                        // Cek apakah tanggal valid
                        if (checkdate($bulan, $hari, $tahun01)) {
                            $currentDate = Carbon::create($tahun01, $bulan, $hari);

                            // Cek apakah hari tersebut bukan hari Sabtu atau Minggu (Senin hingga Jumat)
                            if ($currentDate->dayOfWeek != 6 && $currentDate->dayOfWeek != 0) {
                                // Cek apakah tanggal sudah lewat (kurang dari atau sama dengan kemarin)
                                if ($currentDate->lessThanOrEqualTo($yesterday)) {
                                    $tgl_list[] = $currentDate->format('Y-m-d');
                                }
                            }
                        }
                    }
                }

                // dd($tgl_list);

                // $tgl_list sekarang berisi semua tanggal selama tahun $tahun01 yang bukan Sabtu atau Minggu

                $noPayroll = $absen->no_payroll;
                // dd($thn_inp);
                $tglabs = absen_d::join('absen_hs', 'absen_hs.int_absen', '=', 'absen_ds.int_absen')->where('absen_hs.no_payroll', $noPayroll)->whereYear('absen_ds.tgl_absen', $thn_inp)->pluck('absen_ds.tgl_absen')->toArray();

                $prese = DB::table('presensis')->where('no_reg', $noPayroll)->whereYear('tanggal', $thn_inp)->pluck('tanggal')->toArray();

                $tglon = onoff_tg::whereYear('tgl_on', $thn_inp)->pluck('tgl_on')->toArray();

                $tgloff = onoff_tg::whereYear('tgl_off', $thn_inp)->pluck('tgl_off')->toArray();

                $tglLibur = TglLibur::whereYear('tgl_libur', $thn_inp)->pluck('tgl_libur')->toArray();

                $onoff = array_merge($tglon, $tgloff);
                $jmlh = array_diff(array_diff(array_diff(array_diff($tgl_list, $prese), $tglabs), $tglLibur), $onoff);
                $Mts = count($jmlh);

                $potonggajits = Pt_gaji::where('no_payroll', $noPayroll)->where('thn', $thn_inp)->pluck('jml_hari')->sum();
                // dd($ICts);
                // $scbts = $cutits - $ICts - $IPCts - $Mts + $potonggajits;// INI DI MATIKAN KARENA HRD NYA PLIN PLAN TIDAK JADI PAKAI
                $scbts = 0;
            } else {
                $scbts = 0;
            }
            // dd($scbts);

            // udah +=+=________________++++++++++++++++++++++++__________++++++_+_+_+_++++++++++++_________+++++++++_________+++++++++++_+_------------------------++++++++++++++++++_+++++++__________________+

            //MENGHITUNG CUTIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIII
            $inputBulan = $bulanAkhir; // Ganti dengan bulan yang diinginkan, misalnya 10 untuk Oktober
            $tahun = $request->input('thn'); // Tahun saat ini

            if (array_key_exists($inputBulan, $bulanIndonesia)) {
                $tanggalAkhirBulan = date('Y-m-t', strtotime("$tahun-{$inputBulan}-01"));
                $blncoba = new DateTime($tanggalAkhirBulan); // Konversi ke objek DateTime
            } else {
                // echo "Bulan tidak valid.";
            }

            $masuk = new DateTime($peg->tgl_masuk); // Konversi ke objek DateTime

            // dd($tahun);
            $interval = $masuk->diff($blncoba);
            $selisihBulan = $interval->y * 12 + $interval->m;

            $selisihTahun = $blncoba->diff($masuk)->y;
            // dd($selisihBulan);
            if ($selisihBulan < 12) {
                $cuti = 0 + $scbts;
            } else {
                $cuti = 12 + $scbts; // Awal tahun langsung dapat cuti 12
            }

            // dd($cuti);

            $potonggaji = Pt_gaji::where('no_payroll', $noPayroll)
                ->whereBetween('no_bln', [$bulanAwal, $bulanAkhir])
                ->where('thn', $tahun)
                ->pluck('jml_hari')
                ->sum();
            //SCTB
            // dd($SCTB);
            $SCTB = $cuti - $IC - $IPC - $M + $potonggaji;
            // dd($potonggaji);

            $tahunSebelumnya = $tahun - 1;
            // SCB BBBBBBBBBBBBBBBBBBBBBBBBBBBBBBB
            $mskrj = Carbon::parse($peg->tgl_masuk); // Mengonversi tanggal masuk ke objek Carbon
            $sdbulan = Carbon::createFromDate($tahun, $bulanAkhir, 1); // Objek Carbon untuk tanggal yang diinginkan

            // Menyesuaikan tanggal referensi ke tanggal masuk pegawai
            $sdbulan->day($mskrj->day);

            // Menghitung selisih tahun antara tanggal masuk dan tanggal referensi
            $yearsOfWork = $mskrj->diffInYears($sdbulan);

            // dd($sdbulan);
            $datacb = ct_besar::where('no_payroll', $noPayroll)->where('tahun', $tahunSebelumnya)->first();
            // dd($datacb->sisa_cb);

            if ($datacb) {
                $SCB = ct_besar::where('no_payroll', $noPayroll)->where('tahun', $tahunSebelumnya)->value('sisa_cb');
            } else {
                if ($yearsOfWork == 10) {
                    $SCB = 20;
                } elseif ($yearsOfWork > 10) {
                    $SCB = ct_besar::where('no_payroll', $noPayroll)->where('tahun', $tahunSebelumnya)->value('sisa_cb');
                } else {
                    $SCB = 0;
                }
            } // dd($SCB);

            // $tahunIni = Carbon::$tahun->year;
            $icbthini = absen_d::join('absen_hs', 'absen_hs.int_absen', '=', 'absen_ds.int_absen')->where('absen_hs.no_payroll', $noPayroll)->where('absen_ds.jns_absen', 'ICB')->whereYear('absen_ds.tgl_absen', $tahun)->count();
            // dd($icbthini);

            $SCB = $SCB - $icbthini;

            // Menggunakan $sdbulan dan $SCB sesuai kebutuhan

            // dd($SCB);
            return view('user.cuti', compact('bulanAwal', 'saving', 'bulanAkhir', 'tahun', 'peg', 'H', 'SK', 'SD', 'I', 'IPC', 'IC', 'M', 'lmbtx', 'lmbtjm', 'ipax', 'ipajam', 'dl', 'icb', 'SCTB', 'SCB'));
            //  batassssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssss----------------------------------------------------------------------------------------------------------------------------------------------------------
        }
        return view('user.cuti');
    }
}
