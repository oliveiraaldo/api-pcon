<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function redirectToCas()
    {
        $serviceUrl = urlencode(config('app.url') . '/api/auth/cas-callback');
        return redirect("https://cas.correios.com.br/login?service={$serviceUrl}");
    }

    public function handleCasCallback(Request $request)
    {
        $ticket = $request->query('ticket');
        $serviceUrl = config('app.url') . '/api/auth/cas-callback';
    
        if (!$ticket) {
            return response()->json(['error' => 'Ticket ausente'], 400);
        }
    
        $casResponse = Http::get("https://cas.correios.com.br/serviceValidate", [
            'ticket' => $ticket,
            'service' => $serviceUrl,
        ]);
    
        // Extração simples do username
        if (preg_match('/<cas:user>(.*?)<\/cas:user>/', $casResponse->body(), $matches)) {
            $username = $matches[1];
    
            $user = User::firstOrCreate(['username' => $username]);
    
            Auth::login($user);
            $token = JWTAuth::fromUser($user);
    
            // return redirect("http://localhost/auth/callback?token={$token}");
            
            return redirect($token);
        }
    
        return response()->json(['error' => 'Falha na autenticação CAS'], 401);
    }
    
}
