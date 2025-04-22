<?php

namespace emteknetnz\DBQueryCounter;

use SilverStripe\Dev\BuildTask;
use SilverStripe\View\ViewableData_Debugger;

// CMS 5 class not in CMS 6
if (!class_exists(ViewableData_Debugger::class)) {
    return;
}

class DBQueryCounterTaskCMS5 extends BuildTask
{
    use DBQuerCounterTaskTrait;

    public function run($request)
    {
        $queryCounts = $this->getQueryCounts();
        $this->writeReport($queryCounts);
    }
}
