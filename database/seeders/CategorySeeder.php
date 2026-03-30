<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Laptop',
            'Komputer PC',
            'Proyektor',
            'Printer',
            'Scanner',
            'Kamera',
            'Tripod',
            'Microphone',
            'Speaker',
            'Mixer Audio',
            'Kabel & Adaptor',
            'Router & Access Point',
            'Switch Hub',
            'Harddisk & Flashdisk',
            'Toolkit / Perkakas',
            'Alat Kebersihan',
            'Meja',
            'Kursi',
            'Papan Tulis',
            'Alat Olahraga',
            'Alat Praktikum',
            'Multimeter',
            'Solder & Peralatan Elektronik',
            'Mesin & Peralatan Bengkel',
            'Lainnya',
        ];

        foreach ($categories as $name) {
            Category::create([
                'name' => $name,
            ]);
        }
    }
}
