<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory;

    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'text',
		'votes',
		'user_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
		'created_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
		'user_id' => User::class
    ];

    /**
     * Get the comments for a post.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'post_id');
    }
    
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class, 'post_id');
    }

    public function editions(): HasMany
    {
        return $this->hasMany(Edition::class, 'post_id');
    }

}