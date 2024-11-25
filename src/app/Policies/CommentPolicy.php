<?php

namespace App\Policies;

use App\Enum\User\Permission;
use App\Models\Comment;
use App\Models\User;

class CommentPolicy
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
    public function update(User $user, Comment $comment): bool
    {
        $has_role = $has_role = $user->isAdmin();;
		$is_owner = $user->id == $comment->post->user_id;
		$is_blocked = $user->role == Permission::Blocked;
		
        return $has_role || ($is_owner && !$is_blocked);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Comment $comment): bool
    {
		$has_role = $user->role == Permission::Admin
			|| $user->role == Permission::Moderator;
		$is_owner = $user->id == $comment->post->user_id;
		$is_blocked = $user->role == Permission::Blocked;

        return $has_role || ($is_owner && !$is_blocked);
    }
}
