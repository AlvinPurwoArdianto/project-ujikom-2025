<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class KaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        User::create([
            'nama_pegawai'   => 'Sample Karyawan 1',
            'tempat_lahir'   => 'Palembang',
            'tanggal_lahir'  => '1999-10-10',
            'jenis_kelamin'  => 'Perempuan',
            'alamat'         => 'Jl. Contoh 10, Palembang',
            'email'          => 'sample1@gmail.com',
            'password'       => Hash::make('1234567890'),
            'tanggal_masuk'  => Carbon::now(),
            'gaji'           => 0,
            'status_pegawai' => 1,
            'is_admin'       => 0,
            'provinsi'       => 32,
            'kabupaten'      => 3204,
            'kecamatan'      => 3204140,
            'kelurahan'      => 3204140007,
        ]);

        // for ($i = 1; $i <= 1000; $i++) {
        //     User::create([
        //         'nama_pegawai'   => 'Sample Karyawan ' . $i,
        //         'tempat_lahir'   => $this->getTempatLahir($i),
        //         'tanggal_lahir'  => $this->getTanggalLahir($i),
        //         'jenis_kelamin'  => $i % 2 == 0 ? 'Perempuan' : 'Laki-laki',
        //         'alamat'         => 'Jl. Contoh ' . $i . ', Kota ' . $this->getTempatLahir($i),
        //         'email'          => 'sample' . $i . '@gmail.com',
        //         'password'       => Hash::make('1234567890'),
        //         'tanggal_masuk'  => Carbon::now(),
        //         'gaji'           => 0 + ($i * 50000),
        //         'status_pegawai' => 1,
        //         'is_admin'       => 0,
        //         'provinsi'       => 32,
        //         'kabupaten'      => 3204,
        //         'kecamatan'      => 3204140,
        //         'kelurahan'      => 3204140007,
        //     ]);
        // }
    }

    /**
     * Dummy function to get tempat lahir based on iteration index.
     */
    // private function getTempatLahir(int $index)
    // {
    //     $locations = ['Surabaya', 'Bandung', 'Jakarta', 'Yogyakarta', 'Medan', 'Makassar', 'Semarang', 'Bali', 'Palembang', 'Malang'];
    //     return $locations[$index % count($locations)];
    // }

    // /**
    //  * Dummy function to generate tanggal lahir based on iteration index.
    //  */
    // private function getTanggalLahir(int $index)
    // {
    //     return '199' . (3 + ($index % 7)) . '-0' . (1 + ($index % 9)) . '-1' . (1 + ($index % 9));
    // }
}
