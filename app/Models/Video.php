<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = [
        'title',
        'filename',
        'type',
        'description',
    	'status'
    ];

    protected $appends = ['link_video'];

    public function getLinkVideoAttribute(){
        $link = asset('video/'.$this->attributes['filename']);
        return $link;
    }

}
