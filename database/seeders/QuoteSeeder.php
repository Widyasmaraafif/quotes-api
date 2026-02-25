<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Quote;
use App\Models\Category;

class QuoteSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data');
        $files = glob($path . '/*.json');

        foreach ($files as $file) {

            $categoryName = ucfirst(basename($file, '.json'));
            $data = json_decode(file_get_contents($file), true);

            $category = \App\Models\Category::firstOrCreate([
                'name' => $categoryName
            ]);

            foreach ($data as $item) {
                \App\Models\Quote::firstOrCreate(
                    [
                        'quote' => $item['quote'],
                        'author' => $item['author'],
                    ],
                    [
                        'category_id' => $category->id
                    ]
                );
            }
        }
    }
}
