<?php

namespace emteknetnz\DBQueryCounter;

use SilverStripe\ORM\Connect\MySQLiConnector;
use emteknetnz\DBQueryCounter\DBQueryLogger;

class DBQueryMySQLiConnector extends MySQLiConnector
{
    private DBQueryLogger $logger;

    public function __construct()
    {
        $this->logger = new DBQueryLogger();
    }

    public function query($sql, $errorLevel = E_USER_ERROR)
    {
        $this->logger->log($sql);
        return parent::query($sql, $errorLevel);
    }

    public function preparedQuery($sql, $parameters, $errorLevel = E_USER_ERROR)
    {
        $this->logger->log($sql);
        return parent::preparedQuery($sql, $parameters, $errorLevel);
    }
}
