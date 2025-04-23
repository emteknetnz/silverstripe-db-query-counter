<?php

namespace emteknetnz\DBQueryCounter;

use SilverStripe\Control\Controller;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Path;

class DBQueryLogger
{
    use Configurable;

    private static int $trace_depth = 10;

    public function __construct()
    {
        $this->ensureLogFileExists();
    }

    public function log($sql)
    {
        if (!$this->sessionVal('log')) {
            return;
        }
        $trace = debug_backtrace(options: DEBUG_BACKTRACE_IGNORE_ARGS, limit: 50);
        $trace = $this->filterTrace($trace);
        $callees = [];
        $depth = DBQueryLogger::config()->get('trace_depth');
        for ($i = 0; $i < $depth; $i++) {
            if (!array_key_exists($i, $trace)) {
                continue;
            }
            $arr = $trace[$i];
            $callees[] = $arr['file'] . ':' . $arr['line'];
        }
        $sql = preg_replace("#\s+#", ' ', $sql);
        $line = implode(PHP_EOL, [$sql, ...$callees]) . PHP_EOL . PHP_EOL;
        file_put_contents($this->getLogFilePath(), $line, FILE_APPEND);
    }

    public static function getLogFilePath()
    {
        return Path::join(sys_get_temp_dir(), 'db-query-counter', 'queries.log');
    }
    
    public static function reset()
    {
        file_put_contents(static:: getLogFilePath(), '');
    }

    private function filterTrace(array $trace)
    {
        return array_values(array_filter($trace, function($arr) {
            if (!array_key_exists('file', $arr)) {
                return false;
            }
            if (array_key_exists('class', $arr)) {
                $strs = [
                    'emteknetnz\\DBQueryCounter\\',
                    'SilverStripe\\ORM\\Connect\\',
                ];
                foreach ($strs as $str) {
                    if (str_contains($arr['class'], $str)) {
                        return false;
                    }
                }
            }
            $strs = [
                'silverstripe/framework/src/ORM/DataQuery.php',
                'silverstripe/framework/src/ORM/DataList.php',
                'silverstripe/framework/src/ORM/Queries/SQLExpression.php',
                'silverstripe/framework/src/ORM/DB.php',
                'silverstripe/framework/src/Model/List/ArrayList.php', // todo CMS5
                'silverstripe/framework/src/Core/Extensible.php',
                'silverstripe/framework/src/Core/CustomMethods.php',
                'silverstripe/framework/src/Core/Extension.php',
            ];
            foreach ($strs as $str) {
                if (str_contains($arr['file'], $str)) {
                    return false;
                }
            }
            return true;
        }));
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