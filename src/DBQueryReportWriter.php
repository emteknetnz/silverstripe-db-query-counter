<?php

namespace emteknetnz\DBQueryCounter;

use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Path;

class DBQueryReportWriter
{
    use Configurable;

    public function __construct(
        private bool $doEcho = true,
    ) {}

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
        $outfile = DBQueryReportWriter::config()->get('outfile');
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
        if ($total == 0) {
            $this->echo("Did not write to $reportFile as total is 0");
            return;
        }
        file_put_contents($reportFile, implode(PHP_EOL . PHP_EOL, $lines));
        $this->echo("Wrote $total queries to $reportFile");
        $intoFile = Path::join(BASE_PATH, 'report.txt');
        if ($intoFile !== $reportFile) {
            $this->echo(
                "Report written to: $reportFile",
                "To copy to root of project, run:",
                "cat $reportFile > $intoFile"
            );
        }
    }

    private function echo(string ...$lines): void
    {
        if (!$this->doEcho) {
            return;
        }
        echo implode(PHP_EOL, $lines) . PHP_EOL;
    }
}
