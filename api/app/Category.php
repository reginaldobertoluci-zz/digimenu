<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categories';

    /**
     * Get the venue that owns the category
     */
    public function venue()
    {
        return $this->belongsTo('App\Venue');
    }

    /**
     * Get all of the items for the category
     */
    public function items()
    {
        return $this->hasMany('App\Item');
    }
}
