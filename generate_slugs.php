<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use Illuminate\Support\Str;

echo "Generating slugs for existing products...\n";

$products = Product::whereNull('slug')->orWhere('slug', '')->get();

foreach ($products as $product) {
    $product->slug = Str::slug($product->title);
    $product->save();
    echo "Generated slug '{$product->slug}' for product '{$product->title}'\n";
}

echo "Done! Generated slugs for " . count($products) . " products.\n";
