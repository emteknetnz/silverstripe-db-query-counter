# Query Counter Module

This is a SilverStripe module for counting and analyzing database queries.

## Installation

```bash
composer require emteknetnz/db-query-counter
```

You will need to flush TWICE because of the unorthodox way dual support has been implemented (see notes).

```bash
vendor/bin/sake dev/tasks flush=1
vendor/bin/sake dev/tasks flush=1
```

## Usage

- Add `?log=1` to the URL to enable query logging for the current request. You should probably delete this from the URL bar straight away so that extra queries are not logged on subsequent requests.
- To reset the query log, add `?reset=1` to the URL
- The view a report of the queries, run `vendor/bin/sake dev/tasks/QueryCounterTask` from the command line.

By default a `report.txt` file will be written to the system temporary directory. To change where this file is written:

```yml
emteknetnz\DBQueryCounter\DBQueryCounterTask:
  outfile: /path/to/report.txt
```

## Notes

- The log and report are written to the system temp directory
- A `DBQueryMySQLiConnector` class is injected in to replace `MySQLiConnector` to allow for logging database queries. If you have a custom connector, you will need to extend this class instead of `MySQLiConnector`.
- Dual support for CMS 5 and CMS 6 has been implemented by copying in the relevant `<version>.php.inc` to `DBCounterTask` in `_config.php`. This necessitates flushing twice in order for things to be picked up by the Silverstripe class loader. This unorthodox method for dual support was chosen over the usual `class_exists() ... class Foo` as that methoed appeared incompatible with the way that `BuildTask`'s are picked up.
