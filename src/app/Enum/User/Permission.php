<?php

namespace App\Enum\User;

enum Permission: string

{
    case Blocked = 'BLOCKED';
    case Regular = 'REGULAR';
    case Moderator = 'MODERATOR';
	case Admin = 'ADMIN';
}