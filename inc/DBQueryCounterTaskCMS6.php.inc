<?php

namespace emteknetnz\DBQueryCounter;

use SilverStripe\Dev\BuildTask;
use SilverStripe\PolyExecution\PolyOutput;
use Symfony\Component\Console\Input\InputInterface;
use emteknetnz\DBQueryCounter\DBQueryCounterTaskTrait;

class DBQueryCounterTask extends BuildTask
{
    protected string $title = 'DB Query Counter Task';

    protected static string $commandName = 'DBQueryCounterTask';

    protected function execute(InputInterface $input, PolyOutput $output): int
    {
        (new DBQueryReportWriter)->run();
        return 0;
    }
}
