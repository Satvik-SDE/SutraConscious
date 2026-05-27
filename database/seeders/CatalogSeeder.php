<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    /**
     * Seeds two collections and a set of placeholder products using the
     * provided reference imagery from references/Product Catalogue Photos/.
     * Names are placeholders that the admin can rename and re-price in Filament.
     */
    public function run(): void
    {
        $collections = [
            [
                'name' => 'First Collection',
                'slug' => 'first-collection',
                'description' => 'Our debut release — modern cuts in 100% premium cotton, woven for everyday wear.',
                'sort_order' => 10,
                'source_dir' => base_path('references/Product Catalogue Photos/First collection launch'),
                'base_price' => 1499,
                'name_prefix' => 'Sutra No.',
                'is_featured' => true,
            ],
            [
                'name' => 'The Solids Edition',
                'slug' => 'the-solids-edition',
                'description' => 'Versatile solid cotton kurtas. The wardrobe foundation.',
                'sort_order' => 20,
                'source_dir' => base_path('references/Product Catalogue Photos/Solid collection'),
                'base_price' => 1299,
                'name_prefix' => 'Solid Sutra No.',
                'is_featured' => false,
            ],
        ];

        $sizes = ['S', 'M', 'L', 'XL'];

        foreach ($collections as $collectionData) {
            $category = Category::updateOrCreate(
                ['slug' => $collectionData['slug']],
                [
                    'name' => $collectionData['name'],
                    'description' => $collectionData['description'],
                    'sort_order' => $collectionData['sort_order'],
                    'is_active' => true,
                ],
            );

            if (! is_dir($collectionData['source_dir'])) {
                $this->command->warn("Skipping {$collectionData['name']}: source folder missing at {$collectionData['source_dir']}");
                continue;
            }

            $files = collect(File::files($collectionData['source_dir']))
                ->filter(fn ($f) => in_array(strtolower($f->getExtension()), ['png', 'jpg', 'jpeg', 'webp']))
                ->sortBy(fn ($f) => $f->getFilename())
                ->values();

            foreach ($files as $index => $file) {
                $num = $index + 1;
                $name = $collectionData['name_prefix'] . ' ' . str_pad((string) $num, 2, '0', STR_PAD_LEFT);
                $slug = Str::slug($collectionData['slug'] . '-' . str_pad((string) $num, 2, '0', STR_PAD_LEFT));

                $product = Product::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'category_id' => $category->id,
                        'name' => $name,
                        'short_description' => '100% Premium Cotton kurta. Breathable, soft, soil-to-soil.',
                        'fabric' => '100% Premium Cotton',
                        'sleeve' => 'Full Sleeve',
                        'base_price' => $collectionData['base_price'],
                        'currency' => 'INR',
                        'is_active' => true,
                        'is_featured' => $collectionData['is_featured'] && $num <= 3,
                        'sort_order' => $num,
                    ],
                );

                foreach ($sizes as $sizeIndex => $size) {
                    ProductVariant::updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'size' => $size,
                            'color' => null,
                        ],
                        [
                            'sku' => Str::upper(Str::substr($collectionData['slug'], 0, 3)) . '-' . str_pad((string) $num, 2, '0', STR_PAD_LEFT) . '-' . $size,
                            'stock' => 5,
                            'is_active' => true,
                        ],
                    );
                }

                $destDir = 'products/' . $product->slug;
                $destPath = $destDir . '/' . $file->getFilename();
                Storage::disk('public')->putFileAs($destDir, $file->getRealPath(), $file->getFilename());

                ProductImage::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'path' => $destPath,
                    ],
                    [
                        'alt' => $name . ' — Sutra Conscious',
                        'sort_order' => 1,
                        'is_primary' => true,
                    ],
                );
            }
        }
    }
}
