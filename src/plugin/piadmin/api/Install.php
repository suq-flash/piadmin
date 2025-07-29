<?php

namespace plugin\piadmin\api;

use plugin\admin\api\Menu;
use support\Db;
use Throwable;

class Install
{

    /**
     * 数据库连接
     */
    protected static $connection = 'plugin.admin.mysql';
    
    /**
     * 安装
     *
     * @param $version
     * @return void
     */
    public static function install($version)
    {

        var_dump('piadmin install.....');

        //处理env文件，修改config/think-orm.php,redis.php,cache.php配置从env获取
        $env = base_path() . DIRECTORY_SEPARATOR .'.env';

        clearstatcache();
        if (is_file($env)) {
            var_dump('env文件存在,请勿重复安装');
            return;
        }

        if (!is_writable(base_path() . DIRECTORY_SEPARATOR . 'config')) {
            var_dump('文件权限验证失败');
            return;
        }

        // 重写配置文件
        self::generateConfig();

        // 写入env文件
        $env_config = <<<EOF
# 数据库配置
DB_TYPE = mysql
DB_HOST = 127.0.0.1
DB_PORT = 3306
DB_NAME = piadmin
DB_USER = piadmin
DB_PASSWORD = 123456
DB_PREFIX = 

# 缓存方式
CACHE_MODE = file

# Redis配置
REDIS_HOST = 127.0.0.1
REDIS_PORT = 6379
REDIS_PASSWORD = ''
REDIS_DB = 0
REDIS_PREFIX = pi_adm_

EOF;
        file_put_contents(base_path() . DIRECTORY_SEPARATOR . '.env', $env_config);

        var_dump('安装成功,请修改env配置文件后，导入/plugin/piadmin/install.sql文件');

        // 安装数据库
//        static::installSql();
//        // 导入菜单
//        if($menus = static::getMenus()) {
//            Menu::import($menus);
//        }




    }

    /**
     * 生成配置文件
     */
    protected static function generateConfig()
    {
        // 1、think-orm配置文件
        $think_orm_config = <<<EOF
<?php

return [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [
            // 数据库类型
            'type' => getenv('DB_TYPE'),
            // 服务器地址
            'hostname' => getenv('DB_HOST'),
            // 数据库名
            'database' => getenv('DB_NAME'),
            // 数据库用户名
            'username' => getenv('DB_USER'),
            // 数据库密码
            'password' => getenv('DB_PASSWORD'),
            // 数据库连接端口
            'hostport' => getenv('DB_PORT'),
            // 数据库连接参数
            'params' => [
                // 连接超时3秒
                \PDO::ATTR_TIMEOUT => 3,
            ],
            // 数据库编码默认采用utf8
            'charset' => 'utf8',
            // 数据库表前缀
            'prefix' => getenv('DB_PREFIX'),
            // 断线重连
            'break_reconnect' => true,
            // 自定义分页类
            'bootstrap' =>  '',
            // 连接池配置
            'pool' => [
                'max_connections' => 5, // 最大连接数
                'min_connections' => 1, // 最小连接数
                'wait_timeout' => 3,    // 从连接池获取连接等待超时时间
                'idle_timeout' => 60,   // 连接最大空闲时间，超过该时间会被回收
                'heartbeat_interval' => 50, // 心跳检测间隔，需要小于60秒
            ],
        ],
    ],
];
EOF;
        file_put_contents(base_path() . '/config/think-orm.php', $think_orm_config);

        // 2、chache配置文件
        $cache_config = <<<EOF
<?php

return [
    'default' => getenv('CACHE_MODE'),
    'stores' => [
        'file' => [
            'driver' => 'file',
            'path' => runtime_path('cache')
        ],
        'redis' => [
            'driver' => 'redis',
            'connection' => 'default'
        ],
        'array' => [
            'driver' => 'array'
        ]
    ]
];
EOF;
        file_put_contents(base_path() . '/config/cache.php', $cache_config);

        // 3、redis配置文件
        $redis_config = <<<EOF
<?php

return [
    'default' => [
        'password' => getenv('REDIS_PASSWORD'),
        'host' => getenv('REDIS_HOST'),
        'port' => getenv('REDIS_PORT'),
        'database' => getenv('REDIS_DB'),
        'prefix' => getenv('REDIS_PREFIX'),
        'pool' => [
            'max_connections' => 5,
            'min_connections' => 1,
            'wait_timeout' => 3,
            'idle_timeout' => 60,
            'heartbeat_interval' => 50,
        ],
    ]
];
EOF;
        file_put_contents(base_path() . '/config/redis.php', $redis_config);

    }

    /**
     * 卸载
     *
     * @param $version
     * @return void
     */
    public static function uninstall($version)
    {
        // 删除菜单
        foreach (static::getMenus() as $menu) {
            Menu::delete($menu['key']);
        }
        // 卸载数据库
        static::uninstallSql();
    }

    /**
     * 更新
     *
     * @param $from_version
     * @param $to_version
     * @param $context
     * @return void
     */
    public static function update($from_version, $to_version, $context = null)
    {
        // 删除不用的菜单
        if (isset($context['previous_menus'])) {
            static::removeUnnecessaryMenus($context['previous_menus']);
        }
        // 安装数据库
        static::installSql();
        // 导入新菜单
        if ($menus = static::getMenus()) {
            Menu::import($menus);
        }
        // 执行更新操作
        $update_file = __DIR__ . '/../update.php';
        if (is_file($update_file)) {
            include $update_file;
        }
    }

    /**
     * 更新前数据收集等
     *
     * @param $from_version
     * @param $to_version
     * @return array|array[]
     */
    public static function beforeUpdate($from_version, $to_version)
    {
        // 在更新之前获得老菜单，通过context传递给 update
        return ['previous_menus' => static::getMenus()];
    }

    /**
     * 获取菜单
     *
     * @return array|mixed
     */
    public static function getMenus()
    {
        clearstatcache();
        if (is_file($menu_file = __DIR__ . '/../config/menu.php')) {
            $menus = include $menu_file;
            return $menus ?: [];
        }
        return [];
    }

    /**
     * 删除不需要的菜单
     *
     * @param $previous_menus
     * @return void
     */
    public static function removeUnnecessaryMenus($previous_menus)
    {
        $menus_to_remove = array_diff(Menu::column($previous_menus, 'name'), Menu::column(static::getMenus(), 'name'));
        foreach ($menus_to_remove as $name) {
            Menu::delete($name);
        }
    }
    
    /**
     * 安装SQL
     *
     * @return void
     */
    protected static function installSql()
    {
        static::importSql(__DIR__ . '/../install.sql');
    }
    
    /**
     * 卸载SQL
     *
     * @return void
     */
    protected static function uninstallSql() {
        // 如果卸载数据库文件存在责直接使用
        $uninstallSqlFile = __DIR__ . '/../uninstall.sql';
        if (is_file($uninstallSqlFile)) {
            static::importSql($uninstallSqlFile);
            return;
        }
        // 否则根据install.sql生成卸载数据库文件uninstall.sql
        $installSqlFile = __DIR__ . '/../install.sql';
        if (!is_file($installSqlFile)) {
            return;
        }
        $installSql = file_get_contents($installSqlFile);
        preg_match_all('/CREATE TABLE `(.+?)`/si', $installSql, $matches);
        $dropSql = '';
        foreach ($matches[1] as $table) {
            $dropSql .= "DROP TABLE IF EXISTS `$table`;\n";
        }
        file_put_contents($uninstallSqlFile, $dropSql);
        static::importSql($uninstallSqlFile);
        unlink($uninstallSqlFile);
    }
    
    /**
     * 导入数据库
     *
     * @return void
     */
    public static function importSql($mysqlDumpFile)
    {
        if (!$mysqlDumpFile || !is_file($mysqlDumpFile)) {
            return;
        }
        foreach (explode(';', file_get_contents($mysqlDumpFile)) as $sql) {
            if ($sql = trim($sql)) {
                try {
                    Db::connection(static::$connection)->statement($sql);
                } catch (Throwable $e) {}
            }
        }
    }

}