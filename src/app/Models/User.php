<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

use App\Enum\User\Permission;
use App\Models\Post;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Comment;
use App\Models\Vote;
use App\Models\Medal;
use App\Models\Report;
use App\Models\Tag;
use App\Models\Notification;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'google_token',
        'x_token',
        'profile_pic',
        'bio',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'google_token',
        'x_token',
        'remember_token',
        'created_at',
        'role'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'password' => 'hashed',
        'role' => Permission::class
    ];

    /**
     * Get the posts for a user.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get all the tags that a user follows.
     */
    public function followedTags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'followtag', 'user_id', 'tag_id');
    }

    /**
     * Get all the questions that a user follows.
     */
    public function followedQuestions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'followquestion', 'user_id', 'question_id');
    }

    /**
     * Get all the notifications for a user.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'receiver');
    }

    /**
     * Get all the medals for a user.
     */
    public function medals(): HasOne
    {
        return $this->hasOne(Medal::class, 'user_id');
    }

    /**
     * Get the sum of all medals for a user.
     */
    public function totalMedals(): int
    {
        $medals = $this->medals;
        return $medals->posts_upvoted + $medals->posts_created + $medals->questions_created + $medals->answers_posted;
    }

    /**
     * Get the count of posts upvoted medals.
     */
    public function postsUpvotedMedals(): int
    {
        return $this->medals->posts_upvoted;
    }

    /**
     * Get the count of posts created medals.
     */
    public function postsCreatedMedals(): int
    {
        return $this->medals->posts_created;
    }

    /**
     * Get the count of questions created medals.
     */
    public function questionsCreatedMedals(): int
    {
        return $this->medals->questions_created;
    }

    /**
     * Get the count of answers posted medals.
     */
    public function answersPostedMedals(): int
    {
        return $this->medals->answers_posted;
    }

    /**
     * Get all the reports made by a user.
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'user_id');
    }

    /**
     * Get all the answers to the user's questions.
     */
    public function answersToQuestions(): HasManyThrough
    {
        return $this->hasManyThrough(
            Answer::class,
            Question::class,
            'user_id', // Foreign key on the Question table
            'question_id', // Foreign key on the Answer table
            'id', // Local key on the User table
            'id' // Local key on the Question table
        );
    }

    /**
     * Get all comments on the user's posts.
     */
    public function commentsOnPosts(): HasManyThrough
    {
        return $this->hasManyThrough(
            Comment::class,
            Post::class,
            'user_id', // Foreign key on the Post table
            'post_id', // Foreign key on the Comment table
            'id', // Local key on the User table
            'id' // Local key on the Post table
        );
    }

    /**
     * Get all votes on the user's posts.
     */
    public function votesOnPosts(): HasManyThrough
    {
        return $this->hasManyThrough(
            Vote::class,
            Post::class,
            'user_id', // Foreign key on the Post table
            'post_id', // Foreign key on the Vote table
            'id', // Local key on the User table
            'id' // Local key on the Post table
        );
    }

    /**
     * Get all the questions created by the user.
     * Its kinda posts created, but only questions.
     */
    public function questionsCreated(): HasManyThrough
    {
        return $this->hasManyThrough(
            Question::class,
            Post::class,
            'user_id', // Foreign key on the Post table
            'id', // Foreign key on the Question table
            'id', // Local key on the User table
            'id' // Local key on the Post table
        );
    }

    /**
     * Get all the answers posted by the user.
     */
    public function answersPosted(): HasManyThrough
    {
        return $this->hasManyThrough(
            Answer::class,
            Post::class,
            'user_id', // Foreign key on the Post table
            'id', // Foreign key on the Answer table
            'id', // Local key on the User table
            'id' // Local key on the Post table
        );
    }
}