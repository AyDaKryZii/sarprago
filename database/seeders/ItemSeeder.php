<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'Laptop' => [
                ['Acer', 'Laptop Acer Aspire 5'],
                ['Asus', 'Laptop Asus VivoBook'],
                ['Asus', 'Laptop Asus ROG Strix'],
                ['Lenovo', 'Laptop Lenovo ThinkPad'],
                ['HP', 'Laptop HP Pavilion'],
            ],

            'Komputer PC' => [
                ['Rakitan', 'PC Lab Multimedia'],
                ['Rakitan', 'PC Lab TKJ'],
                ['Dell', 'PC Dell Optiplex'],
            ],

            'Proyektor' => [
                ['Epson', 'Proyektor Epson X500'],
                ['BenQ', 'Proyektor BenQ MS550'],
            ],

            'Printer' => [
                ['Canon', 'Printer Canon G2010'],
                ['Epson', 'Printer Epson L3210'],
                ['HP', 'Printer HP DeskJet'],
            ],

            'Kamera' => [
                ['Canon', 'Kamera Canon EOS 3000D'],
                ['Nikon', 'Kamera Nikon D3500'],
            ],

            'Speaker' => [
                ['Polytron', 'Speaker Polytron PAS'],
                ['JBL', 'Speaker JBL Partybox'],
            ],

            'Router & Access Point' => [
                ['Mikrotik', 'Router Mikrotik RB750'],
                ['TP-Link', 'Access Point TP-Link EAP'],
            ],

            'Multimeter' => [
                ['Sanwa', 'Multimeter Sanwa Digital'],
                ['Fluke', 'Multimeter Fluke 101'],
            ],
        ];

        foreach ($data as $categoryName => $items) {

            $category = Category::where('name', $categoryName)->first();

            if (! $category) {
                continue;
            }

            $categoryPrefix = Str::upper(
                Str::substr(Str::slug($categoryName, ''), 0, 3)
            );

            $brandCounter = [];

            foreach ($items as $itemData) {

                [$brand, $name] = $itemData;

                $brandPrefix = Str::upper(
                    Str::substr(Str::slug($brand, ''), 0, 3)
                );

                $basePrefix = "{$categoryPrefix}-{$brandPrefix}";

                if (! isset($brandCounter[$basePrefix])) {
                    $brandCounter[$basePrefix] = 1;
                    $finalPrefix = $basePrefix;
                } else {
                    $brandCounter[$basePrefix]++;

                    $finalPrefix = sprintf(
                        "%s-%03d",
                        $basePrefix,
                        $brandCounter[$basePrefix]
                    );
                }

                Item::create([
                    'category_id' => $category->id,
                    'name'        => $name,
                    'brand'       => $brand,
                    'description' => $name . ' untuk peminjaman',
                    'code_prefix' => $finalPrefix,
                ]);
            }
        }
    }
}
