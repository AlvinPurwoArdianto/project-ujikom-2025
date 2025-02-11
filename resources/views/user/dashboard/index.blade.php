@extends('layouts.user.template')

@section('content')
    <style>
        .card {
            border-radius: 10px;
        }

        .card-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: bold;
        }

        .list-unstyled li {
            padding: 10px 0;
        }
    </style>
    <div class="container-fluid py-4">
        <div class="row mt-4">
            <!-- Card Selamat Datang -->
            <div class="col-lg-4 mb-lg-0 mb-4">
                <div class="card z-index-2 h-100 shadow-sm border-0">
                    <div class="card-header pb-0 pt-4 bg-gradient-primary text-white rounded-top">
                        <h4 class="text-capitalize mb-4 text-white">Selamat Datang, {{ Auth::user()->nama_pegawai }}</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-5">
                            <li class="text-sm mb-2 d-flex align-items-center">
                                <i class="fas fa-info-circle me-2 text-primary"></i>
                                <span class="font-weight-bold">Status:</span>
                                <span
                                    class="ms-1">{{ Auth::user()->status_pegawai == 1 ? 'Aktif' : 'Tidak Aktif' }}</span>
                            </li>
                            <li class="text-sm mb-2 d-flex align-items-center">
                                <i class="fas fa-briefcase me-2 text-primary"></i>
                                <span class="font-weight-bold">Jabatan:</span>
                                <span class="ms-1">{{ Auth::user()->jabatan->nama_jabatan ?? 'Tidak ada jabatan' }}</span>
                            </li>
                            <li class="text-sm d-flex align-items-center">
                                <i class="fas fa-calendar-alt me-2 text-primary"></i>
                                <span class="font-weight-bold">Tanggal Masuk:</span>
                                <span class="ms-1">{{ Auth::user()->tanggal_masuk }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>


            <!-- Kalender -->
            <div class="col-lg-8">
                <div class="card overflow-hidden h-100 p-0">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0">Kalender</h5>
                        <div class="dropdown">
                            <button id="prevMonth" class="btn btn-outline-primary btn-sm me-2">← Prev</button>
                            <button id="nextMonth" class="btn btn-outline-primary btn-sm">Next →</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="calendar" class="table-responsive">
                            <!-- Kalender akan dirender di sini oleh JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const calendar = document.getElementById('calendar');
        let currentDate = new Date();
        const today = new Date();
        const absensi = @json($absensi);
        const tanggalMasuk = @json($tanggal_masuk);

        function renderCalendar(date) {
            const month = date.getMonth();
            const year = date.getFullYear();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const firstDayIndex = new Date(year, month, 1).getDay();

            // Nama bulan
            const monthNames = [
                "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                "Juli", "Agustus", "September", "Oktober", "November", "Desember"
            ];

            // Menampilkan bulan dan tahun
            const monthYearHTML = `<h3 class="text-center">${monthNames[month]} ${year}</h3>`;

            let calendarHTML = `<table class="table table-bordered text-center mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Minggu</th>
                            <th>Senin</th>
                            <th>Selasa</th>
                            <th>Rabu</th>
                            <th>Kamis</th>
                            <th>Jumat</th>
                            <th>Sabtu</th>
                        </tr>
                    </thead>
                    <tbody>`;

            let day = 1;
            for (let i = 0; i < 6; i++) {
                calendarHTML += '<tr>';
                for (let j = 0; j < 7; j++) {
                    if (i === 0 && j < firstDayIndex) {
                        calendarHTML += '<td></td>';
                    } else if (day > daysInMonth) {
                        calendarHTML += '<td></td>';
                    } else {
                        const currentDateString =
                            `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                        const dateToCheck = new Date(currentDateString);
                        const isToday = dateToCheck.toDateString() === today.toDateString();
                        const isFutureDate = dateToCheck > today;
                        const isBeforeTanggalMasuk = tanggalMasuk && currentDateString < tanggalMasuk;

                        let cellClass = '';
                        let statusText = '';

                        // Cek apakah hari tersebut adalah hari Minggu
                        const isSunday = dateToCheck.getDay() === 0; // 0 adalah hari Minggu

                        if (isToday) {
                            cellClass = 'bg-primary text-white';
                            statusText = 'Hari Ini';
                        } else if (isFutureDate || isBeforeTanggalMasuk) {
                            cellClass = 'bg-white';
                            statusText = '';
                        } else if (isSunday) {
                            cellClass = 'bg-danger'; // Hari Minggu background putih
                            statusText = 'Libur'; // Status untuk hari Minggu
                        } else {
                            const absensiData = absensi.find(item => item.tanggal_absen === currentDateString);

                            if (absensiData) {
                                if (absensiData.status === 'Hadir') {
                                    cellClass = 'bg-success text-white';
                                    statusText = 'Hadir';
                                } else if (absensiData.status === 'Sakit') {
                                    cellClass = 'bg-warning text-white';
                                    statusText = 'Sakit';
                                } else if (absensiData.status === 'Telat') {
                                    cellClass = 'bg-warning text-white';
                                    statusText = 'Telat';
                                }
                            } else {
                                // Hanya menampilkan "Alfa" jika bukan hari Minggu
                                if (!isSunday) {
                                    cellClass = 'bg-secondary text-white';
                                    statusText = 'Alfa';
                                }
                            }
                        }

                        calendarHTML += `
                <td class="${cellClass}">
                    ${day}<br>
                    <small>${statusText}</small>
                </td>`;
                        day++;
                    }
                }
                calendarHTML += '</tr>';
            }
            calendarHTML += '</tbody></table>';

            // Gabungkan bulan dan tahun dengan kalender
            calendar.innerHTML = monthYearHTML + calendarHTML;
        }

        renderCalendar(currentDate);

        document.getElementById('prevMonth').addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar(currentDate);
        });

        document.getElementById('nextMonth').addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar(currentDate);
        });
    </script>
@endsection
