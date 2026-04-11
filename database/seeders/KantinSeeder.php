<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vendor;
use App\Models\Menu;
use Illuminate\Support\Facades\Hash;

class KantinSeeder extends Seeder
{
    public function run(): void
    {
        // Buat 2 vendor contoh
        $vendor1 = Vendor::create([
            'nama_vendor' => 'Warung Bu Sari',
            'email'       => 'busari@kantin.com',
            'password'    => Hash::make('password123'),
        ]);

        $vendor2 = Vendor::create([
            'nama_vendor' => 'Kantin Pak Budi',
            'email'       => 'pakbudi@kantin.com',
            'password'    => Hash::make('password123'),
        ]);

        // Menu untuk Bu Sari
        $menusV1 = [
            ['nama_menu' => 'Nasi Goreng',        'harga' => 15000],
            ['nama_menu' => 'Mie Goreng',          'harga' => 13000],
            ['nama_menu' => 'Nasi Ayam Geprek',    'harga' => 18000],
            ['nama_menu' => 'Es Teh Manis',        'harga' => 5000],
            ['nama_menu' => 'Es Jeruk',            'harga' => 6000],
        ];

        foreach ($menusV1 as $m) {
            Menu::create(array_merge($m, ['idvendor' => $vendor1->idvendor]));
        }

        // Menu untuk Pak Budi
        $menusV2 = [
            ['nama_menu' => 'Soto Ayam',           'harga' => 14000],
            ['nama_menu' => 'Bakso Spesial',        'harga' => 16000],
            ['nama_menu' => 'Gado-Gado',            'harga' => 13000],
            ['nama_menu' => 'Teh Hangat',           'harga' => 4000],
            ['nama_menu' => 'Kopi Hitam',           'harga' => 5000],
        ];

        foreach ($menusV2 as $m) {
            Menu::create(array_merge($m, ['idvendor' => $vendor2->idvendor]));
        }

        $this->command->info('Data kantin berhasil di-seed!');
        $this->command->info('Login vendor 1: busari@kantin.com / password123');
        $this->command->info('Login vendor 2: pakbudi@kantin.com / password123');
    }
}
