<?php

namespace App\Services;

use App\Interfaces\AuthServiceInterface;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use App\Exceptions\CasAuthenticationException;

class AuthService implements AuthServiceInterface
{
    public function getServiceUrl(): string
    {
        return config('app.url') . '/api/auth/cas-callback';
    }

    public function getCasLoginUrl(string $serviceUrl): string
    {
        return config('services.cas.base_url') . '/login?service=' . urlencode($serviceUrl);
    }

    public function handleCasAuthentication(string $ticket): string
    {
        $username = $this->validateCasTicket($ticket);
        $user = $this->findOrCreateUser($username);
        return $this->generateToken($user);
    }

    public function validateCasTicket(string $ticket): string
    {
        $serviceUrl = $this->getServiceUrl();
        $response = Http::get(config('services.cas.base_url') . '/serviceValidate', [
            'ticket' => $ticket,
            'service' => $serviceUrl,
        ]);

        if (!preg_match('/<cas:user>(.*?)<\/cas:user>/', $response->body(), $matches)) {
            throw new CasAuthenticationException('Falha na autenticação CAS', 401);
        }

        return $matches[1];
    }

    public function findOrCreateUser(string $username)
    {
        return User::firstOrCreate(['username' => $username], [
            'name' => $username,
            'email' => $username . '@' . config('services.cas.email_domain', 'example.com'),
            'password' => bcrypt(uniqid()) // Senha aleatória pois não será usada
        ]);
    }

    public function generateToken($user): string
    {
        return JWTAuth::fromUser($user);
    }

    public function authenticate(array $credentials)
    {
        if (!$token = Auth::attempt($credentials)) {
            throw new \Exception('Credenciais inválidas', 401);
        }

        return $token;
    }

    public function respondWithToken(string $token): array
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ];
    }
}
