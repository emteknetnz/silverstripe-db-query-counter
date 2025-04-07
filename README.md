# Query Counter Module

This is a SilverStripe module for counting and analyzing database queries.

## Installation

```bash
composer require emteknetnz/db-query-counter
```

## Usage

- Add `?log=1` to the URL to enable query logging for the current request. You should probably delete this from the URL bar straight away so that extra queries are not logged on subsequent requests.
- To reset the query log, add `?reset=1` to the URL
- The view a report of the queries, run `vendor/bin/sake dev/tasks/QueryCounterTask` from the command line.

## Notes

- The log and report are written to the system temp directory
- A `DBQueryMySQLiConnector` class is injected in to replace `MySQLiConnector` to allow for logging database queries. If you have a custom connector, you will need to extend this class instead of `MySQLiConnector`.
