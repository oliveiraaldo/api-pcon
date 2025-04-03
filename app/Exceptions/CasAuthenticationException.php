<?php

namespace App\Exceptions;

use Exception;

class CasAuthenticationException extends Exception
{
    protected $message = 'Falha na autenticação CAS';
    protected $code = 401;
}
