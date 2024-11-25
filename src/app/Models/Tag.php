<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    // Define the primary key for the model
    protected $primaryKey = 'id';

    // Don't add create and update timestamps in database.
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        // Add any attributes you want to hide
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * The posts that belong to the tag.
     * may be useless, dont know if makes sense
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'posttag', 'tag_id', 'post_id');
    }

    /**
     * Get the users following the tag.
     */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'followtag', 'tag_id', 'user_id');
    }
   
    /**
     * Get all questions associated with the tag. its defined for
     * post but app logic should ensure its only questions
     */
    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'posttag', 'tag_id', 'post_id', 'id', 'id');
    }

    /**
     * Get all the tags that a user follows.
     */
    public function follows(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'followtag', 'tag_id', 'user_id');
    }
    
    public function isFollowed(User $user)
	{
		return $this->follows()->where('users.id', $user->id)->exists();
	}
}