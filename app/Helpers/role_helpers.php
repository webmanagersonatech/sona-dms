<?php

if (!function_exists('getActionBadgeColor')) {
    function getActionBadgeColor(string $action): string
    {
        return match ($action) {
            'login'         => 'success',
            'logout'        => 'secondary',
            'file_upload'   => 'primary',
            'file_download' => 'info',
            'file_delete'   => 'danger',
            'share_created' => 'warning',
            default         => 'dark',
        };
    }
}

if (!function_exists('getRoleBadgeColor')) {
    function getRoleBadgeColor(string $roleSlug): string
    {
        return match ($roleSlug) {
            'super-admin' => 'danger',
            'admin'       => 'warning',
            'owner'       => 'primary',
            'sender'      => 'info',
            'receiver'    => 'success',
            'third-party' => 'secondary',
            default       => 'secondary',
        };
    }
}
