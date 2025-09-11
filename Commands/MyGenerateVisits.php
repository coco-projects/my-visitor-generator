<?php

    namespace Piwik\Plugins\MyVisitorGenerator\Commands;

    use Piwik\Plugin\ConsoleCommand;
    use Piwik\Plugins\Marketplace\Api\Exception;
    use Coco\wp\Manager;
    use Coco\matomo\MatomoClient;
    use Coco\matomo\MatomoWebApiClient;
    use Coco\matomo\Uv;
    use Coco\wp\VisitorGeneratorGeneral;

    class MyGenerateVisits extends ConsoleCommand
    {
        public string $token       = '';
        public array  $redisConfig = [
            'host'     => '127.0.0.1',
            'port'     => 6379,
            'password' => '',
            'key'      => 'visitors_records',
            'database' => 2,
        ];

//      ./console visitorgenerator:my-generate-visits-db --idsite=1 --tokenauth=c5bb885c94da01883e26573827c63874 --wpurl="http://dev6080/" --starttime="2025-07-27" --endtime="2025-09-10" --lowuv=0 --yearuv=500 --pagename="kokokogames" --mysqlhost="127.0.0.1" --mysqlusername="root" --mysqlpassword="root" --mysqlport=3306 --mysqldb="wp_te_10100"
        protected function configure()
        {
            $this->setName('visitorgenerator:my-generate-visits-db');
            $this->addRequiredValueOption('idsite', null, '网站id');
            $this->addRequiredValueOption('tokenauth', null, '');

            $this->addRequiredValueOption('wpurl', null, 'wp站的域名，如：http://dev6080/');
            $this->addRequiredValueOption('pagename', null, 'wp的网站名');
            $this->addRequiredValueOption('starttime', null, '开始时间，可以精确到时分秒：2025-02-13');
            $this->addRequiredValueOption('endtime', null, '结束时间，不用写时分秒，始终到当天晚上 2025-08-25 23:59:59');
            $this->addRequiredValueOption('lowuv', null, '起始uv，不写默认0');
            $this->addRequiredValueOption('yearuv', null, '结束时间的uv，默认10000');
            $this->addRequiredValueOption('chunk', null, '');

            $this->addRequiredValueOption('redisnamespace', null, '');
            $this->addRequiredValueOption('redishost', null, '');
            $this->addRequiredValueOption('redispassword', null, '');
            $this->addRequiredValueOption('redisport', null, '');
            $this->addRequiredValueOption('redisdb', null, '');

            $this->addRequiredValueOption('mysqlhost', null, '');
            $this->addRequiredValueOption('mysqlusername', null, '');
            $this->addRequiredValueOption('mysqlpassword', null, '');
            $this->addRequiredValueOption('mysqlport', null, '');
            $this->addRequiredValueOption('mysqldb', null, '');
        }

        protected function doExecute(): int
        {
            $idsite    = $this->getOptionWithException('idsite', 'no $idsite');
            $tokenauth = $this->getOptionWithException('tokenauth', 'no $tokenauth');

            $wpurl     = $this->getOptionWithException('wpurl', 'no $wpurl');
            $pagename  = $this->getOptionWithException('pagename', 'no $pagename');
            $starttime = $this->getOptionWithException('starttime', 'no $starttime');
            $endtime   = $this->getOptionWithException('endtime', 'no $endtime');
            $lowuv     = $this->getOptionWithDefault('lowuv', 0);
            $yearuv    = $this->getOptionWithDefault('yearuv', 10000);
            $chunk     = $this->getOptionWithDefault('chunk', 10);

            $redisnamespace = $this->getOptionWithDefault('redisnamespace', 'matomo_cache');
            $redishost      = $this->getOptionWithDefault('redishost', '127.0.0.1');
            $redispassword  = $this->getOptionWithDefault('redispassword', '');
            $redisport      = $this->getOptionWithDefault('redisport', 6379);
            $redisdb        = $this->getOptionWithDefault('redisdb', 10);

            $host     = $this->getOptionWithDefault('mysqlhost', '127.0.0.1');
            $username = $this->getOptionWithDefault('mysqlusername', 'root');
            $password = $this->getOptionWithDefault('mysqlpassword', '');
            $port     = $this->getOptionWithDefault('mysqlport', 3306);
            $db       = $this->getOptionWithException('mysqldb', 'no $db');

            /*------------------------------------
            ------------------------------------
            ---------------------------------------*/
            $this->token       = $tokenauth;
            $this->redisConfig = [
                'host'     => $redishost,
                'port'     => $redisport,
                'password' => $redispassword,
                'database' => $redisdb,
                'key'      => 'visitors_records',
            ];

            $manager = new Manager($redisnamespace);

            $manager->setRedisConfig($redishost, $redispassword, $redisport, $redisdb);

            $manager->setMysqlConfig($db, $host, $username, $password, $port,);

            $manager->setEnableEchoLog(true);
            $manager->setEnableRedisLog(false);

            $manager->initServer();
            $manager->initTableStruct();

            $generator = new VisitorGeneratorGeneral($idsite, $manager, $wpurl, $this->initFunc($manager), $this->addUvFunc($manager), $this->writeRecordFunc($manager));
            $generator->setChunkSize($chunk);
            $generator->setEnableEchoLog(true);
            $generator->setPageName($pagename);
            $generator->setStartTime($starttime);
            $generator->setEndTime($endtime);
            $generator->setLowUv($lowuv);
            $generator->setHightUv($yearuv);

            $generator->listenDoWrite();

            return self::SUCCESS;
        }

        public function initFunc(Manager $manager)
        {
            return function(VisitorGeneratorGeneral $_this) use ($manager) {

                MatomoWebApiClient::initLogger('VisitorGeneratorByInsertToDb', $_this->enableEchoLog);

                $_this->client = new MatomoClient($_this->siteId);
                $_this->client->setChunkSize($_this->chunkSize);
            };
        }

        public function addUvFunc(Manager $manager): \Closure
        {
            return function(Uv $uvObj, VisitorGeneratorGeneral $_this) use ($manager) {
                $_this->client->addUv($uvObj);
            };
        }

        public function writeRecordFunc(Manager $manager): \Closure
        {
            return function(VisitorGeneratorGeneral $_this) use ($manager) {

                $_this->client->eachChunks(function($uvsChunk, $k) use (&$_this, $manager) {

                    $redisClient = $manager->getRedisClient();

                    foreach ($uvsChunk as $k1 => $uv)
                    {
                        $data = [
                            'requests'   => $uv->makeRequests($_this->siteId),
                            'token_auth' => $this->token,
                        ];

                        $redisClient->set($this->redisConfig['key'], json_encode($data), [
                            "EX" => 600,
                        ]);

                        $redisConfigBase64 = base64_encode(json_encode($this->redisConfig));

                        $piwik = realpath(dirname(dirname(dirname(__DIR__)))) . '/cli_insert.php';

                        $commond = implode(' ', [
                            'php',
                            $piwik,
                            $redisConfigBase64,
                        ]);

                        $_this->client->logInfo('命令：' . $commond);
                        $_this->client->logInfo('发送中，共(' . $_this->client->getUvsCount() . ')个uv,当前第(' . (($k * $_this->client->chunkSize) + 1) . '-' . (($k + 1) * $_this->client->chunkSize) . ')个,第' . ($k1 + 1) . '个uv,共' . $uv->getPvCount() . '个pv,uv访问时间:' . $uv->viewTime,);
                        exec($commond);
                        $_this->client->logInfo('uv发送成功');
                    }

                });

            };
        }

        protected function getOptionWithDefault(string $name, string $defaultValue)
        {
            $value = $this->getInput()->getOption($name);
            if (!$value)
            {
                $value = $defaultValue;
            }

            return $value;
        }


        protected function getOptionWithException(string $name, string $msg)
        {
            $value = $this->getInput()->getOption($name);
            if (!$value)
            {
                throw new Exception($msg);
            }

            return $value;
        }

    }
