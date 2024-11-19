<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'id', // This is the foreign key referencing the Post table
    ];

    /**
     * Get the post associated with the question.
     */
    public function post()
    {
        return $this->belongsTo(Post::class, 'id');
    }

    /**
     * Get the answers for the question.
     */
    public function answers()
    {
        return $this->hasMany(Answer::class, 'question_id');
    }

    /**
     * Get the comments for the question.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id', 'id');
    }

    /**
     * Get the tags for the question.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tag', 'post_id', 'tag_id');
    }

    /**
     * Get the top 10 questions sorted by score.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getTopQuestions()
    {
        return self::select('questions.*', DB::raw('0.6 * posts.votes + 0.4 * EXTRACT(EPOCH FROM (NOW() - posts.created_at)) as score'))
            ->join('posts', 'questions.id', '=', 'posts.id')
            ->orderByDesc('score')
            ->limit(10)
            ->get();
    }
    /**
     * Get the latest 10 questions sorted by creation date.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getLatestQuestions()
    {
        return self::select('questions.*')
            ->join('posts', 'questions.id', '=', 'posts.id')
            ->orderByDesc('posts.created_at')
            ->limit(10)
            ->get();
    }
    
}