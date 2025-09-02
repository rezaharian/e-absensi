@extends('user.ly')

@section('content')
    <div class="container py-3">
        {{-- Judul --}}
        <div class="text-center mb-3">
            <h5 class="fw-bold mb-1"><i class="bi bi-person-badge"></i> Detail Absensi</h5>
            <small class="text-muted">Informasi absensi karyawan</small>
        </div>

        {{-- Card Utama --}}
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-3">

                {{-- Profil --}}
                <div class="d-flex align-items-center mb-3">
                    <img src="https://ui-avatars.com/api/?name={{ $attendance->user->name }}&background=0D8ABC&color=fff"
                        alt="Foto Profil" class="rounded-circle me-2" width="55" height="55">
                    <div>
                        <h6 class="mb-0 fw-bold">{{ $attendance->user->nama_asli }}</h6>
                        <small class="text-muted"><i class="bi bi-briefcase"></i>
                            {{ $attendance->user->jabatan ?? 'Karyawan' }}
                        </small>
                    </div>
                </div>

                {{-- Detail Info --}}
                <ul class="list-group list-group-flush small mb-3">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-2 py-2">
                        <span><i class="bi bi-fingerprint text-primary"></i> Jenis</span>
                        <span class="fw-semibold text-dark text-uppercase">
                            {{ $attendance->type == 'in' ? 'Check-In' : 'Check-Out' }}
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-2 py-2">
                        <span><i class="bi bi-calendar-event text-success"></i> Tanggal</span>
                        <span class="fw-semibold">
                            {{ \Carbon\Carbon::parse($attendance->time)->format('d M Y') }}
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-2 py-2">
                        <span><i class="bi bi-clock text-warning"></i> Jam</span>
                        <span class="fw-semibold">
                            {{ \Carbon\Carbon::parse($attendance->time)->format('H:i') }}
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-2 py-2">
                        <span><i class="bi bi-geo-alt text-danger"></i> Lokasi</span>
                        <small class="fw-semibold text-truncate" style="max-width: 200px;">
                            {{ $attendance->address }}
                        </small>
                    </li>
                </ul>

                {{-- Foto Absensi --}}
                <div class="text-center">
                    <h6 class="fw-bold mb-2"><i class="bi bi-camera"></i> Foto Absensi</h6>
                    <img src="{{ asset('storage/' . $attendance->photo) }}" alt="Foto Absensi"
                        class="img-fluid rounded-3 shadow-sm border" style="max-height: 220px; object-fit: cover;">
                </div>

                {{-- Tombol --}}
                <div class="mt-3 text-center">
                    <a href="{{ route('attendance.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
