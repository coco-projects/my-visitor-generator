<?php

    $redisConfig = $argv[1] ?? '{}';
    $redisConfig = json_decode(base64_decode($redisConfig), true);

    $redisClient = new \Redis();

    $redisClient->connect($redisConfig['host'], $redisConfig['port']);
    if ($redisConfig['password'])
    {
        $redisClient->auth($redisConfig['password']);
    }

    $redisClient->select($redisConfig['database']);

    $data = $redisClient->get($redisConfig['key']);
    $data = json_decode($data, true);

//    $redisClient->del($redisConfig['key']);

    if (empty($data['requests']) or !count($data['requests']))
    {
        exit('no requests');
    }

    if (empty($data['token_auth']))
    {
        exit('no token_auth');
    }

    $_COOKIE = [
        'perf_dv6Tr4n'  => '1',
        'matomo_lang'   => 'language=emgtY24=',
        'MATOMO_SESSID' => 'oaohf5io35h8hp2k589psecrpr',
    ];

    $_SERVER = [
        'USER'                           => 'apache',
        'HOME'                           => '/usr/share/httpd',
        'HTTP_COOKIE'                    => 'perf_dv6Tr4n=1; matomo_lang=language%3DemgtY24%3D; MATOMO_SESSID=oaohf5io35h8hp2k589psecrpr',
        'HTTP_ACCEPT_LANGUAGE'           => 'zh-CN,zh;q=0.9',
        'HTTP_ACCEPT_ENCODING'           => 'gzip, deflate',
        'HTTP_ACCEPT'                    => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
        'HTTP_USER_AGENT'                => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36',
        'HTTP_UPGRADE_INSECURE_REQUESTS' => '1',
        'HTTP_CACHE_CONTROL'             => 'max-age=0',
        'HTTP_CONNECTION'                => 'keep-alive',
        'HTTP_HOST'                      => 'dev6058',
        'REDIRECT_STATUS'                => '200',
        'SERVER_NAME'                    => 'dev6058',
        'SERVER_PORT'                    => '80',
        'SERVER_ADDR'                    => '192.168.0.201',
        'REMOTE_PORT'                    => '65293',
        'REMOTE_ADDR'                    => '192.168.0.111',
        'SERVER_SOFTWARE'                => 'nginx/1.20.1',
        'GATEWAY_INTERFACE'              => 'CGI/1.1',
        'REQUEST_SCHEME'                 => 'http',
        'SERVER_PROTOCOL'                => 'HTTP/1.1',
        'DOCUMENT_ROOT'                  => __DIR__,
        'DOCUMENT_URI'                   => '/matomo.php',
        'REQUEST_URI'                    => '/matomo.php',
        'SCRIPT_NAME'                    => '/matomo.php',
        'CONTENT_LENGTH'                 => '',
        'CONTENT_TYPE'                   => '',
        'REQUEST_METHOD'                 => 'GET',
        'QUERY_STRING'                   => '',
        'PATH_TRANSLATED'                => __DIR__,
        'PATH_INFO'                      => '',
        'SCRIPT_FILENAME'                => __DIR__ . '/matomo.php',
        'FCGI_ROLE'                      => 'RESPONDER',
        'PHP_SELF'                       => '/matomo.php',
        'REQUEST_TIME_FLOAT'             => microtime(true),
        'REQUEST_TIME'                   => time(),
    ];

    $_POST = $data;

    if (!defined('PIWIK_DOCUMENT_ROOT'))
    {
        define('PIWIK_DOCUMENT_ROOT', dirname(__FILE__) == '/' ? '' : dirname(__FILE__));
    }

    const IS_CLI_ACCESS = true;
    include PIWIK_DOCUMENT_ROOT . '/piwik.php';
