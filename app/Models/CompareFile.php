<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompareFile extends Model
{
    use HasFactory;
    // protected $table = 'compare_files';
    protected $fillable = [
        'web_url', 'image', 'url_image', 'hash_tag', 'is_image_percent', 'status', 'created_at', 'updated_at'
    ];
}
