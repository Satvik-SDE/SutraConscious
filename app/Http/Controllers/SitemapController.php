<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = [
            ['loc' => route('home'), 'changefreq' => 'weekly', 'priority' => '1.0'],
            ['loc' => route('shop'), 'changefreq' => 'daily', 'priority' => '0.9'],
            ['loc' => route('about'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => route('contact'), 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => route('shipping-returns'), 'changefreq' => 'monthly', 'priority' => '0.4'],
            ['loc' => route('privacy'), 'changefreq' => 'yearly', 'priority' => '0.3'],
            ['loc' => route('terms'), 'changefreq' => 'yearly', 'priority' => '0.3'],
        ];

        foreach (Category::where('is_active', true)->get() as $cat) {
            $urls[] = [
                'loc' => route('category.show', $cat->slug),
                'lastmod' => $cat->updated_at?->toIso8601String(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        }

        foreach (Product::where('is_active', true)->get() as $product) {
            $urls[] = [
                'loc' => route('product.show', $product->slug),
                'lastmod' => $product->updated_at?->toIso8601String(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap-0.9">' . "\n";
        foreach ($urls as $u) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>' . htmlspecialchars($u['loc']) . "</loc>\n";
            if (! empty($u['lastmod'])) {
                $xml .= '    <lastmod>' . $u['lastmod'] . "</lastmod>\n";
            }
            $xml .= '    <changefreq>' . $u['changefreq'] . "</changefreq>\n";
            $xml .= '    <priority>' . $u['priority'] . "</priority>\n";
            $xml .= "  </url>\n";
        }
        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
