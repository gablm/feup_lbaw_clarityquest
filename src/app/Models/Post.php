<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
    ];

	public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

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

	public function creationFTime() : string
	{
		$date = date('Y-m-d', time());
		$time = explode(" ", $this->created_at);

		if ($date == $time[0])
			return $this->created_at->format("H:i");

		return $this->created_at->format("d/m/Y H:i");
	}

     /**
     * Check if the question has been edited and return the most recent edition's timestamp.
     *
     * @return string|null
     */
    public function isEdited()
    {
        $latestEdition = $this->editions()->latest('made_at')->first();
        if ($latestEdition) {
            $currentDate = date('Y-m-d');
            $madeAt = $latestEdition->made_at;
            $timeParts = explode(" ", $madeAt);

            if ($currentDate == $timeParts[0]) {
                return date("H:i", strtotime($madeAt));
            }

            return date("d/m/Y H:i", strtotime($madeAt));
        }
        return null;
    }
    
    public function voteStatus($user_id)
    {
        $vote = $this->votes()->where('user_id', $user_id)->first();
        $voteStatus = $vote ? ($vote->positive ? 'positive' : 'negative') : null;
    
        return $voteStatus;
    }
}