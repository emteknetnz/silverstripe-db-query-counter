<?php

namespace emteknetnz\DBQueryCounter;

use SilverStripe\Dev\BuildTask;
use Symfony\Component\Console\Input\InputInterface;
use emteknetnz\DBQueryCounter\DBQueryCounterTaskTrait;

class DBQueryCounterTask extends BuildTask
{
    protected $title = 'DB Query Counter Task';

    private static string $segment = 'DBQueryCounterTask';

    public function run($request)
    {
        (new DBQueryReportWriter)->run();
    }
}
