<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Usuário Teste',
            'email' => 'teste@example.com',
            'username' => 'testuser', // Se seu sistema usa username
            'password' => Hash::make('senha123'), // Senha fácil para testes
        ]);

        $this->command->info('Usuário de teste criado:');
        $this->command->warn('Email: teste@example.com');
        $this->command->warn('Senha: senha123');
    }
}
