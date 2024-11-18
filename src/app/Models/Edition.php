<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Edition extends Model
{
    use HasFactory;

    // Define the table associated with the model
    protected $table = 'editions';

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
        'post_id',
        'old_title',
        'new_title',
        'old',
        'new',
        'made_at'
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
        'made_at' => 'datetime',
    ];

    /**
     * Get the post that the edition belongs to.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
}