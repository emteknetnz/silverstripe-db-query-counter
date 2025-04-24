# Query Counter Module

This is a SilverStripe module for counting the number of database queries made.

## Installation

```bash
composer require emteknetnz/silverstripe-db-query-counter
```

And then flush.

## Usage

Add `?log=1` to the URL to enable query logging for the current request, including any sub XHR requests. This will redirect to `?log=0` after 3 seconds.

To change the number of seconds before the redirect:

```yml
emteknetnz\DBQueryCounter\DBQueryMiddleware:
  redirect_seconds: 5
```

By default two files `queries.txt` and `queries-trace.txt` will be written to a `db-query-counter` subdirectory in the system temporary directory. To change where these files are written:

```yml
emteknetnz\DBQueryCounter\DBQueryLogger:
  logfile: /path/to/queries.txt
  logfile_trace: /path/to/queries-trace.txt
```

Aftet the automatic redirect to `?log=0`, by default two files `report.txt` and `report-trace.txt` will be written to a `db-query-counter` subdirectory in the system temporary directory. To change where these files are written:

```yml
emteknetnz\DBQueryCounter\DBQueryReportWriter:
  outfile: /path/to/report.txt
  outfile_trace: /path/to/report-trace.txt
```

To help find out what triggered the DB query, `report-trace.txt` file include a stack trace of callees, with common core classes such as ORM classes filtered out. By default this will be done to a depth of 10, though this is configurable, which can be useful for grouping similar queries. Note this has no impact on performance, only on what's reported:

```yml
emteknetnz\DBQueryCounter\DBQueryLogger:
  trace_depth: 3
```

## Notes

- A `DBQueryMySQLiConnector` class is injected in to replace `MySQLiConnector` to allow for logging database queries. If you have a custom connector, you will need to extend this class instead of `MySQLiConnector`.
