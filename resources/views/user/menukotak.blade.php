<div class="info-presensi mt-3 text-muted">
    <div class="rounded-4 p-3 pb-1 mx-3"
        style="background-color: rgba(255, 255, 255, 0.968); 
               box-shadow: 0 2px 4px rgba(0, 111, 239, 0.326); 
               border-bottom-left-radius: 2%; 
               border-bottom-right-radius: 2%;">

        @include('user.geolocation01')

        <hr>

        <div class="row text-center text-muted">
            @php
                $colors = [
                    '0, 123, 255', // biru
                    '40, 167, 69', // hijau
                    '220, 53, 69', // merah
                    '255, 193, 7', // kuning
                    '7, 255, 44', // hijau neon
                    '255, 7, 28', // merah neon
                ];
            @endphp

            @foreach ($rekap as $jenis => $data)
                @php
                    $total = count($rekap);
                    $isFirst = $loop->first;
                    $isLast = $loop->last;
                    $color = $colors[$loop->index % count($colors)];

                    // Mapping icon
                    $icons = [
                        'SD' => 'bi bi-hospital', // Dokter
                        'IC' => 'bi bi-airplane', // Cuti
                        'Mangkir' => 'bi bi-x-circle', // Tidak absen
                    ];
                    $icon = $icons[$jenis] ?? 'bi bi-file-earmark-text'; // Default Surat
                @endphp

                <div class="col stat-box shadow-sm position-relative" data-bs-toggle="modal"
                    data-bs-target="#modal{{ $loop->iteration }}"
                    style="cursor: pointer; transition: transform 0.2s;
                           background: linear-gradient(to top, rgba({{ $color }}, 0.3), transparent);
                           {{ $isFirst ? 'border-bottom-left-radius: 10px;' : '' }}
                           {{ $isLast ? 'border-bottom-right-radius: 10px;' : '' }}">

                    <!-- Icon samar sebagai background -->
                    <i class="{{ $icon }} stat-bg-icon"></i>

                    <!-- Konten di atas icon -->
                    <div class="position-relative">
                        <p class="mb-0 fw-semibold">{{ $jenis == 'Mangkir' ? 'MK' : $jenis }}</p>
                        <b class="">{{ $data['jumlah'] }}</b>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="modal{{ $loop->iteration }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content shadow-lg border-0 rounded-4"
                            style="background-color: rgba(255, 255, 255, 0.858)">
                            <div class="modal-header rounded-top-4">
                                <h5 class="modal-title">Rincian {{ $jenis }}</h5>
                                <button type="button" class="btn-close btn-close-secondary"
                                    data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <ul class="list-group list-group-flush">
                                    @foreach ($data['tanggal'] as $row)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="bi bi-calendar-event me-2 text-primary"></i>
                                                {{ $row['hari'] }}</span>
                                            <span class="fw-bold">{{ $row['tgl'] }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary rounded-pill px-4"
                                    data-bs-dismiss="modal">
                                    <i class="bi bi-x-circle"></i> Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <style>
        .stat-box {
            padding: 1rem;
            transition: transform 0.3s, box-shadow 0.3s;
            overflow: hidden;
            border-radius: 0;
        }

        .stat-box:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .stat-box p {
            margin: 0;
            font-size: 14px;
            font-weight: 500;
            color: #0077ff78;
        }

        .stat-box b {
            font-size: 25px;
            font-weight: 900;
            color: #000000d8;

        }

        /* Icon samar di background */
        .stat-bg-icon {
            position: absolute;
            font-size: 4rem;
            opacity: 0.05;
            right: 10px;
            /* bottom: 0.1px; */
            pointer-events: none;
        }
    </style>
</div>
