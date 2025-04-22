<?php

use SilverStripe\Dev\BuildTask;
use Symfony\Component\Console\Input\InputInterface;
use emteknetnz\DBQueryCounter\DBQueryCounterTaskTrait;

class DBQueryCounterTask extends BuildTask
{
    use DBQueryCounterTaskTrait;

    protected $title = 'DB Query Counter Task';

    public function run($request)
    {
        $queryCounts = $this->getQueryCounts();
        $this->writeReport($queryCounts);
    }
}
