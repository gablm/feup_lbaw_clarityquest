<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Medals extends Model
{
    use HasFactory;

    // Define the table associated with the model
    protected $table = 'medals';

    // Define the primary key for the model
    protected $primaryKey = 'user_id';

    // Don't add create and update timestamps in database.
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'posts_upvoted',
        'posts_created',
        'questions_created',
        'answers_posted',
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
        'posts_upvoted' => 'integer',
        'posts_created' => 'integer',
        'questions_created' => 'integer',
        'answers_posted' => 'integer',
    ];

    /**
     * Get the user that owns the medals.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}