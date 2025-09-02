@extends('user.ly')

@section('content')
    <div class="container mt-3">
        <div class="align-items-center">

            {{-- <p>{{ $datatdkmsk }}</p> --}}
            <div class="row">
                <div class="col-7  d-flex align-items-center">
                    {{-- @if (Auth::user()->foto)
                        <!-- Foto profil -->
                        <img src="{{ asset('storage/foto/' . Auth::user()->foto) }}" alt="Foto Profil"
                            class="rounded-circle me-3" width="60" height="60">
                    @else --}}
                    <!-- Avatar default huruf awal -->
                    <div class="rounded-circle bg-light text-secondary d-flex align-items-center justify-content-center me-2 "
                        style="width:40px; height:40px; font-size:24px; font-weight:500;">
                        {{ strtoupper(substr(Auth::user()->nama_asli, 0, 1)) }}
                    </div>
                    {{-- @endif --}}

                    <!-- Info user -->
                    <div>
                        @php
                            $nameParts = explode(' ', Auth::user()->nama_asli); // pecah per kata
                            $shortName = '';

                            foreach ($nameParts as $index => $part) {
                                if ($index < 2) {
                                    // dua kata pertama tampil penuh
                                    $shortName .= $part . ' ';
                                } else {
                                    // kata ke-3 dst hanya huruf pertama
                                    $shortName .= strtoupper(substr($part, 0, 1)) . ' ';
                                }
                            }

                            $shortName = trim($shortName); // hapus spasi akhir
                            $shortName = strtoupper($shortName); // ubah semua jadi kapital

                            // Batasi maksimal 10 karakter
                            if (strlen($shortName) > 11) {
                                $shortName = substr($shortName, 0, 11) . '..';
                            }
                        @endphp

                        <p class="mb-0 fw-bold text-light" style=" font-weight: 300;">
                            {{ $shortName }}</p>

                        <b class="m-0 text-light" style="font-weight: 80;">
                            {{-- {{ Auth::user()->jabatan }}
                            <br> --}}
                            {{ strtoupper(Auth::user()->no_payroll) }}

                        </b>
                    </div>
                </div>
                <div class="col-5 d-flex align-items-start   justify-content-start">

                    <span
                        style="
                        font-size: 1.7rem;
                        font-weight: 300;
                        color: #ffffff;
                        text-shadow: 2px 2px 6px rgba(0,0,0,0.5);
                    ">
                        <span id="jamSekarang"></span>
                    </span>


                    {{-- @include('user.geolocation') --}}

                </div>

            </div>

        </div>
    </div>
    @include('user.menukotak')
    @include('user.menuutama')
@endsection
