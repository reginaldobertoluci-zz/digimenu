<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
   
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'menu_item';

    /**
     * Get the menu that owns the item
     */
    public function menu()
    {
       return $this->belongsTo('App\Menu');
    }

    /**
     * Get the category that owns the item
     */
    public function category()
    {
        return $this->belongsTo('App\Category');
    }
}
