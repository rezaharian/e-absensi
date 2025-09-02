@extends('user.ly')

@section('content')
    <div class="container my-3">

        {{-- Header --}}
        <div class="text-center mb-3">
            <h5 class="fw-bold text-light mb-1">Rekap Absensi</h5>
            <h6 class="text-light">Tahun : {{ \Carbon\Carbon::now()->year }}</h6>
        </div>

        {{-- Card Pegawai --}}
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body">

                {{-- Identitas --}}
                <div class="mb-3 text-center">
                    <h6 class="fw-bold text-dark mb-1">{{ $peg->nama_asli }}</h6>
                    <p class="text-muted mb-0" style="font-size: 10pt;">
                        <i class="bi bi-person-badge"></i> Reg: {{ $peg->no_payroll }} <br>
                        <i class="bi bi-calendar-check"></i> Masuk: {{ date('d-m-Y', strtotime($peg->tgl_masuk)) }}
                    </p>
                </div>

                <hr class="my-2">

                {{-- Statistik Absensi --}}
                <div class="row row-cols-2 g-2 text-muted" style="font-size: 10pt;">
                    <div class="col">
                        <div class="d-flex justify-content-between p-2 bg-light rounded">
                            <span>SK</span><span class="fw-bold">{{ $SK }}</span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="d-flex justify-content-between p-2 bg-light rounded">
                            <span>SD</span><span class="fw-bold">{{ $SD }}</span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="d-flex justify-content-between p-2 bg-light rounded">
                            <span>Haid</span><span class="fw-bold ">{{ $H }}</span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="d-flex justify-content-between p-2 bg-light rounded">
                            <span>Izin</span><span class="fw-bold ">{{ $I }}</span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="d-flex justify-content-between p-2 bg-light rounded">
                            <span>IPC</span><span class="fw-bold">{{ $IPC }}</span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="d-flex justify-content-between p-2 bg-light rounded">
                            <span>IC</span><span class="fw-bold">{{ $IC }}</span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="d-flex justify-content-between p-2 bg-light rounded">
                            <span>Mangkir</span><span class="fw-bold ">{{ $M }}</span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="d-flex justify-content-between p-2 bg-light rounded">
                            <span>Lambat (x)</span><span class="fw-bold">{{ $lmbtx }}</span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="d-flex justify-content-between p-2 bg-light rounded">
                            <span>Lambat (m)</span><span class="fw-bold">{{ $lmbtjm }}</span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="d-flex justify-content-between p-2 bg-light rounded">
                            <span>IPA (x)</span><span class="fw-bold">{{ $ipax }}</span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="d-flex justify-content-between p-2 bg-light rounded">
                            <span>IPA (m)</span><span class="fw-bold">{{ $ipajam }}</span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="d-flex justify-content-between p-2 bg-light rounded">
                            <span>DL</span><span class="fw-bold">{{ $dl }}</span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="d-flex justify-content-between p-2 bg-light rounded">
                            <span>Cuti Besar</span><span class="fw-bold">{{ $icb }}</span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="d-flex justify-content-between p-2 bg-light rounded">
                            <span>SCTB</span><span class="fw-bold">{{ $SCTB }}</span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="d-flex justify-content-between p-2 bg-light rounded">
                            <span>SCB</span><span class="fw-bold">{{ $SCB }}</span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="d-flex justify-content-between p-2 bg-light rounded">
                            <span>Saving</span><span class="fw-bold">{{ $saving }}</span>
                        </div>
                    </div>
                </div>



            </div>
        </div>
    </div>
@endsection
