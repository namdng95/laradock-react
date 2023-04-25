<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Str;
use Throwable;
use Illuminate\Http\Response;

class BusinessException extends Exception
{
    /**
     * @var string
     */
    protected $messageCode = null;

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

    public static function convertErrorCode($errorCode)
    {
        $errorCode = strtolower(Str::snake($errorCode));
        if (substr($errorCode, 0, 7) == 'errors.') {
            $errorCode = substr($errorCode, 7);
        } elseif (substr($errorCode, 0, 9) == 'messages.') {
            $errorCode = substr($errorCode, 9);
        }
        return $errorCode;
    }

    public function __construct($errorCode = 'server_error', $params = [], $httpCode = Response::HTTP_BAD_REQUEST, Throwable $previous = null)
    {
        $errorCode = self::convertErrorCode($errorCode);
        $this->setMessageCode('errors.'.$errorCode);
        $message = __('messages.'.$errorCode, $params);

        if ($errorCode == 'server_error') {
            $httpCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        }
        if ($errorCode == 'jinjer_proxy_error') {
            $message = $params['message'] ?? $message;
        }
        parent::__construct($message, $httpCode, $previous);
    }

    public static function __callStatic($errorCode, $arguments)
    {
        $errorCode = self::convertErrorCode($errorCode);
        $params = $arguments[0] ?? [];
        if (!is_array($params)) {
            $params = [];
        }
        if ($errorCode == 'jinjer_proxy_error') {
            $params['message'] = (string) ($arguments[0] ?? __('messages.jinjer_proxy_error'));
        }

        $httpCode = $arguments[1] ?? Response::HTTP_BAD_REQUEST;

        return (new static($errorCode, $params, $httpCode));
    }
}
