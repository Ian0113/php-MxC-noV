<?php
namespace Core\Exceptions;

// use Core\Exceptions\CoreException;

class MiddlewareException extends \Exception
{
    function __toString()
    {
        return get_class($this).': [in file \''.$this->getFile() .'\' on line \''.$this->getLine().'\'] '. $this->getMessage();
    }
}