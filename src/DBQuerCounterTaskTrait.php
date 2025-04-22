<?php

namespace emteknetnz\DBQueryCounter;

use SilverStripe\Core\Path;

trait DBQuerCounterTaskTrait
{
    protected $title = 'DB Query Counter Task';

    private function getQueryCounts(): array
    {
        $queryCounts = [];
        $logFile = (new DBQueryLogger)->getLogFilePath();
        $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $query = trim($line);
            if (!empty($query)) {
                if (!isset($queryCounts[$query])) {
                    $queryCounts[$query] = 0;
                }
                $queryCounts[$query]++;
            }
        }
        arsort($queryCounts);
        return $queryCounts;
    }

    private function writeReport(array $queryCounts): void
    {
        $reportFile = Path::join(sys_get_temp_dir(), 'db-query-counter', 'report.txt');
        if (!is_dir(dirname($reportFile))) {
            mkdir(dirname($reportFile), 0777, true);
        }
        $lines = [];
        $total = array_sum($queryCounts);
        $lines[] = "Total queries: $total";
        foreach ($queryCounts as $query => $count) {
            $lines[] = "Count: $count - Query: $query";
        }
        file_put_contents($reportFile, implode(PHP_EOL . PHP_EOL, $lines));
        $intoFile = Path::join(BASE_PATH, 'report.txt');
        echo implode(PHP_EOL, [
            "Report written to: $reportFile",
            "To copy to root of project, run:",
            "cat $reportFile > $intoFile"
        ]) . PHP_EOL;
    }
}
