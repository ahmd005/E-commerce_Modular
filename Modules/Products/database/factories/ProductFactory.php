<?php

namespace Modules\Products\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Products\Models\Product;
use Modules\Products\Models\Category;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    protected static ?array $cachedCategoryIds = null;

    protected static function categoryIds(): array
    {
        if (static::$cachedCategoryIds === null) {
            static::$cachedCategoryIds = Category::pluck('id')->all();
        }

        return static::$cachedCategoryIds;
    }

    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'category_id' => $this->faker->randomElement(static::categoryIds()),
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'image' => null,
        ];
    }
}