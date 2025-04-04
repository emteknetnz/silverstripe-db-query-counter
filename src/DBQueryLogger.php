<?php

namespace emteknetnz\DBQueryCounter;

use SilverStripe\Core\Path;

class DBQueryLogger
{
    public function __construct()
    {
        $this->ensureLogFileExists();
        if (isset($_GET['reset'])) {
            $this->reset();
        }
    }

    public function log($sql)
    {
        if (!isset($_GET['log'])) {
            return;
        }
        $sql = preg_replace("#\s+#", ' ', $sql);
        file_put_contents($this->getLogFilePath(), $sql . PHP_EOL, FILE_APPEND);
    }

    public function getLogFilePath()
    {
        return Path::join(sys_get_temp_dir(), 'db-query-counter', 'queries.log');
    }

    public function reset()
    {
        file_put_contents($this->getLogFilePath(), '');
    }

    private function ensureLogFileExists()
    {
        $logFile = $this->getLogFilePath();
        if (file_exists($logFile)) {
            return;
        }
        $logDir = dirname($logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        $this->reset();
    }
}