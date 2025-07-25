<?php

return [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [
            // 数据库类型
            'type' => getenv('DB_TYPE') ?? 'mysql',
            // 服务器地址
            'hostname' => getenv('DB_HOST') ?? '127.0.0.1',
            // 数据库名
            'database' => getenv('DB_DATABASE') ?? 'pi_admin',
            // 数据库用户名
            'username' => getenv('DB_USER') ?? 'root',
            // 数据库密码
            'password' => getenv('DB_PASSWORD') ?? 'root',
            // 数据库连接端口
            'hostport' => getenv('DB_PORT') ?? '3306',
            // 数据库连接参数
            'params' => [
                // 连接超时3秒
                \PDO::ATTR_TIMEOUT => 3,
            ],
            // 数据库编码默认采用utf8
            'charset' => getenv('DB_CHARSET') ?? 'utf8bm4',
            // 数据库表前缀
            'prefix' => getenv('DB_PREFIX') ?? 'pi_',
            // 断线重连
            'break_reconnect' => true,
            // 自定义分页类
            'bootstrap' =>  '',
            // 连接池配置
            'pool' => [
                'max_connections' => getenv('DB_POOL_MAX_CONNECTIONS') ?? 5, // 最大连接数
                'min_connections' => getenv('DB_POOL_MIN_CONNECTIONS') ?? 3, // 最小连接数
                'wait_timeout' => getenv('DB_POOL_MAX_IDLE_TIMEOUT') ?? 3,    // 从连接池获取连接等待超时时间
                'idle_timeout' => getenv('DB_POOL_MAX_WAIT_TIMEOUT') ?? 30,   // 连接最大空闲时间，超过该时间会被回收
                'heartbeat_interval' => getenv('DB_POOL_HEARTBEAT_INTERVAL') ?? 50, // 心跳检测间隔，需要小于60秒
            ],
        ],
    ],
];
