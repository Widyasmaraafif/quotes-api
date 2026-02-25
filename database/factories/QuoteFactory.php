<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;

class QuoteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'quote' => fake()->sentence(12),
            'author' => fake()->name(),
            'category_id' => Category::inRandomOrder()->first()->id,
        ];
    }
}
