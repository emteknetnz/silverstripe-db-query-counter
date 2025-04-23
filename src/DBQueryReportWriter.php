<?php

namespace emteknetnz\DBQueryCounter;

use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Path;

class DBQueryReportWriter
{
    use Configurable;

    private static string $outfile = '';

    private static string $outfile_trace = '';

    public function run()
    {
        $queryCounts = $this->getQueryCounts(false);
        $this->writeReports(false, $queryCounts);
        $queryCounts = $this->getQueryCounts(true);
        $this->writeReports(true, $queryCounts);
    }

    private function getQueryCounts(bool $trace): array
    {
        $queryCounts = [];
        if ($trace) {
            $logFile = DBQueryLogger::getLogTraceFilePath();
        } else {
            $logFile = DBQueryLogger::getLogFilePath();
        }
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

    private function writeReports(bool $trace, array $queryCounts): void
    {
        if ($trace) {
            $outfile = DBQueryReportWriter::config()->get('outfile_trace');
        } else {
            $outfile = DBQueryReportWriter::config()->get('outfile');
        }
        if ($outfile) {
            $reportFile = $outfile;
        } else {
            if ($trace) {
                $reportFile = Path::join(sys_get_temp_dir(), 'db-query-counter', 'report-trace.txt');
            } else {
                $reportFile = Path::join(sys_get_temp_dir(), 'db-query-counter', 'report.txt');
            }
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
