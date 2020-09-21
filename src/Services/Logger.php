<?php

namespace ialopezg\Services;

use ialopezg\Exceptions\MissingArgumentException;

/**
 * Log Service Class.
 *
 * @package ialopezg\Services
 */
class Logger {
    /** @var bool whether or not the logger can write to the log files. */
    protected $enabled;
    /** @var string log date format. */
    protected $log_date_format;
    /** @var string log path */
    protected $log_path;
    /** @var string log file extension. */
    protected $log_file_extension;
    /** @var int directories and files permissions. */
    protected $log_file_permissions;
    /** @var bool whether or not if message will indented. */
    protected $log_indented;
    /** @var array logging levels.  */
    protected $log_levels = [
        'ERROR' => 1,
        'DEBUG' => 2,
        'INFO' => 3,
        'WARNING' => 4,
        'ALL' => 5
    ];
    /** @var int log threshold. */
    protected $log_threshold;
    /** @var array log thresholds */
    protected $log_thresholds;

    /**
     * Logger constructor.
     *
     * @param array $params Default values.
     */
    public function __construct(array $params = []) {
        $this->initialize($params);
    }

    /**
     * Initialize class with default values.
     *
     * @param array $params Default values.
     */
    protected function initialize(array $params) {
        // set whether or not logger can write to the log files
        $this->enabled = isset($params['enabled']) ? $params['enabled'] : true;

        // set log directory and file permissions
        $this->log_file_permissions = isset($params['log_file_permissions']) ? $params['log_file_permissions'] : 0644;

        // set log path where logs will be recorded
        if (!isset($params['log_path'])) {
            $this->enabled = false;
        }
        // set log path
        $this->log_path = trim($params['log_path'], '/\\') . DIRECTORY_SEPARATOR;
        // if path does not exists
        if (!is_dir($this->log_path) && is_writable($this->log_path)) {
            mkdir($this->log_path, 0755, true);
        }

        // checks if logger can write to the log files
        if (!is_dir($this->log_path) && is_writable($this->log_path)) {
            $this->enabled = false;
        }

        // set log thresholds
        $this->log_thresholds = [];
        if (isset($params['log_threshold'])) {
            if (is_numeric($params['log_threshold']) && isset($this->log_levels[$params['log_threshold']])) {
                $this->log_threshold = (int)$params['log_threshold'];
            } elseif (is_array($params['log_threshold'])) {
                $this->log_threshold = 0;
                $this->log_thresholds = array_flip($params['log_threshold']);
            } elseif (is_string($params['log_threshold'])) {
                $this->log_threshold = $this->log_levels[strtoupper($params['log_threshold'])];
            } else {
                $this->enabled = false;
            }
        } else {
            $this->log_threshold = 1;
        }

        // set log directory and file permissions
        $this->log_file_extension = isset($params['log_file_extension']) ? $params['log_file_extension'] : 'log';

        // Set log format date
        $this->log_date_format = isset($params['log_date_format']) && !empty($params['log_date_format']) ? $params['log_date_format'] : 'Y-m-d H:i:s';

        // Set log format date
        $this->log_indented = isset($params['log_indented']) && !empty($params['log_indented']) ? $params['log_indented'] : true;
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
        $spaces = strlen(max(array_keys($this->log_levels)));

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
        // checks if this logger will be abe to write to the log files
        if (!$this->enabled) {
            return false;
        }

        $level = strtoupper($level);
        // checks if level is recognized by this instance
        if ((!isset($this->log_levels[$level]) || ($this->log_levels[$level] > $this->log_threshold))
            && !isset($this->log_thresholds[$this->log_levels[$level]])) {
            return false;
        }

        // check if messages will be indented
        if ($this->log_indented) {
            $level = $this->indentMessage($level);
        }

        // create file if not exists
        $new_file = false;
        $filepath = $this->log_path . 'log-' . date('Y-m-d') . ".{$this->log_file_extension}";
        if (!file_exists($filepath)) {
            $new_file = true;
        }

        // open file for writing mode
        if (!$fp = @fopen($filepath, 'ab')) {
            return false;
        }
        flock($fp, LOCK_EX);

        // format message
        $message = $this->formatMessage($level, date($this->log_date_format), $message);

        // write the message and close the handler
        for ($written = 0; $written < strlen($message); $written += $result) {
            if (($result = fwrite($fp, substr($message, $written))) === false) {
                break;
            }
        }
        flock($fp, LOCK_UN);
        fclose($fp);

        // apply permissions
        if (isset($new_file) && $new_file === TRUE) {
            chmod($filepath, $this->log_file_permissions);
        }

        return is_int($result);
    }

    /**
     * Log a message into the default log system. If the log system does not exists, will create a new one.
     *
     * @param int $level log message level. Accepts: `debug`, `error`, `info` and `warning` messages.
     * @param string $message log message.
     *
     * @return bool <code>true</code> if line was successfully wrote, <code>false</code> otherwise.
     */
    public static function log($level, $message) {
        static $logger;
        if (!($logger instanceof Logger)) {
            $logger = new Logger();
        }

        return $logger->write($level, $message);
    }
}
