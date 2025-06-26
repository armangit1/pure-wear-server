<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  public function category()
  {
    return $this->belongsTo(Categorie::class);
  }

  public function brand()
  {
    return $this->belongsTo(Brand::class);
  }
  public function images()
  {
    return $this->hasMany(Image::class);
  }
  public function sizes()
  {
    return $this->belongsToMany(Size::class, 'product_sizes')
      ->withPivot('user_id')
      ->withTimestamps();
  }
}
