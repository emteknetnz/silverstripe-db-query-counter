<?php

use SilverStripe\Dev\BuildTask;
use SilverStripe\PolyExecution\PolyOutput;
use Symfony\Component\Console\Input\InputInterface;
use emteknetnz\DBQueryCounter\DBQueryCounterTaskTrait;

class DBQueryCounterTask extends BuildTask
{
    use DBQueryCounterTaskTrait;

    protected string $title = 'DB Query Counter Task';

    protected function execute(InputInterface $input, PolyOutput $output): int
    {
        $queryCounts = $this->getQueryCounts();
        $this->writeReport($queryCounts);
        return 0;
    }
}
