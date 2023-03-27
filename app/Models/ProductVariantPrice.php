<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariantPrice extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_variant_one',
        'product_variant_two',
        'product_variant_three',
        'price',
        'stock',
        'product_id'
    ];
    /**
     * Variant name one
     *
     * @return void
     */
    public function variantNameOne()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_one', 'id');
    }

    /**
     * Variant name two
     *
     * @return void
     */
    public function variantNameTwo()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_two', 'id');
    }

    /**
     * Variant name three
     *
     * @return void
     */
    public function variantNameThree()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_three', 'id');
    }
}
