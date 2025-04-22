<?php

namespace emteknetnz\DBQueryCounter;

use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Path;

trait DBQueryCounterTaskTrait
{
    use Configurable;

    private static string $outfile = '';

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
        $outfile = DBQueryCounterTask::config()->get('outfile');
        if ($outfile) {
            $reportFile = $outfile;
        } else {
            $reportFile = Path::join(sys_get_temp_dir(), 'db-query-counter', 'report.txt');
        }
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
        echo "Wrote $total queries to $reportFile" . PHP_EOL;
        $intoFile = Path::join(BASE_PATH, 'report.txt');
        if ($intoFile !== $reportFile) {
            echo implode(PHP_EOL, [
                "Report written to: $reportFile",
                "To copy to root of project, run:",
                "cat $reportFile > $intoFile"
            ]) . PHP_EOL;
        }
    }
}
