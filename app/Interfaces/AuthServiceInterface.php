<?php

namespace App\Interfaces;

interface AuthServiceInterface
{
    public function getServiceUrl(): string;
    public function getCasLoginUrl(string $serviceUrl): string;
    public function handleCasAuthentication(string $ticket): string;
    public function validateCasTicket(string $ticket): string;
    public function findOrCreateUser(string $username);
    public function generateToken($user): string;

    public function authenticate(array $credentials);
    public function respondWithToken(string $token);
}
