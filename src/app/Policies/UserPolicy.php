<?php

namespace App\Policies;

use App\Enum\User\Permission;
use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $user2): bool
    {
        $has_role = $has_role = $user->isAdmin();;
		$is_owner = $user->id == $user2->id;
		$is_blocked = $user->role == Permission::Blocked;
		
        return $has_role || ($is_owner && !$is_blocked);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $user2): bool
    {
		$has_role = $user->isAdmin();
		$is_owner = $user->id == $user2->id;
		$is_blocked = $user->role == Permission::Blocked;
		
        return $has_role || ($is_owner && !$is_blocked);
    }
}
