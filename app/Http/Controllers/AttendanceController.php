<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Office;
use App\Models\presensi;
use App\Models\Timework;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{

    public function index(Request $request)
    {
        $bulan = $request->bulan ?? Carbon::now()->format('m');
        $tahun = $request->tahun ?? Carbon::now()->format('Y');

        $presensis = DB::table(DB::raw("
    (
        SELECT 
            presensis.tanggal,
            presensis.masuk,
            presensis.keluar,
            attendances.id,
            attendances.type,
            attendances.time,
            attendances.address,
            attendances.photo,
            ROW_NUMBER() OVER (
                PARTITION BY presensis.tanggal, attendances.type
                ORDER BY 
                    CASE WHEN attendances.type = 'in' THEN attendances.time END ASC,
                    CASE WHEN attendances.type = 'out' THEN attendances.time END DESC
            ) AS row_num
        FROM presensis
        LEFT JOIN attendances 
            ON attendances.no_payroll = presensis.no_reg
           AND DATE(attendances.time) = presensis.tanggal
        WHERE presensis.no_reg = '" . Auth::user()->no_payroll . "'
    ) as t
"))
            ->where('row_num', 1)
            ->when($bulan, function ($query, $bulan) {
                $query->whereMonth('t.tanggal', $bulan);
            })
            ->when($tahun, function ($query, $tahun) {
                $query->whereYear('t.tanggal', $tahun);
            })
            ->orderBy('t.tanggal', 'desc')
            ->orderBy('t.type', 'desc')
            ->get();



        // dd($presensis);

        return view('user.attendance.index', compact('presensis', 'bulan', 'tahun'));
    }

    public function show($id, $tanggal, $type)
    {
        $attendance = Attendance::where('user_id', Auth::id())
            ->where('id', $id) // filter by id
            ->whereDate('time', $tanggal) // filter by tanggal
            ->where('type', $type)        // filter by type
            ->select('user_id', 'time', 'type', 'address', 'photo', 'no_payroll')
            ->orderBy('time', 'desc')
            ->first();

        $presensi = null;
        if ($attendance) {
            $presensi = Presensi::where('no_reg', $attendance->no_payroll)
                ->whereDate('tanggal', $tanggal) // filter juga tanggal
                ->select('tanggal', 'masuk', 'keluar')
                ->first();
        }

        return view('user.attendance.show', compact('attendance', 'presensi'));
    }



    public function create()
    {
        $attendances = Attendance::where('user_id', Auth::id())
            ->orderBy('time', 'desc')
            ->get();
        $offices = Office::get(); // Ambil kantor user
        // dd($offices);

        return view('user.attendance.create', compact('attendances', 'offices'));
    }





    public function checkin(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|in:in,out',
                'time' => 'required|date',
                'address' => 'required',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'photo' => 'required|string', // base64 string
            ]);

            DB::beginTransaction(); // Mulai transaksi

            // --- SIMPAN FOTO ---
            $photo = $request->photo;
            if (preg_match('/^data:image\/(\w+);base64,/', $photo, $type)) {
                $photo = substr($photo, strpos($photo, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, dll
                if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif'])) {
                    return back()->with('error', 'Format gambar tidak didukung');
                }
                $photo = base64_decode($photo);
            } else {
                return back()->with('error', 'Data gambar tidak valid');
            }

            $destinationPath = public_path('storage/selfies');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $fileName = 'selfie_' . time() . '.' . $type;
            file_put_contents($destinationPath . '/' . $fileName, $photo);

            // --- SIMPAN KE ATTENDANCE ---
            Attendance::create([
                'user_id'   => Auth::id(),
                'office_id' => 1,
                'no_payroll' => Auth::user()->no_payroll,
                'type'      => $request->type,
                'time'      => $request->time,
                'address'   => $request->address,
                'latitude'  => $request->latitude,
                'longitude' => $request->longitude,
                'photo'     => 'selfies/' . $fileName,
            ]);

            // --- LOGIKA PRESENSI ---
            if ($request->type == 'in') {
                $timeIn  = date('H:i', strtotime($request->time));
                $timeOut = null;
            } else {
                $timeIn  = null;
                $timeOut = date('H:i', strtotime($request->time));
            }

            $datetime = Carbon::parse($request->time);
            $date     = $datetime->toDateString();
            $pin      = Auth::user()->no_payroll;
            $peg      = User::where('no_payroll', $pin)->first();

            if (!$peg) {
                DB::rollBack();
                return redirect()->route('user.attendance.index')
                    ->with('error', 'Data karyawan tidak ditemukan.');
            }

            $time = Timework::where('tw_cod', $peg->gkcod)
                ->orWhereNull('tw_cod')
                ->orWhereNull('tw_qty')
                ->first();

            if (!$time) {
                DB::rollBack();
                return redirect()->route('user.attendance.index')
                    ->with('error', 'Jadwal kerja tidak ditemukan.');
            }

            $m   = $time->tw_ins;
            $k   = $time->tw_out;
            $msk = $timeIn ? date('G', strtotime($timeIn)) * 60 + date('i', strtotime($timeIn)) : null;
            $klr = $timeOut ? date('G', strtotime($timeOut)) * 60 + date('i', strtotime($timeOut)) : null;

            if ($time->tw_qty != 0 && $msk && $klr) {
                $time1 = Timework::where('tw_cod', $peg->gkcod)
                    ->where(function ($query) use ($msk, $klr) {
                        $query->where(function ($q) use ($msk, $klr) {
                            $q->where('vins01', '>=', $msk)->where('vout02', '<=', $klr);
                        })
                            ->orWhere(function ($q) use ($msk, $klr) {
                                $q->where('vins01', '>=', $msk)->where('vout01', '<=', $klr);
                            })
                            ->orWhere(function ($q) use ($msk, $klr) {
                                $q->where('vins02', '>=', $msk)->where('vout02', '<=', $klr);
                            })
                            ->orWhere(function ($q) use ($msk, $klr) {
                                $q->where('vins02', '>=', $msk)->where('vout01', '<=', $klr);
                            });
                    })
                    ->first();

                if ($time1) {
                    $m = $time1->tw_ins;
                    $k = $time1->tw_out;
                }
            }

            $existingPresensi = Presensi::where('tanggal', $date)
                ->where('no_reg', $pin)
                ->first();

            if ($existingPresensi) {
                if (!empty($timeIn) && (empty($existingPresensi->masuk) || $existingPresensi->masuk > $timeIn)) {
                    $existingPresensi->masuk = $timeIn;
                }

                if (!empty($timeOut) && (empty($existingPresensi->keluar) || $existingPresensi->keluar < $timeOut)) {
                    $existingPresensi->keluar = $timeOut;
                }

                if (empty($existingPresensi->norm_m)) {
                    $existingPresensi->norm_m = $m ?? null;
                }

                if (empty($existingPresensi->norm_k)) {
                    $existingPresensi->norm_k = $k ?? null;
                }

                $existingPresensi->gkcod = $peg->gkcod ?? null;
                $existingPresensi->save();
            } else {
                Presensi::create([
                    'tanggal' => $date,
                    'no_reg'  => $pin,
                    'masuk'   => $timeIn,
                    'keluar'  => $timeOut,
                    'norm_m'  => $m ?? null,
                    'norm_k'  => $k ?? null,
                    'gkcod'   => $peg->gkcod ?? null,
                ]);
            }

            DB::commit(); // semua berhasil

            return redirect()->route('attendance.index')
                ->with('success', 'Presensi berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack(); // kalau error rollback semua
            return redirect()->route('attendance.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
