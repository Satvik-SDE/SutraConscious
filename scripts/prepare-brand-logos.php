<?php

declare(strict_types=1);

$brandDir = dirname(__DIR__) . '/public/img/brand';
$sources = [
    $brandDir . '/logo-source.jpg',
    $brandDir . '/logo-new-1.png',
    $brandDir . '/logo-new-2.png',
];

function loadImage(string $path): GdImage
{
    $header = file_get_contents($path, false, null, 0, 4);

    if ($header === "\xFF\xD8\xFF\xE0" || $header === "\xFF\xD8\xFF\xE1") {
        $image = imagecreatefromjpeg($path);
    } elseif ($header === "\x89PNG") {
        $image = imagecreatefrompng($path);
    } else {
        throw new RuntimeException("Unsupported image format: {$path}");
    }

    if ($image === false) {
        throw new RuntimeException("Could not load image: {$path}");
    }

    imagesavealpha($image, true);

    return $image;
}

function savePng(GdImage $image, string $path): void
{
    if (! imagepng($image, $path, 9)) {
        throw new RuntimeException("Could not save PNG: {$path}");
    }
}

function makeTransparent(GdImage $source, int $threshold = 248): GdImage
{
    $width = imagesx($source);
    $height = imagesy($source);
    $target = imagecreatetruecolor($width, $height);

    if ($target === false) {
        throw new RuntimeException('Could not create target image.');
    }

    imagealphablending($target, false);
    imagesavealpha($target, true);

    $transparent = imagecolorallocatealpha($target, 255, 255, 255, 127);
    imagefilledrectangle($target, 0, 0, $width, $height, $transparent);

    for ($y = 0; $y < $height; $y++) {
        for ($x = 0; $x < $width; $x++) {
            $rgba = imagecolorat($source, $x, $y);
            $red = ($rgba >> 16) & 0xFF;
            $green = ($rgba >> 8) & 0xFF;
            $blue = $rgba & 0xFF;

            if ($red >= $threshold && $green >= $threshold && $blue >= $threshold) {
                continue;
            }

            $color = imagecolorallocatealpha($target, $red, $green, $blue, 0);
            imagesetpixel($target, $x, $y, $color);
        }
    }

    return $target;
}

function trimWhitespace(GdImage $source, int $threshold = 248): GdImage
{
    $width = imagesx($source);
    $height = imagesy($source);
    $minX = $width;
    $minY = $height;
    $maxX = 0;
    $maxY = 0;

    for ($y = 0; $y < $height; $y++) {
        for ($x = 0; $x < $width; $x++) {
            $rgba = imagecolorat($source, $x, $y);
            $alpha = ($rgba >> 24) & 0x7F;
            $red = ($rgba >> 16) & 0xFF;
            $green = ($rgba >> 8) & 0xFF;
            $blue = $rgba & 0xFF;

            $isBackground = $alpha === 127 || ($red >= $threshold && $green >= $threshold && $blue >= $threshold);

            if ($isBackground) {
                continue;
            }

            $minX = min($minX, $x);
            $minY = min($minY, $y);
            $maxX = max($maxX, $x);
            $maxY = max($maxY, $y);
        }
    }

    if ($maxX <= $minX || $maxY <= $minY) {
        return $source;
    }

    $padding = 8;
    $minX = max(0, $minX - $padding);
    $minY = max(0, $minY - $padding);
    $maxX = min($width - 1, $maxX + $padding);
    $maxY = min($height - 1, $maxY + $padding);
    $cropWidth = $maxX - $minX + 1;
    $cropHeight = $maxY - $minY + 1;

    $cropped = imagecreatetruecolor($cropWidth, $cropHeight);
    imagealphablending($cropped, false);
    imagesavealpha($cropped, true);
    $transparent = imagecolorallocatealpha($cropped, 255, 255, 255, 127);
    imagefilledrectangle($cropped, 0, 0, $cropWidth, $cropHeight, $transparent);
    imagecopy($cropped, $source, 0, 0, $minX, $minY, $cropWidth, $cropHeight);

    return $cropped;
}

function stripSolidLeftEdgeBar(GdImage $source, float $darkRatioThreshold = 0.98, int $maxRgb = 50): GdImage
{
    $width = imagesx($source);
    $height = imagesy($source);
    $cropFromX = 0;

    for ($x = 0; $x < $width; $x++) {
        $darkPixels = 0;

        for ($y = 0; $y < $height; $y++) {
            $rgba = imagecolorat($source, $x, $y);
            $alpha = ($rgba >> 24) & 0x7F;
            $red = ($rgba >> 16) & 0xFF;
            $green = ($rgba >> 8) & 0xFF;
            $blue = $rgba & 0xFF;

            if ($alpha !== 127 && $red <= $maxRgb && $green <= $maxRgb && $blue <= $maxRgb) {
                $darkPixels++;
            }
        }

        if (($darkPixels / $height) >= $darkRatioThreshold) {
            $cropFromX = $x + 1;

            continue;
        }

        break;
    }

    if ($cropFromX <= 0) {
        return $source;
    }

    $cropWidth = $width - $cropFromX;
    $cropped = imagecreatetruecolor($cropWidth, $height);
    imagealphablending($cropped, false);
    imagesavealpha($cropped, true);
    imagecopy($cropped, $source, 0, 0, $cropFromX, 0, $cropWidth, $height);

    return $cropped;
}

function trimSourceMargins(GdImage $source, int $threshold = 248): GdImage
{
    $width = imagesx($source);
    $height = imagesy($source);
    $minX = $width;
    $minY = $height;
    $maxX = 0;
    $maxY = 0;

    for ($y = 0; $y < $height; $y++) {
        for ($x = 0; $x < $width; $x++) {
            $rgba = imagecolorat($source, $x, $y);
            $red = ($rgba >> 16) & 0xFF;
            $green = ($rgba >> 8) & 0xFF;
            $blue = $rgba & 0xFF;

            if ($red >= $threshold && $green >= $threshold && $blue >= $threshold) {
                continue;
            }

            $minX = min($minX, $x);
            $minY = min($minY, $y);
            $maxX = max($maxX, $x);
            $maxY = max($maxY, $y);
        }
    }

    if ($maxX <= $minX || $maxY <= $minY) {
        return $source;
    }

    $padding = 4;
    $minX = max(0, $minX - $padding);
    $minY = max(0, $minY - $padding);
    $maxX = min($width - 1, $maxX + $padding);
    $maxY = min($height - 1, $maxY + $padding);
    $cropWidth = $maxX - $minX + 1;
    $cropHeight = $maxY - $minY + 1;

    $cropped = imagecreatetruecolor($cropWidth, $cropHeight);
    imagecopy($cropped, $source, 0, 0, $minX, $minY, $cropWidth, $cropHeight);

    return $cropped;
}

function flattenOnWhite(GdImage $source): GdImage
{
    $width = imagesx($source);
    $height = imagesy($source);
    $target = imagecreatetruecolor($width, $height);
    $white = imagecolorallocate($target, 255, 255, 255);
    imagefilledrectangle($target, 0, 0, $width, $height, $white);
    imagecopy($target, $source, 0, 0, 0, 0, $width, $height);

    return $target;
}

$bestSource = null;
$bestArea = 0;

foreach ($sources as $sourcePath) {
    if (! is_file($sourcePath)) {
        continue;
    }

    $image = loadImage($sourcePath);
    $area = imagesx($image) * imagesy($image);

    if ($area > $bestArea) {
        $bestArea = $area;
        $bestSource = $sourcePath;
    }

    imagedestroy($image);
}

if ($bestSource === null) {
    throw new RuntimeException('No logo source files found.');
}

echo "Using source: {$bestSource}\n";

$source = trimSourceMargins(stripSolidLeftEdgeBar(loadImage($bestSource)));
$transparent = trimWhitespace(makeTransparent($source));
$whiteBg = flattenOnWhite($transparent);

savePng($transparent, $brandDir . '/logo-transparent.png');
savePng($whiteBg, $brandDir . '/logo.png');

$width = imagesx($transparent);
$height = imagesy($transparent);
$faviconSize = 512;
$favicon = imagecreatetruecolor($faviconSize, $faviconSize);
$white = imagecolorallocate($favicon, 255, 255, 255);
imagefilledrectangle($favicon, 0, 0, $faviconSize, $faviconSize, $white);

$scale = min(($faviconSize - 80) / $width, ($faviconSize - 80) / $height);
$targetWidth = (int) round($width * $scale);
$targetHeight = (int) round($height * $scale);
$offsetX = (int) round(($faviconSize - $targetWidth) / 2);
$offsetY = (int) round(($faviconSize - $targetHeight) / 2);

imagecopyresampled(
    $favicon,
    $whiteBg,
    $offsetX,
    $offsetY,
    0,
    0,
    $targetWidth,
    $targetHeight,
    $width,
    $height,
);

savePng($favicon, $brandDir . '/favicon.png');

imagedestroy($source);
imagedestroy($transparent);
imagedestroy($whiteBg);
imagedestroy($favicon);

@unlink($brandDir . '/logo-new-1.png');
@unlink($brandDir . '/logo-new-2.png');

echo 'Stripped left edge bar and created logo.png, logo-transparent.png, favicon.png' . PHP_EOL;
