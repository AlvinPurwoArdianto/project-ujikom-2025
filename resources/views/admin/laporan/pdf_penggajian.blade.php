<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Absensi</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <h1>Laporan Absensi</h1>
    <p>Periode: {{ request('tanggal_awal') }} - {{ request('tanggal_akhir') }}</p>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pegawai</th>
                <th>Tanggal Gajian</th>
                <th>Jumlah Gaji Pokok</th>
                <th>Jumlah Potongan</th>
                <th>Jumlah Bonus</th>
                <th>Jumlah Gaji Bersih</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($penggajian as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->pegawai ? $item->pegawai->nama_pegawai : 'Tidak Ada Nama' }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal_gaji)->translatedFormat('d F Y') }}
                    </td>
                    <td>Rp {{ number_format($item->jumlah_gaji, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($item->potongan, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($item->bonus, 0, ',', '.') }}</td>
                    <td>Rp
                        {{ number_format($item->jumlah_gaji + $item->bonus - $item->potongan, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
