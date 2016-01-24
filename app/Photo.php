<?php namespace ImagesManager;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','title', 'path', 'album_id',
    ];

    /**
     * Relationship to album
     */
    public function album()
    {
        return $this->belongsTo('ImagesManager\Album');
    }

}
