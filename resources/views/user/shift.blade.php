@extends('user.ly')

@section('content')
    <div class="container my-3  mt-5">
        {{-- Header --}}
        <div class="text-center mb-3">
            <h5 class="fw-bold text-light mb-1">JAM KERJA</h5>
            <small class="text-light">PT. EXTRUPACK</small>
        </div>

        {{-- Card List --}}
        <div class="row g-3">
            @foreach ($data as $shift => $jam)
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-bold text-dark mb-1">{{ $shift }}</h6>
                                <small class="text-muted">
                                    Masuk: <span class="fw-semibold text-success">{{ $jam['masuk'] }}</span><br>
                                    Pulang: <span class="fw-semibold text-danger">{{ $jam['pulang'] }}</span>
                                </small>
                            </div>
                            <div class="text-primary" style="font-size: 1.8rem;">
                                <i class="bi bi-clock-history"></i>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
