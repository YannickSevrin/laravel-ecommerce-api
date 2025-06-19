<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'category_id', 'image'];

    // 🔗 A product belongs to a category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // 🔗 A product can appear in several order items
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
