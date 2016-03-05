<?php
namespace ACP3\Core;

use Monolog\Handler\AbstractHandler;
use Monolog\Logger as MonologLogger;
use Psr\Log\LogLevel;

/**
 * Monolog error handler
 *
 * A facility to enable logging of runtime errors, exceptions and fatal errors.
 *
 * Quick setup: <code>ErrorHandler::register($logger);</code>
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @autorh Tino Goratsch <mail@goratschwebdesign.de>
 */
class ErrorHandler
{
    /**
     * @var \Monolog\Logger
     */
    private $logger;
    /**
     * @var array
     */
    private $errorLevelMap = [];
    /**
     * @var array
     */
    private static $fatalErrors = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];

    /**
     * ErrorHandler constructor.
     *
     * @param \Monolog\Logger $logger
     */
    public function __construct(MonologLogger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Registers a new ErrorHandler for a given Logger
     *
     * By default it will handle errors, exceptions and fatal errors
     *
     * @param  \Monolog\Logger $logger
     *
     * @return ErrorHandler
     */
    public static function register(MonologLogger $logger)
    {
        $handler = new static($logger);
        $handler->registerErrorHandler();
        $handler->registerExceptionHandler();
        $handler->registerFatalHandler();

        return $handler;
    }

    public function registerExceptionHandler()
    {
        set_exception_handler([$this, 'handleException']);
    }

    public function registerErrorHandler()
    {
        set_error_handler([$this, 'handleError'], E_ALL);
        $this->errorLevelMap = $this->defaultErrorLevelMap();
    }

    public function registerFatalHandler()
    {
        register_shutdown_function([$this, 'handleFatalError']);
    }

    /**
     * @return array
     */
    protected function defaultErrorLevelMap()
    {
        return [
            E_ERROR => LogLevel::CRITICAL,
            E_WARNING => LogLevel::WARNING,
            E_PARSE => LogLevel::ALERT,
            E_NOTICE => LogLevel::NOTICE,
            E_CORE_ERROR => LogLevel::CRITICAL,
            E_CORE_WARNING => LogLevel::WARNING,
            E_COMPILE_ERROR => LogLevel::ALERT,
            E_COMPILE_WARNING => LogLevel::WARNING,
            E_USER_ERROR => LogLevel::ERROR,
            E_USER_WARNING => LogLevel::WARNING,
            E_USER_NOTICE => LogLevel::NOTICE,
            E_STRICT => LogLevel::NOTICE,
            E_RECOVERABLE_ERROR => LogLevel::ERROR,
            E_DEPRECATED => LogLevel::NOTICE,
            E_USER_DEPRECATED => LogLevel::NOTICE,
        ];
    }

    /**
     * @param \Exception $e
     */
    public function handleException(\Exception $e)
    {
        $this->logger->log(
            LogLevel::ERROR,
            sprintf(
                'Uncaught Exception %s: "%s" at %s line %s',
                get_class($e),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            ),
            ['exception' => $e]
        );

        exit(255);
    }

    /**
     * @param int    $code
     * @param string $message
     * @param string $file
     * @param int    $line
     *
     * @throws \ErrorException
     */
    public function handleError($code, $message, $file = '', $line = 0)
    {
        if (!(error_reporting() & $code)) {
            return;
        }

        // fatal error codes are ignored if a fatal error handler is present as well to avoid duplicate log entries
        if (!in_array($code, self::$fatalErrors, true)) {
            throw new \ErrorException($message, $code, 1, $file, $line);
        }
    }

    public function handleFatalError()
    {
        $lastError = error_get_last();
        if ($lastError !== null && in_array($lastError['type'], self::$fatalErrors, true)) {
            $this->logger->log(
                LogLevel::ALERT,
                'Fatal Error (' . self::errorCodeToString($lastError['type']) . '): ' . $lastError['message'],
                [
                    'code' => $lastError['type'],
                    'message' => $lastError['message'],
                    'file' => $lastError['file'],
                    'line' => $lastError['line']
                ]
            );

            $this->closeLoggerHandlers();
        }
    }

    /**
     * @param int $errorCode
     *
     * @return string
     */
    private static function errorCodeToString($errorCode)
    {
        switch ($errorCode) {
            case E_ERROR:
                return 'E_ERROR';
            case E_WARNING:
                return 'E_WARNING';
            case E_PARSE:
                return 'E_PARSE';
            case E_NOTICE:
                return 'E_NOTICE';
            case E_CORE_ERROR:
                return 'E_CORE_ERROR';
            case E_CORE_WARNING:
                return 'E_CORE_WARNING';
            case E_COMPILE_ERROR:
                return 'E_COMPILE_ERROR';
            case E_COMPILE_WARNING:
                return 'E_COMPILE_WARNING';
            case E_USER_ERROR:
                return 'E_USER_ERROR';
            case E_USER_WARNING:
                return 'E_USER_WARNING';
            case E_USER_NOTICE:
                return 'E_USER_NOTICE';
            case E_STRICT:
                return 'E_STRICT';
            case E_RECOVERABLE_ERROR:
                return 'E_RECOVERABLE_ERROR';
            case E_DEPRECATED:
                return 'E_DEPRECATED';
            case E_USER_DEPRECATED:
                return 'E_USER_DEPRECATED';
        }

        return 'Unknown PHP error';
    }

    private function closeLoggerHandlers()
    {
        foreach ($this->logger->getHandlers() as $handler) {
            if ($handler instanceof AbstractHandler) {
                $handler->close();
            }
        }
    }
}
