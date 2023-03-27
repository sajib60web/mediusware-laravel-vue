<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Variant extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'description'
    ];

    /**
     * Product variants
     *
     * @return void
     */
    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class)->groupBy('variant');
    }
}
