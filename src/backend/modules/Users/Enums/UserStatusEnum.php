<?php

namespace Users\Enums;

enum UserStatusEnum: string
{
    case ACTIVE = 'active';
    case BLOCKED = 'blocked';
    case PENDING_PASSWORD = 'pending_password';
}
