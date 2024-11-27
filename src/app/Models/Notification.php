<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Notification extends Model
{
    use HasFactory;

    // Define the table associated with the model
    protected $table = 'notifications';

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
        'receiver',
        'description',
        'type',
        'sent_at',
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
        'sent_at' => 'datetime',
        'type' => 'string',
    ];

    /**
     * Get the user that received the notification.
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver');
    }

    /**
     * Get the posts associated with the notification.
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'notificationpost', 'notification_id', 'post_id');
    }

    /**
     * Get the users associated with the notification.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'notificationuser', 'notification_id', 'user_id');
    }
}