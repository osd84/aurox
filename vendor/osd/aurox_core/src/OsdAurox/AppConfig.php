<?php

namespace OsdAurox;

use OsdAurox\Sec;

Class AppConfig {

    private static ?self $instance = null;

    public string $appName;
    public string $appTitle;
    public string $appAuthor;
    public string $appDescription;
    public string $ceoDesc;
    public string $ceoKeywords;
    public string $appFavicon;
    public string $appLogo;
    public string $appLang;
    public string $appVersion;

    public ?string $appUrl;
    public string $devUrl;
    public string $prodUrl;
    public string $loginUrlForm;

    public ?bool $debug;
    public string $host;
    public string $db;
    public string $user;
    public string $pass;
    public string $charset;
    public bool $feature_register;
    public int $password_min_length;
    public int $password_max_length;
    public string $password_complexity;
    public string $admin_folder;
    public bool $nonce;
    public string $devIp;

    public array $lang;
    public bool $feature_user_allow_admin_create;

    public string $discordWebhook;
    public string $salt;

    public static function init(array $config): self
    {
        if (self::$instance !== null) {
            throw new \RuntimeException("AppConfig déjà initialisé");
        }
        self::$instance = new self($config);
        return self::$instance;
    }


    private function __construct(array $conf)
    {

        // Affectation des propriétés depuis le tableau $conf
        $this->appName = $conf['appName'] ?? 'DefaultAppName';
        $this->appTitle = $conf['appTitle'] ?? 'DefaultTitle';
        $this->appAuthor = $conf['appAuthor'] ?? '-';
        $this->appDescription = $conf['appDescription'] ?? '-';
        $this->ceoDesc = $conf['ceoDesc'] ?? '-';
        $this->ceoKeywords = $conf['ceoKeywords'] ?? '-';
        $this->appFavicon = $conf['appFavicon'] ?? 'favicon.ico';
        $this->appLogo = $conf['appLogo'] ?? 'logo.png';
        $this->appLang = $conf['appLang'] ?? 'fr';
        $this->appVersion = $conf['appVersion'] ?? '1.0.0';
        $this->devIp = $conf['devIp'] ?? '127.0.0.1';

        $this->devUrl = $conf['devUrl'] ?? 'http://localhost';
        $this->prodUrl = $conf['prodUrl'] ?? 'http://localhost';
        $this->appUrl = $conf['appUrl'];
        $this->loginUrlForm = $conf['loginUrlForm'] ?? '/';

        $this->debug = $conf['debug'] ?? false;
        $this->host = $conf['host'] ?? '127.0.0.1';
        $this->db = $conf['db'] ?? 'default_db';
        $this->user = $conf['user'] ?? 'root';
        $this->pass = $conf['pass'] ?? '';
        $this->charset = $conf['charset'] ?? 'utf8mb4';
        $this->password_min_length = $conf['password_min_length'] ?? 8;
        $this->password_max_length = $conf['password_max_length'] ?? 255;
        $this->password_complexity = $conf['password_complexity'] ?? '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,}$/';
        $this->admin_folder = $conf['admin_folder'] ?? 'admin';
        $this->nonce = $conf['nonce'] ?? false;

        $this->feature_register = $conf['feature_register'] ?? true;
        $this->feature_user_allow_admin_create = $conf['feature_user_allow_admin_create'] ?? false;

        $this->lang = $conf['lang'] ?? ['fr'];
        $this->discordWebhook = $conf['discordWebhook'] ?? '';
        $this->salt = $conf['salt'] ?? '';

    }

    public static function getInstance(): self
    {
        if(!self::$instance) {
            throw new \Exception('AppConfig not initialized');
        }
        return self::$instance;
    }

    public static function get(string $key, $default = '', $safe=false) {

        $instance = self::getInstance();
        if(!$safe) {
            return Sec::h($instance->$key ?? $default);
        }
        return $instance->$key ?? $default;
    }

}