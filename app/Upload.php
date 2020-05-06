<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{

    protected $fillable = array(
        'name', 'size', 'chunks', 'uploadedChunks', 'chunkSize',
        'uploadedFileID', 'uploadedFileName', 'fileType', 'uploaded',
    );

}
