<?php
/**
 * Created by PhpStorm.
 * User: PVer
 * Date: 2017/8/11
 * Time: 15:20
 */

namespace wx;

class WxException extends \Exception
{

    public function __construct($type, $message, $file, $line)
    {
        $this->$type = $type;
        $this->message = $message;
        $this->file = $file;
        $this->line = $line;
    }

    // 用户定义的错误处理函数
    public static function myErrorHandler($errno, $errstr, $errfile, $errline)
    {
        /*   switch ($errno) {
        case E_USER_ERROR:
        echo "<b>My ERROR</b> [$errno] $errstr<br />\n";
        echo "  Fatal error on line $errline in file $errfile";
        echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
        echo "Aborting...<br />\n";
        exit(1);
        break;
        case E_USER_WARNING:
        echo "<b>My WARNING</b> [$errno] $errstr<br />\n";
        break;
        case E_USER_NOTICE:
        echo "<b>My NOTICE</b> [$errno] $errstr<br />\n";
        break;
        default:
        echo "Unknown error type: [$errno] $errstr<br />\n";
        break;
        }*/
        $exception = new WxException($errno, $errstr, $errfile, $errline);
        throw $exception;
    }

    //用户定义的异常处理函数
    public static function myExceptionHandler($exception)
    {
        echo "<b>cause:</b> " . $exception->getMessage() . ',' . '<b>file:</b>'
        . $exception->getFile() . ',' . '<b>line:</b>' . $exception->getLine();
    }

    /**
     * 用户定义程序停止后的处理函数
     */
    public static function myShutdownHandler()
    {
        if (!is_null($error = error_get_last())) {
            $exception = new WxException($error['type'], $error['message'], $error['file'], $error['line']);
            self::myExceptionHandler($exception);
        }
    }

}
