# Logger Service
[![Total Downloads](https://img.shields.io/packagist/dt/ialopezg/logger.svg?style=flat-square)](https://packagist.org/packages/ialopezg/logger)
[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE.txt)

Logger Service for PHP. Easily and customizable logger service. Allow to you write log files, in custom paths or directories, file extension, date format, message indentation and several levels.

**Table of Contents**

- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#basic-usage)
    - [Options](#options)
    - [Methods details](#method-details)
- [License](#license)

## Requirements

* PHP 5.6+

## Installation

Install latest version via composer:

```shell script
composer require ialopezg/logger
```

## Usage instructions

### Basic usage

First, create a new "Logger" instance. Logger aims to make configuring the library as easy as possible.

```php
$logger = new \ialopezg\Services\Logger([
    'log_path' => 'logs'
]);
```

For more options see [examples](examples) directory.

### Options

This is a list of possible options that can be passed when creating a logger instance:

* `enabled` if logger can write to the log files. Default: `true`.
* `log_date_format` log date format. Default: `Y-m-d H:i:s`.
* `log_file_extension` log file extension. Default: `log`.
* `log_path:` log path where log files will be written. If not specified or not writable, modify `Logger::enabled` property to `false`.
* `log_file_permissions` log files permissions. Default: `0644`, read and write for owner, read for everybody else.
* `log_indented` message indentation. Default: `true`.
* `log_threshold` log threshold. Default: `1` or `error`. Accepts single values or array of values. Accept single or array of values. Accepted values: `error`, `debug`, `info`, `warning`, `all`.

### Methods

| Method | Description |
|---|---|
| <a href="#logger_log">`log()`</a> | Static function that write a log message line into the default log system, new or empty log system. |
| <a href="#logger_write">`write()`</a> | Write a log message line. |

#### Method Details

##### <a name="logger_log"></a> Method: `log()`

```php
/**
 * Log a message into the default log system. If the log system does not exists, will create a new one.
 *
 * @param int $level log message level. Accepts: `debug`, `error`, `info` and `warning` messages.
 * @param string $message log message.
 *
 * @return bool <code>true</code> if line was successfully wrote, <code>false</code> otherwise.
 */
public static function log($level, $message): bool
```

**Examples**

```php
// debug message
Logger::log('debug', 'Debug message');
// error message
Logger::log('error', 'Error message');
// info message
Logger::log('info', 'Informative message');
// warning message
Logger::log('warning', 'Waring message');
```

##### <a name="logger_write"></a> Method: `write()`

```php
/**
 * Write a log message line.
 *
 * @param string $level Error log level.
 * @param string $message Error log message.
 *
 * @return bool True if line was successfully wrote.
 */
public static function write($level, $message): void
```

**Examples**

```php
// debug message
$logger->write('debug', 'Debug message');
// error message
$logger->write('error', 'Error message');
// info message
$logger->write('info', 'Informative message');
// warning message
$logger->write('warning', 'Waring message');
```
## License
This project is under the MIT license. For more information see See [LICENSE](LICENSE).
