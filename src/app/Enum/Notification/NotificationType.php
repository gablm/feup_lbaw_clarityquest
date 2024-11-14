<?php

namespace App\Enum\Notification;

enum NotificationType: string

{
    case Response = 'RESPONSE';
    case Report = 'REPORT';
    case Follow = 'FOLLOW';
	case Mention = 'MENTION';
	case Other = 'OTHER';
}