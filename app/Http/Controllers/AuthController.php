<?php

namespace App\Http\Controllers;

use App\Http\Requests\CasCallbackRequest;
use App\Interfaces\AuthServiceInterface;
use App\Http\Resources\TokenResource;
use App\Requests\LoginRequest;

class AuthController extends Controller
{
    private $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
        $this->middleware('auth:api', ['except' => ['login', 'redirectToCas', 'handleCasCallback']]);

    }

    public function redirectToCas()
    {
        $serviceUrl = $this->authService->getServiceUrl();
        $casLoginUrl = $this->authService->getCasLoginUrl($serviceUrl);

        return redirect($casLoginUrl);
    }

    public function handleCasCallback(CasCallbackRequest $request)
    {
        try {
            $token = $this->authService->handleCasAuthentication($request->ticket);
            return new TokenResource(['token' => $token]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], $e->getCode());
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $token = $this->authService->authenticate($request->only('email', 'password'));
            return response()->json($this->authService->respondWithToken($token));
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ]);
        }
    }
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Logout realizado com sucesso']);
    }

    public function refresh(): \Illuminate\Http\JsonResponse
    {
        return response()->json($this->authService->respondWithToken(auth()->refresh()));
    }

    public function me(): \Illuminate\Http\JsonResponse
    {
        return response()->json(auth()->user());
    }
}
