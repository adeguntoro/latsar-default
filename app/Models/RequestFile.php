<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestFile extends Model
{
    /** @use HasFactory<\Database\Factories\RequestFileFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'post_id',
        'nama_peminta',
        'nomor_telepon',
        'alamat_peminta',
        'alasan_permintaan',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'user_served'
    ];

    /**
     * Get the post that owns the request file.
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Get the user who created this request file.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_served');
    }
}