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

    protected $fillable = ['name', 'qrcode'];

    protected $hidden = ['qrcode', 'venue_id', 'created_at', 'updated_at'];

    /**
     * Get the venue that owns the menu.
     */
    public function venue()
    {
        return $this->belongsTo('App\Venue');
    }

    /**
     * Get the sections for the menu
     */
    public function sections()
    {
        return $this->hasMany('App\Section');
    }

}
