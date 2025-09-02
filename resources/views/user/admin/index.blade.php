@extends('user.ly')

@section('content')
    <div class="container my-2 text-muted">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">
            <h6 class="fw-bold m-0 text-light">Aktifitas</h6>

            {{-- Filter --}}
            <form method="GET" action="{{ route('halamanadmin.index') }}" class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                {{-- Tahun --}}
                <div class="flex-grow-1">
                    <select name="tahun" class="form-select form-select-sm shadow-sm border-0">
                        @for ($t = date('Y'); $t >= date('Y') - 5; $t--)
                            <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>
                                {{ $t }}
                            </option>
                        @endfor
                    </select>
                </div>

                {{-- Bulan --}}
                <div class="flex-grow-1">
                    <select name="bulan" class="form-select form-select-sm shadow-sm border-0">
                        @for ($b = 1; $b <= 12; $b++)
                            <option value="{{ sprintf('%02d', $b) }}" {{ $bulan == sprintf('%02d', $b) ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($b)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                {{-- Tombol Filter --}}
                <div>
                    <button type="submit" class="btn btn-sm btn-outline-light shadow-sm d-flex align-items-center gap-1">
                        <i class="fa-solid fa-filter"></i>
                        <span class="d-none d-md-inline">Filter</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="alert alert-success text-center rounded-pill shadow-sm small py-1 mb-2 m-0">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger text-center rounded-pill shadow-sm small py-1 mb-2 m-0">
                {{ session('error') }}
            </div>
        @endif

        {{-- List Absensi --}}
        <div class="row g-2 rounded-4 p-3 pb-1 mx-1 mt-1"
            style="background-color: rgba(255, 255, 255, 0.968); 
                   box-shadow: 0 2px 4px rgba(0,0,0,0.08); 
                   border-bottom-left-radius: 2%; 
                   border-bottom-right-radius: 2%;">

            @forelse($presensis as $p)
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-3 m-0">
                        <div class="card-body d-flex justify-content-between align-items-center p-2 flex-wrap">

                            {{-- Foto & Info --}}
                            <div class="d-flex align-items-center mb-2 mb-md-0">
                                <div class="me-2">
                                    @if ($p->photo)
                                        <img src="{{ asset('storage/' . $p->photo) }}" alt="Selfie"
                                            class="rounded-circle border" style="width:40px; height:40px; object-fit:cover;"
                                            onerror="this.onerror=null; this.style.display='none'; this.insertAdjacentHTML('afterend', '<i class=\'fa-regular fa-circle-user fa-2x text-muted\'></i>');">
                                    @else
                                        <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center"
                                            style="width:40px; height:40px;">
                                            <i class="fa-regular fa-circle-user text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="fw-bold small m-0">
                                        {{ $p->tanggal ? \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d F Y') : '-' }}
                                    </div>
                                    <small class="text-muted d-block">
                                        <i class="fa-solid fa-id-badge me-1"></i>
                                        {{ $p->no_payroll ?? '-' }} — {{ $p->nama_pegawai ?? '-' }}
                                    </small>
                                    <small class="text-muted">
                                        @if ($p->time)
                                            {{ \Carbon\Carbon::parse($p->time)->format('H:i') }} —
                                            <span class="{{ $p->type == 'in' ? 'text-success' : 'text-danger' }}">
                                                {{ $p->type == 'in' ? 'Check-In' : 'Check-Out' }}
                                            </span>
                                        @endif
                                    </small>
                                </div>
                            </div>

                            {{-- Detail --}}
                            <div class="text-end small">
                                <div><span class="text-muted">Masuk:</span>
                                    <span class="fw-semibold">{{ $p->masuk ?? '-' }}</span>
                                </div>
                                <div><span class="text-muted">Keluar:</span>
                                    <span class="fw-semibold">{{ $p->keluar ?? '-' }}</span>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-muted py-4">
                    <i class="fa-regular fa-calendar-xmark fa-2x d-block mb-2"></i>
                    <p class="small mb-0">Belum ada data absensi</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
