<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Define o redirecionamento padrão caso o usuário não esteja autenticado.
     */
    protected function redirectTo($request): ?string
    {
        return null; // Evita redirecionamento em APIs e retorna 401
    }
}
