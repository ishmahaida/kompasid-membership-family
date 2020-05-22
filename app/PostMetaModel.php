<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostMetaModel extends Model
{
    //
    // protected $connection = 'wpdb_kompaside16';
    protected $connection = 'mysql';
    protected $table = 'wp_g4i_postmeta';
}