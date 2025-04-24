<?php

namespace OsdAurox;

use App\AppUrls;

class Sec
{

    public static function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    public static function getRealIpAddr()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            // Check IP from internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // Check IP is passed from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            // Check IP address from remote address
            $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        }
        return $ip;
    }

    public static function h($string)
    {
        if ($string === null || $string === '') {
            return '';
        }
        if (is_array($string)) {
            return '<array>';
        }
        if (is_object($string) || is_resource($string)) {
            return '<complex-type>';
        }
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    public static function getAction(string $default = 'home')
    {
        $action = $_GET['action'] ?? $default;
        $action = Sec::hNoHtml($action);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action .= 'Post'; // Si POST, appelle les méthodes correspondantes comme "loginPost"
        }

        if(isset($_SERVER['CONTENT_TYPE'])  && $_SERVER['CONTENT_TYPE'] === 'application/json') {
            $action .= 'Json'; // Si JSON, appelle les méthodes correspondantes comme "loginJson"
        }

        return $action;
    }

    public static function safeForLikeStrong($string)
    {
        if(!is_string($string)) {
            return '';
        }
        if(!$string) {
            return '';
        }

        $string = trim($string);

        if (strlen($string) < 2) {
            return '';
        }

        $string = substr($string, 0, 250);
        $string = strip_tags($string);

        $cleanString = preg_replace('/[^a-zA-Z0-9\p{L}\s]/u', '', $string);
        return str_replace(['%', '_'], ['\%', '\_'], $cleanString);
    }

    public static function safeForLike($string)
    {
        if (!is_string($string)) {
            return '';
        }
        if (!$string) {
            return '';
        }

        $string = trim($string);

        if (strlen($string) < 3) {
            return '';
        }

        $string = substr($string, 0, 250);

        $string = strip_tags($string);
        return str_replace(['%', '_'], ['\%', '\_'], $string);
    }


    public static function jsonDatas()
    {
        $jsonData = file_get_contents('php://input');
        $datas = json_decode($jsonData, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }
        $data_sanitized = [];
        foreach ($datas as $key => $value) {
            $data_sanitized[$key] = $value;
            if($value === 'on') {
                $data_sanitized[$key] = true;
            }
        }
        return $data_sanitized;
    }

    public static function isAdminOrDie($flash = true, $redirect = true)
    {
        if (Sec::isAdminBool() === false) {
            if ($flash) {
                Flash::add('danger', 'Accès refusé');
            }
            if ($redirect) {
                header('Location: ' . AppUrls::LOGIN);
                exit;
            }
            die('Accès refusé');
        }
    }

    public static function isAdminBool()
    {
        if (!isset($_SESSION['user'])){
            return false;
        }
        if (!isset($_SESSION['user']['role'])){
            return false;
        }
        if($_SESSION['user']['role'] === 'admin'){
            return true;
        }
        return false;
    }

    public static function isRoleOrDie($role, $flash = true, $redirect = true)
    {
        if (Sec::isRoleBool($role) === false) {
            if ($flash) {
                Flash::add('danger', 'Accès refusé');
            }
            if ($redirect) {
                header('Location: ' . AppUrls::LOGIN);
                exit;
            }
            die('Accès refusé');
        }
    }

    public static function isRoleBool($role)
    {
        if (!isset($_SESSION['user'])){
            return false;
        }
        if (!isset($_SESSION['user']['role'])){
            return false;
        }
        if($_SESSION['user']['role'] === $role){
            return true;
        }
        return false;
    }

    public static function isLogged($flash = true, $redirect = true)
    {
        if (!isset($_SESSION['user'])) {
            if ($flash) {
                Flash::add('danger', 'Accès refusé');
            }
            if ($redirect) {
                header('Location: ' . AppUrls::LOGIN);
                exit;
            }
            die('Accès refusé');
        }
    }


    public static function noneCsp(): string
    {
        return htmlspecialchars(NONCE, ENT_QUOTES, 'UTF-8');
    }

    public static function getPage():int
    {
        $page = Sec::h($_GET['page'] ?? 1);

        if ($page < 1) {
            $page = 1;
        }
        return (int) $page;
    }

    public static function getPerPage(): int
    {
        $perPage = Sec::h($_GET['per_page'] ?? 10);
        if ($perPage < 1) {
            $perPage = 10;
        }
        return (int) $perPage;
    }

    public static function hNoHtml($string): string
    {
        if ($string === null || $string === '') {
            return '';
        }
        if (is_array($string)) {
            return '{array}';
        }
        if (is_object($string) || is_resource($string)) {
            return '{complex-type}';
        }

        $string = strip_tags($string);
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }


}