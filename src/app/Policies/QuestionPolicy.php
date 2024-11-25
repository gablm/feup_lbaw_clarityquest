<?php

namespace App\Policies;

use App\Enum\User\Permission;
use App\Models\Question;
use App\Models\User;

class QuestionPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role != Permission::Blocked;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Question $question): bool
    {
        $has_role = $has_role = $user->isAdmin();;
		$is_owner = $user->id == $question->post->user_id;
		$is_blocked = $user->role == Permission::Blocked;
		
        return $has_role || ($is_owner && !$is_blocked);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Question $question): bool
    {
		$has_role = $user->role == Permission::Admin
			|| $user->role == Permission::Moderator;
		$is_owner = $user->id == $question->post->user_id;
		$is_blocked = $user->role == Permission::Blocked;

        return $has_role || ($is_owner && !$is_blocked);
    }
}
