<?php

namespace emteknetnz\DBQueryCounter;

use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Path;

class DBQueryReportWriter
{
    use Configurable;

    private static string $outfile = '';

    public function run()
    {
        $count = $this->getQueryCounts();
        $this->writeReport($count);
    }

    private function getQueryCounts(): array
    {
        $queryCounts = [];
        $logFile = DBQueryLogger::getLogFilePath();
        $contents = file_get_contents($logFile);
        foreach (explode(PHP_EOL . PHP_EOL, $contents) as $query) {
            $query = trim($query);
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
        $outfile = DBQueryReportWriter::config()->get('outfile');
        if ($outfile) {
            $reportFile = $outfile;
        } else {
            $reportFile = Path::join(sys_get_temp_dir(), 'db-query-counter', 'report.txt');
        }
        if (!is_dir(dirname($reportFile))) {
            mkdir(dirname($reportFile), 0777, true);
        }
        $total = array_sum($queryCounts);
        if ($total === 0) {
            return;
        }
        $lines = [];
        $lines[] = "Total queries: $total";
        foreach ($queryCounts as $query => $count) {
            $lines[] = "Count: $count - Query: $query";
        }
        file_put_contents($reportFile, implode(PHP_EOL . PHP_EOL, $lines));
    }
}
