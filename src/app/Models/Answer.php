<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Answer extends Model
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
        'id', // This is the foreign key referencing the Post table
        'question_id',
        'correct',
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
        'correct' => 'boolean',
    ];

    /**
     * Get the post associated with the answer.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'id');
    }

    /**
     * Get the question that the answer belongs to.
     * May be useless, get post and check if it is a question.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    /**
     * Mark this answer as correct.
     */
    public function markAsCorrect(): void
    {
        // should it unmark other answers for the same question?
        self::where('question_id', $this->question_id)
            ->where('id', '!=', $this->id)
            ->update(['correct' => false]);

        $this->correct = true;
        $this->save();
    }
}