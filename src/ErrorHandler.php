<?php

declare(strict_types=1);

namespace Kobens\Core;

/**
 * An error handler that converts runtime errors into exceptions
 */
final class ErrorHandler
{
    /**
     * Error messages
     *
     * @var array
     */
    protected $errorPhrases = [
        E_ERROR => 'Error',
        E_WARNING => 'Warning',
        E_PARSE => 'Parse Error',
        E_NOTICE => 'Notice',
        E_CORE_ERROR => 'Core Error',
        E_CORE_WARNING => 'Core Warning',
        E_COMPILE_ERROR => 'Compile Error',
        E_COMPILE_WARNING => 'Compile Warning',
        E_USER_ERROR => 'User Error',
        E_USER_WARNING => 'User Warning',
        E_USER_NOTICE => 'User Notice',
        E_STRICT => 'Strict Notice',
        E_RECOVERABLE_ERROR => 'Recoverable Error',
        E_DEPRECATED => 'Deprecated Functionality',
        E_USER_DEPRECATED => 'User Deprecated Functionality',
    ];

    /**
     * Custom error handler
     *
     * @param int $errorNo
     * @param string $errorStr
     * @param string $errorFile
     * @param int $errorLine
     * @return bool
     * @throws \Exception
     */
    public function handler(int $errorNo, string $errorStr, string $errorFile, int $errorLine): bool
    {
        if (strpos($errorStr, 'DateTimeZone::__construct') !== false) {
            // there's no way to distinguish between caught system exceptions and warnings
            return false;
        }
        throw new \Exception(sprintf(
            "%s: %s in %s on line %s",
            $this->errorPhrases[$errorNo] ?? "Unknown error ({$errorNo})",
            $errorStr,
            $errorFile,
            $errorLine
        ));
    }
}
