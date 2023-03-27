<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'sku', 'description'
    ];

    /**
     * Product variants
     *
     * @return void
     */
    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Product variant price
     *
     * @return void
     */
    public function productVariantPrice()
    {
        return $this->hasMany(ProductVariantPrice::class, 'product_id', 'id');
    }

    /**
     * Product images
     *
     * @return void
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
}
