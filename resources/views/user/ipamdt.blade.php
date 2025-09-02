@extends('user.ly')

@section('content')
    <div class="container my-3">

        {{-- Header --}}
        <div class="text-center mb-4">
            <h6 class="fw-bold text-light mb-1">
                DATA ABSENSI IPA & MDT <br> KARYAWAN PT. EXTRUPACK
            </h6>
            <small class="text-light">Tahun {{ date('Y') }}</small>
        </div>

        {{-- Info Karyawan --}}
        <div class="card shadow-sm border-0   rounded-4">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted"><i class="bi bi-person-badge me-1"></i>No Payroll</span>
                    <span class="fw-semibold">{{ $peg->no_payroll }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted"><i class="bi bi-person me-1"></i>Nama</span>
                    <span class="fw-semibold">{{ $peg->nama_asli }}</span>
                </div>
            </div>
        </div>

        {{-- Data Absen --}}
        @if (count($absenDataL) > 0)
            <div class="list-group shadow-sm rounded-4 overflow-hidden">
                @foreach ($absenDataL as $absen)
                    <a href="#"
                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                        data-bs-toggle="modal" data-bs-target="#detailModal{{ $loop->iteration }}">
                        <div>
                            <div class="fw-bold">{{ date('d M Y', strtotime($absen->tanggal)) }}</div>
                            <small class="text-muted"><i class="bi bi-info-circle me-1"></i>{{ $absen->keterangan }}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-success px-3 py-2 rounded-pill">{{ $absen->masuk }}</span>
                            <span class="badge bg-danger px-3 py-2 rounded-pill">{{ $absen->keluar }}</span>
                        </div>
                    </a>

                    {{-- Modal Detail --}}
                    <div class="modal fade" id="detailModal{{ $loop->iteration }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content rounded-4 shadow-sm">
                                <div class="modal-header border-0">
                                    <h6 class="modal-title fw-bold text-primary">Detail Absensi</h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body small">
                                    <p><i class="bi bi-calendar-event me-1 text-primary"></i><strong>Tanggal:</strong>
                                        {{ date('d-m-Y', strtotime($absen->tanggal)) }}</p>
                                    <p><i class="bi bi-card-text me-1 text-primary"></i><strong>Keterangan:</strong>
                                        {{ $absen->keterangan }}</p>
                                    <p><i class="bi bi-box-arrow-in-right me-1 text-success"></i><strong>Masuk:</strong>
                                        {{ $absen->masuk }}</p>
                                    <p><i class="bi bi-box-arrow-left me-1 text-danger"></i><strong>Keluar:</strong>
                                        {{ $absen->keluar }}</p>
                                    <hr>
                                    <p><i class="bi bi-stopwatch me-1 text-warning"></i><strong>Norm M:</strong>
                                        {{ $absen->norm_m }}</p>
                                    <p><i class="bi bi-stopwatch me-1 text-warning"></i><strong>Norm K:</strong>
                                        {{ $absen->norm_k }}</p>
                                    <p><i class="bi bi-clock-history me-1 text-info"></i><strong>Menit DT:</strong>
                                        {{ $absen->mnt_dt }}</p>
                                    <p><i class="bi bi-clock-history me-1 text-info"></i><strong>Menit IPA:</strong>
                                        {{ $absen->mnt_ipa }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Ringkasan --}}
            <div class="card shadow-sm border-0 mt-3 rounded-4">
                <div class="card-body p-3 small">
                    <div class="d-flex justify-content-between">
                        <span><i class="bi bi-clock-history text-info me-1"></i>Jumlah Menit IPA</span>
                        <span class="fw-bold">{{ $jumlah_mnt_dt }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span><i class="bi bi-clock-history text-info me-1"></i>Jumlah Menit MDT</span>
                        <span class="fw-bold">{{ $jumlah_mnt_ipa }}</span>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between">
                        <span><i class="bi bi-calendar-check text-success me-1"></i>Jumlah Hari IPA</span>
                        <span class="fw-bold">{{ $jumlah_hari_dt }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span><i class="bi bi-calendar-check text-success me-1"></i>Jumlah Hari MDT</span>
                        <span class="fw-bold">{{ $jumlah_hari_ipa }}</span>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-warning text-center rounded-4 shadow-sm">
                Tidak ada data yang tersedia.
            </div>
        @endif

        {{-- Keterangan --}}
        <div class="card shadow-sm border-0 mt-3 rounded-4 p-3 small bg-light">
            <span class="fw-bold">Keterangan :</span>
            <ul class="mb-0">
                <li><strong>MDT</strong> = Masuk Datang Terlambat</li>
                <li><strong>IPA</strong> = Izin Pulang Awal</li>
            </ul>
        </div>

    </div>
@endsection
