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
            [
                'name_uz' => "🍎 Oziq-ovqatlar",
                'name_ru' => "🍎 Продукты питания",
                'name_en' => "🍎 Foods"
            ],
            [
                'name_uz' => "👕 Kiyim-kechaklar",
                'name_ru' => "👕 Одежда",
                'name_en' => "👕 Clothes"
            ],
            [
                'name_uz' => "🪑 Uy jihozlari",
                'name_ru' => "🪑 Товары для дома",
                'name_en' => "🪑 Home goods"
            ],
            [
                'name_uz' => "📱 Elektronika",
                'name_ru' => "📱 Электроника",
                'name_en' => "📱 Electronics"
            ],
            [
                'name_uz' => "🧸 Bolalar uchun",
                'name_ru' => "🧸 Для детей",
                'name_en' => "🧸 For children"
            ],
            [
                'name_uz' => "⏳ Muddati tugayotgan mahsulotlar",
                'name_ru' => "⏳ Продукты с истекающим сроком годности",
                'name_en' => "⏳ Expiring products"
            ],
            [
                'name_uz' => "📚 Kitoblar va o'quv qurollari",
                'name_ru' => "📚 Книги и учебные принадлежности",
                'name_en' => "📚 Books and stationery"
            ],
            [
                'name_uz' => "💊 Dorilar",
                'name_ru' => "💊 Лекарства",
                'name_en' => "💊 Medicines"
            ],
            [
                'name_uz' => "📦 Boshqa",
                'name_ru' => "📦 Другое",
                'name_en' => "📦 Other"
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
