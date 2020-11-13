<?php
namespace Core\Exceptions;

class CoreException extends \Exception
{
    function __toString()
    {
        return get_class($this).': [in file \''.$this->getFile() .'\' on line \''.$this->getLine().'\'] '. $this->getMessage();
    }
}
