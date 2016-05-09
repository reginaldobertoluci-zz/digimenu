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

    protected $fillable = ['name'];

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

    public function sections()
    {
        return $this->hasManyThrough('App\Section', 'App\Menu');
    }


}
