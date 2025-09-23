# MyVisitorGenerator

1，复制 cli_insert.php 到 matomo.php 同一目录下，项目根目录

2， plugins/BulkTracking/Tracker/Requests.php 修改 getRawBulkRequest 方法为下面这样

```php
    public function getRawBulkRequest()
    {
        if (defined('IS_CLI_ACCESS') && IS_CLI_ACCESS)
        {
            return json_encode($_POST);
        }
        else
        {
            return file_get_contents("php://input");
        }
    }
```

3，安装批量写记录插件
> 参考 https://plugins.matomo.org/QueuedTracking#faq
> 
> 参考 https://github.com/matomo-org/plugin-QueuedTracking/releases

4，项目根目录下执行 `php console development:enable`

5，在项目后台管理插件安装此插件

6，项目根目录下执行 `php console development:disable`

7，确保 config.ini.php 中 debug 没开启

```ini
[Tracker]
debug = 0
enable_sql_profiler = 0
```

8，开始传输，命令：./console visitorgenerator:my-generate-visits-db --idsite=1 --tokenauth=c5bb885c94da01883e26573827c63874
--wpurl="http://dev6080/" --starttime="2025-06-10" --endtime="2025-08-10" --lowuv=0 --yearuv=10000 --chunk=200
--pagename="kokokogames" --mysqlhost="127.0.0.1" --mysqlusername="root" --mysqlpassword="root" --mysqlport=3306
--mysqldb="wp_te_10100"
