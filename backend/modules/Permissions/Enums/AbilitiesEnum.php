<?php

namespace Permissions\Enums;

enum AbilitiesEnum: string
{
    // Restrict abilities
    case RESET_PASSWORD = 'reset_password';

    // General abilities
    case USERS = 'users';
    case PERMISSIONS = 'permissions';

    public static function availableAbilities(): array
    {
        return [
            self::USERS,
            self::PERMISSIONS,
        ];
    }

    public static function requireAllAbilities(array $abilities): string
    {
        $abilities = array_map(fn (self $ability) => $ability->value, $abilities);

        return 'abilities:' . implode(',', $abilities);
    }

    public static function requireAnyAbility(array $abilities): string
    {
        return 'ability:' . implode(',', $abilities);
    }
}