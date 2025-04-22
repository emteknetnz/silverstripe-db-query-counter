<?php

namespace emteknetnz\DBQueryCounter;

use SilverStripe\Dev\BuildTask;
use SilverStripe\Dev\Command\GenerateSecureToken;

// CMS 6 class not in CMS 5
if (!class_exists(GenerateSecureToken::class)) {
    return;
}

class DBQueryCounterTaskCMS6 extends BuildTask
{
    use DBQuerCounterTaskTrait;

    protected function execute(InputInterface $input, PolyOutput $output): int;
    {
        $queryCounts = $this->getQueryCounts();
        $this->writeReport($queryCounts);
    }
}
