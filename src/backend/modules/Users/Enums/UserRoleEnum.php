<?php

namespace Users\Enums;

enum UserRoleEnum: string
{
    case API = 'api';
    case ADMIN = 'admin';
    case ACCOUNT_OWNER = 'account_owner';
}
