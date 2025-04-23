<?php

namespace emteknetnz\DBQueryCounter;

use SilverStripe\Control\Controller;
use SilverStripe\Core\Path;

class DBQueryLogger
{
    public function __construct()
    {
        $this->ensureLogFileExists();
    }

    public function log($sql)
    {
        if (!$this->sessionVal('log')) {
            return;
        }
        $sql = preg_replace("#\s+#", ' ', $sql);
        file_put_contents($this->getLogFilePath(), $sql . PHP_EOL, FILE_APPEND);
    }

    public static function getLogFilePath()
    {
        return Path::join(sys_get_temp_dir(), 'db-query-counter', 'queries.log');
    }

    public static function reset()
    {
        file_put_contents(static:: getLogFilePath(), '');
    }

    private function sessionVal(string $key): bool
    {
        if (method_exists(Controller::class, 'has_curr')) {
            if (!call_user_func([Controller::class, 'has_curr'])) {
                return false;
            }
        }
        return (bool) Controller::curr()?->getRequest()?->getSession()?->get($key);
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