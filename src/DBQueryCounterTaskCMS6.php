<?php

namespace emteknetnz\DBQueryCounter;

use SilverStripe\Dev\BuildTask;
use SilverStripe\PolyExecution\PolyOutput;
use Symfony\Component\Console\Input\InputInterface;

// CMS 6 class not in CMS 5
if (!class_exists(PolyOutput::class)) {
    return;
}

class DBQueryCounterTaskCMS6 extends BuildTask
{
    use DBQuerCounterTaskTrait;

    protected function execute(InputInterface $input, PolyOutput $output): int
    {
        $queryCounts = $this->getQueryCounts();
        $this->writeReport($queryCounts);
    }
}
