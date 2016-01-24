<?php namespace ImagesManager;

use Illuminate\Database\Eloquent\Model;

class Album extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','title', 'description', 'user_id',
    ];

    /**
     * Relationship to user
     */
    public function owner()
    {
        return $this->belongsTo('ImagesManager\User');
    }

    /**
     * Relationship to photos
     */
    public function photos()
    {
        return $this->hasMany('ImagesManager\Photo');
    }



}
