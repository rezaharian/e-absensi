<?php

namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class HalamanAdminDiUserController extends Controller
{

    public function index(Request $request)
    {

        // cek user login dan bagian
        if (!(Auth::check() && Auth::user()->bagian === 'EDP')) {
            abort(403, 'Anda tidak punya akses ke halaman ini.');
        }
        $bulan = $request->bulan ?? Carbon::now()->format('m');
        $tahun = $request->tahun ?? Carbon::now()->format('Y');

        $presensis = DB::table(DB::raw("
                    (
                        SELECT 
                            presensis.tanggal,
                            presensis.no_reg as no_payroll,
                            pegawais.nama_asli as nama_pegawai,
                            presensis.masuk,
                            presensis.keluar,
                            attendances.id,
                            attendances.type,
                            attendances.time,
                            attendances.address,
                            attendances.photo,
                            ROW_NUMBER() OVER (
                                PARTITION BY presensis.tanggal, presensis.no_reg, attendances.type
                                ORDER BY 
                                    CASE WHEN attendances.type = 'in' THEN attendances.time END ASC,
                                    CASE WHEN attendances.type = 'out' THEN attendances.time END DESC
                            ) AS row_num
                        FROM presensis
                        INNER JOIN attendances 
                            ON attendances.no_payroll = presensis.no_reg
                           AND DATE(attendances.time) = presensis.tanggal
                        INNER JOIN pegawais
                            ON pegawais.no_payroll = presensis.no_reg
                    ) as t
                "))
            ->where('row_num', 1)
            ->when($bulan, fn($q) => $q->whereMonth('t.tanggal', $bulan))
            ->when($tahun, fn($q) => $q->whereYear('t.tanggal', $tahun))
            ->orderBy('t.tanggal', 'desc')
            ->orderBy('t.type', 'desc')
            ->get();


        // dd($presensis);
        return view('user.admin.index', compact('presensis', 'bulan', 'tahun'));
    }
}
