<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// Added to define Eloquent relationships.
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post
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
		'user'
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

    /**
     * Upvote the post.
     */
    public function upvote(int $userId): void
    {
        $vote = Vote::updateOrCreate(
            ['user_id' => $userId, 'post_id' => $this->id],
            ['positive' => true]
        );

        if (!$vote->wasRecentlyCreated && !$vote->positive) {
            $this->post->increment('votes');
        }
    }

    /**
     * Downvote the post.
     */
    public function downvote(int $userId): void
    {
        $vote = Vote::updateOrCreate(
            ['user_id' => $userId, 'post_id' => $this->id],
            ['positive' => false]
        );

        if (!$vote->wasRecentlyCreated && $vote->positive) {
            $this->post->decrement('votes');
        }
    }
}