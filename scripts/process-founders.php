<?php

$source = $argv[1] ?? '';
$target = $argv[2] ?? '';

if ($source === '' || $target === '') {
    fwrite(STDERR, "Usage: php process-founders.php <source> <target>\n");
    exit(1);
}

$image = new Imagick($source);
$format = $image->getImageFormat();
echo "Source format: {$format}\n";

if (strtoupper($format) === 'PNG' && $image->getImageAlphaChannel()) {
    $image->setImageFormat('png');
    $image->writeImage($target);
    echo "Copied PNG with alpha unchanged.\n";
    exit(0);
}

// JPEG or PNG without alpha: remove black matte, export lossless PNG.
$image->setImageAlphaChannel(Imagick::ALPHACHANNEL_SET);
$image->transparentPaintImage('#000000', 0, 800, false);
$image->setImageFormat('png');
$image->setOption('png:compression-level', '3');
$image->writeImage($target);
echo "Exported PNG with transparent background.\n";
