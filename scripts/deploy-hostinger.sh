#!/bin/bash
set -e

cd ~/domains/sutraconscious.com/sutra-app
PHP83=/opt/alt/php83/usr/bin/php

echo "== PHP =="
$PHP83 -v

echo "== Git hard sync =="
git fetch origin
git reset --hard origin/master
git log -1 --oneline

echo "== Composer =="
$PHP83 "$(which composer)" install --no-dev --optimize-autoloader

echo "== Sync public_html =="
rm -rf ../public_html/*
cp -a public/. ../public_html/

cat > ../public_html/index.php <<'PHP'
<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$laravelRoot = dirname(__DIR__) . '/sutra-app';

if (file_exists($maintenance = $laravelRoot.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $laravelRoot.'/vendor/autoload.php';

(require_once $laravelRoot.'/bootstrap/app.php')
    ->handleRequest(Request::capture());
PHP

cat > ../public_html/.htaccess <<'HT'
# Disable LiteSpeed cache for this site (prevents stale old design)
<IfModule LiteSpeed>
  CacheDisable public /
</IfModule>

<IfModule mod_headers.c>
  Header set Cache-Control "no-cache, no-store, must-revalidate" env=NO_CACHE
  <FilesMatch "\.(html|php)$">
    SetEnv NO_CACHE 1
  </FilesMatch>
  <FilesMatch "\.(css|js)$">
    Header set Cache-Control "public, max-age=31536000, immutable"
  </FilesMatch>
</IfModule>

<IfModule mime_module>
  AddHandler application/x-httpd-ea-php83___lsphp .php .php8 .phtml
</IfModule>

<IfModule mod_rewrite.c>
  <IfModule mod_negotiation.c>
    Options -MultiViews -Indexes
  </IfModule>
  RewriteEngine On
  RewriteCond %{HTTP:Authorization} .
  RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteCond %{REQUEST_URI} (.+)/$
  RewriteRule ^ %1 [L,R=301]
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteRule ^ index.php [L]
</IfModule>
HT

echo "== Laravel maintenance =="
mkdir -p storage/logs
chmod -R 775 storage bootstrap/cache
$PHP83 artisan storage:link 2>/dev/null || true
$PHP83 artisan optimize:clear
$PHP83 artisan config:cache
$PHP83 artisan view:clear

echo "== Optimize product images =="
$PHP83 scripts/optimize-product-images.php || true

echo "== Asset fingerprint =="
ls -la public/build/assets/
ls -la ../public_html/build/assets/
cat ../public_html/build/manifest.json

echo "BUILD_2026-05-28-v3" > ../public_html/BUILD.txt
date >> ../public_html/BUILD.txt

echo "== DONE =="
