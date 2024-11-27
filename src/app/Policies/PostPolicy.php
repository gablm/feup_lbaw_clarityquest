<?php

namespace App\Policies;

use App\Enum\User\Permission;
use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Post $post): bool
    {
        return $user == null || $user->is_blocked == false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->is_blocked == false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Post $post): bool
    {
        $has_role = $user->isAdmin();;
		$is_owner = $user->id == $post->user_id;
		$is_blocked = $user->role == Permission::Blocked;
		
        return $has_role || ($is_owner && !$is_blocked);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Post $post): bool
    {
		$has_role = $user->role == Permission::Admin
			|| $user->role == Permission::Moderator;
		$is_owner = $user->id == $post->user_id;
		$is_blocked = $user->role == Permission::Blocked;

        return $has_role || ($is_owner && !$is_blocked);
    }
}
