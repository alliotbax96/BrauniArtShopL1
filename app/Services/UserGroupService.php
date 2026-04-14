<?php

namespace App\Services;

use App\Models\Group;
use App\Models\User;

class UserGroupService
{
    public function assignGroup(User $user, string $groupType): void
    {
        $group = Group::where('type', $groupType)->firstOrFail();
        $user->groups()->syncWithoutDetaching([$group->id]);
    }

    public function removeGroup(User $user, string $groupType): void
    {
        $group = Group::where('type', $groupType)->firstOrFail();
        $user->groups()->detach($group->id);
    }
}
