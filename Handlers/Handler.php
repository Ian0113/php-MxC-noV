<?php

namespace Handlers;

use Core\Exceptions\MiddlewareException;
use Core\Exceptions\CoreException;
use Core\Base\Handler as BaseHandler;

class Handler extends BaseHandler
{
    public function run()
    {
        try {
            parent::run();
            $this->preExec[0]['is_completed'] = true;
            $this->setSuccessMsg();
        } catch (MiddlewareException $middlewareExp) {
            $this->setFailMsg((string) $middlewareExp, $middlewareExp->getCode());
        } catch (CoreException $coreExp) {
            $this->setFailMsg((string) $coreExp, $coreExp->getCode());
        } catch (\Throwable $th) {
            $this->setFailMsg($th, $th->getCode());
        }
    }
}
