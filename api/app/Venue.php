<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'venues';

    /**
     * Get the users that owns the venue
     */
    public function users()
    {
        return $this->belongsToMany('App\User', 'venue_user');
    }    
    
    /**
     * Get the menus for the venue
     */
    public function menus()
    {
        return $this->hasMany('App\Menu');

    }

    /**
     * Get the categories for the venue
     */
    public function categories()
    {
        return $this->hasMany('App\Category');

    }
}