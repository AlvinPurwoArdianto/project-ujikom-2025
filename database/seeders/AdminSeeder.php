<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'nama_pegawai'   => 'Admin',
            'google_id'      => null,
            'email'          => 'admin@gmail.com',
            'password'       => bcrypt('12345678'),
            'is_admin'       => 1,
            'status_pegawai' => 1,
        ]);
    }
}
