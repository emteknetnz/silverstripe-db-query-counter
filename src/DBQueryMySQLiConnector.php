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
        // mirror what happens in MySQLiConnector::preparedQuery
        // do this to prevent double query logging
        if (empty($parameters)) {
            return $this->query($sql, $errorLevel);
        }
        $this->logger->log($sql);
        return parent::preparedQuery($sql, $parameters, $errorLevel);
    }
}
