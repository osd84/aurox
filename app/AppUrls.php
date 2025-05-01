<?php

namespace App;

use OsdAurox\Sec;

class AppUrls
{
    public const HOME = '/';

    # 404
    public const NOT_FOUND = '/404.php';

    # login
    public const LOGIN = '/auth/login.php';
    public const LOGOUT = '/auth/login.php?action=logout';

    public const ADMIN_DIR = '/admin';
    public const ADMIN_HOME = '/admin/index.php';
    public const ADMIN_USERS = '/admin/users.php';
    public const ADMIN_USER_EDIT = '/admin/user_edit.php';
    public const ADMIN_USER_ADD = '/admin/user_edit.php';

    public static function getList(): array
    {
        $reflect = new \ReflectionClass(__CLASS__);
        return $reflect->getConstants();
    }

    public static function existOr404()
    {
        $url = $_SERVER['REQUEST_URI'] ?? '/';
        $url = trim($url, '/');
        $url = explode('?', $url);
        $url = $url[0];
        $url = trim($url, '/');
        $url = strtolower($url);
        if(str_starts_with('/', $url)){
            $url = substr($url, 1);
        }
        $url = '/' . $url;
        if (in_array($url, ['/', '/index.php']))
        {
            return true;
        }
        $urls = self::getList();
        if(in_array($url, $urls)) {
            return true;
        }
        header('Location: ' . AppUrls::NOT_FOUND . '?url=' . Sec::hNoHtml($url));
        exit;
    }
}