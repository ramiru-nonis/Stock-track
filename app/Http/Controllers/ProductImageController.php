<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Response;

class ProductImageController extends Controller
{
    /**
     * Serve product image directly from database BLOB / longtext.
     */
    public function show(Product $product): Response
    {
        if (empty($product->image_data)) {
            abort(404, 'Image not found.');
        }

        $binaryData = base64_decode($product->image_data);
        $mimeType = $product->image_mime ?? 'image/jpeg';

        return response($binaryData, 200, [
            'Content-Type' => $mimeType,
            'Content-Length' => strlen($binaryData),
            'Cache-Control' => 'max-age=86400, public',
        ]);
    }
}
