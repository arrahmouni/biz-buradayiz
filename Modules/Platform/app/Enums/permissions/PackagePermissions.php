<?php

namespace Modules\Platform\Enums\permissions;

final class PackagePermissions
{
    const PERMISSION_NAMESPACE = 'PACKAGE';

    const CREATE = 'CREATE_'.self::PERMISSION_NAMESPACE;

    const READ = 'READ_'.self::PERMISSION_NAMESPACE;

    const SHOW = 'SHOW_'.self::PERMISSION_NAMESPACE;

    const UPDATE = 'UPDATE_'.self::PERMISSION_NAMESPACE;

    const SOFT_DELETE = 'SOFT_DELETE_'.self::PERMISSION_NAMESPACE;

    const HARD_DELETE = 'HARD_DELETE_'.self::PERMISSION_NAMESPACE;

    const RESTORE = 'RESTORE_'.self::PERMISSION_NAMESPACE;

    const VIEW_TRASH = 'VIEW_TRASH_'.self::PERMISSION_NAMESPACE;

    const DISABLE = 'DISABLE_'.self::PERMISSION_NAMESPACE;

    const ENABLE = 'ENABLE_'.self::PERMISSION_NAMESPACE;
}
