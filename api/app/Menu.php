<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'menus';

    /**
     * Get the venue that owns the menu.
     */
    public function venue()
    {
        return $this->belongsTo('App\Venue');
    }

     /**
     * Get the items for the menu
     */
    public function items()
    {
        return $this->hasMany('App\Item');

    }

    
    /**
     * Get the categories for the menu
     */
    public function categories()
    {
        return $this->belongsToMany('App\Category', 'menu_category');
    }

}
