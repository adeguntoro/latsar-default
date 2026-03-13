<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory, Searchable;
    protected $fillable = [
        'title',
        'content',
        'file_path',
        'type',
        'slug',
        'short_url',
        'excerpt',
        'file_name',
        'file_type',
        'file_size',
        'status',
        'published_at',
        'views_count',
        'downloads_count',
        'is_featured',
        'department'
    ];

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'file_name' => $this->file_name,
            // 'excerpt' => $this->excerpt
        ];
    }

    /**
     * Get the request files for the post.
     */
    public function requestFiles()
    {
        return $this->hasMany(RequestFile::class);
    }
}
