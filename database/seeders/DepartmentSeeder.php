<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            [
                'name' => "Men's Wear",
                'slug' => 'mens-wear',
                'description' => '100% cotton kurtas and everyday menswear — crafted in Bharat.',
                'sort_order' => 10,
            ],
            [
                'name' => "Women's Wear",
                'slug' => 'womens-wear',
                'description' => 'Thoughtfully cut cotton pieces for women — breathable, soft, made for every day.',
                'sort_order' => 20,
            ],
            [
                'name' => 'Kids — Girls',
                'slug' => 'kids-girls',
                'description' => 'Comfortable cotton clothing for girls — gentle on skin, made to move.',
                'sort_order' => 30,
            ],
            [
                'name' => 'Kids — Boys',
                'slug' => 'kids-boys',
                'description' => 'Durable cotton clothing for boys — easy wear, easy care, made in Bharat.',
                'sort_order' => 40,
                'size_chart_image_path' => 'size-charts/departments/kids-boys-size-guide.png',
            ],
        ];

        foreach ($departments as $data) {
            Department::updateOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, ['is_active' => true]),
            );
        }

        // Ensure kids-boys keeps its default size chart when re-seeded.
        Department::where('slug', 'kids-boys')->update([
            'size_chart_image_path' => 'size-charts/departments/kids-boys-size-guide.png',
        ]);

        $mensWear = Department::where('slug', 'mens-wear')->first();

        if ($mensWear) {
            Category::query()
                ->whereNull('department_id')
                ->update(['department_id' => $mensWear->id]);
        }
    }
}
