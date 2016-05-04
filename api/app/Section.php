<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sections';

    protected $fillable = ['name', 'order'];

    protected $hidden = ['order', 'menu_id', 'created_at', 'updated_at'];

    /**
     * Get the menu that owns the section
     */
    public function menu()
    {
        return $this->belongsTo('App\Menu');
    }

    /**
     * Get all of the items for the category
     */
    public function items()
    {
        return $this->hasMany('App\Item');
    }
}
