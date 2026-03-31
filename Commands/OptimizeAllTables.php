<?php

    namespace Piwik\Plugins\MyVisitorGenerator\Commands;

    use Piwik\Plugin\ConsoleCommand;
    use Piwik\Db;

    /**
     * 优化 Matomo 数据库所有表（OPTIMIZE TABLE）
     * 执行后会遍历当前数据库中所有表，执行一次 OPTIMIZE TABLE
     * 适合你刚删除大量原始数据后使用，能回收空间、整理碎片
     */
    class OptimizeAllTables extends ConsoleCommand
    {
        protected function configure()
        {
            $this->setName('optimize:all-tables');
            $this->setDescription('优化 Matomo 数据库中【所有表】（包括 log_、archive_ 等），回收磁盘空间并整理碎片。');
            $this->setHelp('建议在删除旧日志或大批量导入数据后运行此命令。');
        }

        protected function doExecute(): int
        {
            $output = $this->getOutput();
            $output->writeln('<info>🚀 开始优化 Matomo 所有数据表...</info>');

            $db        = Db::get();
            $startTime = microtime(true);

            // 获取当前数据库所有表
            $tables  = $db->fetchCol('SHOW TABLES');
            $total   = count($tables);
            $success = 0;

            $output->writeln("<info>共发现 {$total} 张表，开始逐一优化...</info>");

            foreach ($tables as $i => $table)
            {
                $index = $i + 1;
                $output->write(sprintf('  [%d/%d] 正在优化表 → %s ... ', $index, $total, $table));

                try
                {
                    // 执行 OPTIMIZE TABLE
                    $db->query("OPTIMIZE TABLE `{$table}`");
                    $output->writeln('<info>✅ 完成</info>');
                    $success++;
                }
                catch (\Exception $e)
                {
                    $output->writeln('<error>❌ 失败：' . $e->getMessage() . '</error>');
                }
            }

            $duration = round(microtime(true) - $startTime, 2);
            $output->writeln('');
            $output->writeln("<info>🎉 优化完成！共处理 {$total} 张表，成功 {$success} 张，耗时 {$duration} 秒。</info>");
            $output->writeln('<comment>建议定期在删除旧日志后运行此命令，或加入 crontab 每周执行一次。</comment>');

            return self::SUCCESS;
        }
    }