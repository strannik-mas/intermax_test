<?php
declare(strict_types=1);

namespace App\Enum;

enum ActivityType: string
{
    case Login = 'login';
    case Purchase = 'purchase';
    case SupportTicket = 'support_ticket';
    case ProfileUpdate = 'profile_update';
    case PasswordChange = 'password_change';
}
