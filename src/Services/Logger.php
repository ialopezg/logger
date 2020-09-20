<?php

namespace ialopezg\Services;

use ialopezg\Exceptions\MissingArgumentException;

/**
 * Log Service Class.
 *
 * @package ialopezg\Services
 */
class Logger {
    /** @var string log date format. */
    protected $log_date_format;
    /** @var string log path */
    protected $log_path;
    /** @var int directories and files permissions. */
    protected $log_file_permissions;
    /** @var array logging levels.  */
    protected $levels = [
        'DEBUG',
        'ERROR',
        'INFO',
        'WARNING'
    ];

    public function __construct(array $params = []) {
        $this->initialize($params);
    }

    /**
     * Initialize class with default values.
     *
     * @param array $params Default values.
     */
    protected function initialize(array $params) {
        // set log directory and file permissions
        $this->log_file_permissions = isset($params['log_file_permissions']) ? $params['log_file_permissions'] : 0644;

        if (!isset($params['log_path'])) {
            throw new MissingArgumentException('log_path', __CLASS__, __FUNCTION__);
        }
        // set log path
        $this->log_path = trim("{$params['log_path']}/", '/') . DIRECTORY_SEPARATOR;
        // if path does not exists
        if (!is_dir($this->log_path)) {
            mkdir($this->log_path, 0755, true);
        }

        // Set log format date
        $this->log_date_format = isset($params['log_date_format']) && !empty($params['log_date_format']) ? $params['log_date_format'] : 'Y-m-d H:i:s';
    }

    /**
     * Format a log message line.
     *
     * @param string $level Error level.
     * @param string $date Date of logging.
     * @param string $message Log message line.
     *
     * @return string A message properly formatted.
     */
    protected function formatMessage($level, $date, $message) {
        $level = $this->indentMessage($level);

        return "[{$date}] - {$level} => {$message}" . PHP_EOL;
    }

    /**
     * Indent the message with spaces in relation to longest log level.
     *
     * @param string $level log level.
     *
     * @return string indented message.
     */
    protected function indentMessage($level) {
        // get the longest log level
        $spaces = max(array_map('strlen', $this->levels));

        if (strlen($level) < $spaces) {
            for ($i = strlen($level); $i < $spaces; $i++) {
                $level .= ' ';
            }
        }

        return $level;
    }

    /**
     * Write a log message line.
     *
     * @param string $level Error log level.
     * @param string $message Error log message.
     *
     * @return bool True if line was successfully wrote.
     */
    public function write($level, $message) {
        $level = strtoupper($level);

        $new_file = false;
        $filepath = $this->log_path . 'log-' . date('Y-m-d') . '.log';
        if (!file_exists($filepath)) {
            $new_file = true;
        }

        if (!$fp = @fopen($filepath, 'ab')) {
            return false;
        }
        flock($fp, LOCK_EX);

        // format message
        $message = $this->formatMessage($level, date($this->log_date_format), $message);

        // write the message
        for ($written = 0; $written < strlen($message); $written += $result) {
            if (($result = fwrite($fp, substr($message, $written))) === false) {
                break;
            }
        }
        flock($fp, LOCK_UN);
        fclose($fp);

        // apply permissions
        if (isset($newfile) && $newfile === TRUE) {
            chmod($filepath, $this->log_file_permissions);
        }

        return is_int($result);
    }
}
