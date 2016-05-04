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
    protected $table = 'menu_items';

    protected $fillable = ['name', 'price'];

    protected $hidden = ['section_id', 'created_at', 'updated_at'];

    /**
     * Get the section that owns the item
     */
    public function section()
    {
        return $this->belongsTo('App\Section');
    }
}
