<?php

namespace App\Core\Exceptions;

use Exception;
use Illuminate\Support\Str;
use Throwable;

/** @SuppressWarnings(PHPMD.NumberOfChildren) */
abstract class BaseException extends Exception
{
    /**
     * @var int
     */
    protected $code = 400;

    /**
     * @var string
     */
    protected $messageCode = null;

    final public function __construct($message = "", $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get prefix of code
     *
     * @return string
     */
    abstract protected static function getPrefix();

    /**
     * Get list of allowable methods
     *
     * @return array<string>
     */
    protected static function getAllowableMethods()
    {
        return [];
    }

    /**
     * Set the message code
     *
     * @param string $code
     * @return self
     */
    public function setMessageCode(string $code)
    {
        $this->messageCode = $code;

        return $this;
    }

    /**
     * Get the message code
     *
     * @return string
     */
    public function getMessageCode()
    {
        return $this->messageCode;
    }

    public static function __callStatic($name, $arguments)
    {
        $code = static::getPrefix() . '.' . Str::snake($name);

        return (new static(__('messages.' . $code, $arguments[1] ?? []), $arguments[0] ?? 0))->setMessageCode($code);
    }
}
