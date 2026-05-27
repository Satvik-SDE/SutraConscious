<?php
/**
 * Downscale and recompress product images in storage/app/public/products/*.
 *
 * - Max dimension: 1400px (preserves aspect ratio)
 * - Saves as JPG at 85% quality for photographic content
 * - Updates product_images.path in the database to the new .jpg filename
 *
 * Run with:  php scripts/optimize-product-images.php
 */

require __DIR__ . '/../vendor/autoload.php';

/** @var \Illuminate\Foundation\Application $app */
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

if (! extension_loaded('gd')) {
    fwrite(STDERR, "GD extension is not loaded.\n");
    exit(1);
}

$root = __DIR__ . '/../storage/app/public/products';
$maxDimension = 1400;
$jpegQuality  = 82;

if (! is_dir($root)) {
    fwrite(STDERR, "Products folder not found: {$root}\n");
    exit(1);
}

$updatedRows = 0;
$savedBytes  = 0;

foreach (glob($root . '/*', GLOB_ONLYDIR) as $productDir) {
    $slug = basename($productDir);
    echo "→ {$slug}\n";

    foreach (glob($productDir . '/*.{png,PNG,jpg,JPG,jpeg,JPEG,webp,WEBP}', GLOB_BRACE) as $file) {
        $relativeOld = 'products/' . $slug . '/' . basename($file);
        $info        = @getimagesize($file);
        if (! $info) {
            echo "   ! Skipped (not an image): {$relativeOld}\n";
            continue;
        }

        [$w, $h, $type] = $info;
        $sourceMime = $info['mime'] ?? '';
        $originalSize = filesize($file);

        $scale = min(1, $maxDimension / max($w, $h));
        $newW  = (int) round($w * $scale);
        $newH  = (int) round($h * $scale);

        $src = match ($type) {
            IMAGETYPE_PNG  => imagecreatefrompng($file),
            IMAGETYPE_JPEG => imagecreatefromjpeg($file),
            IMAGETYPE_WEBP => imagecreatefromwebp($file),
            default        => null,
        };

        if (! $src) {
            echo "   ! Could not read: {$relativeOld}\n";
            continue;
        }

        $dst = imagecreatetruecolor($newW, $newH);
        $white = imagecolorallocate($dst, 251, 250, 246);
        imagefilledrectangle($dst, 0, 0, $newW, $newH, $white);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $w, $h);
        imagedestroy($src);

        $baseName  = pathinfo($file, PATHINFO_FILENAME);
        $newFile   = $productDir . '/' . $baseName . '.jpg';
        $relativeNew = 'products/' . $slug . '/' . $baseName . '.jpg';

        imagejpeg($dst, $newFile, $jpegQuality);
        imagedestroy($dst);

        if (strtolower($file) !== strtolower($newFile) && file_exists($file)) {
            @unlink($file);
        }

        $newSize = filesize($newFile);
        $savedBytes += max(0, $originalSize - $newSize);

        $rowsUpdated = App\Models\ProductImage::query()
            ->where('path', $relativeOld)
            ->update(['path' => $relativeNew]);
        $updatedRows += $rowsUpdated;

        printf("   %s  %dx%d → %dx%d  %s → %s%s\n",
            $baseName,
            $w, $h, $newW, $newH,
            human($originalSize), human($newSize),
            $rowsUpdated ? "  (db updated)" : ($relativeOld === $relativeNew ? "" : "  (no db row)")
        );
    }
}

printf("\nDone. %d DB rows updated. %.1f MB saved.\n", $updatedRows, $savedBytes / 1024 / 1024);

function human(int $bytes): string {
    return $bytes >= 1024 * 1024
        ? sprintf('%.1f MB', $bytes / 1024 / 1024)
        : sprintf('%.0f KB', $bytes / 1024);
}
