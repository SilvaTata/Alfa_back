<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cargo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function users()
    {
        return response()->json(User::with(['contato', 'cargo'])->get(), 200);
    }

    public function register(Request $request)
    {
        $request->validate([
            'cpf' => 'required|string|size:11|unique:users,cpf',
            'password' => 'required|string|min:6',
            'nome' => 'required|string|max:255',
            'status' => 'required|string',
            'email' => 'required|string|email|max:255|unique:contatos,email',
            'telefone' => 'required|string|max:20',
            'cargo' => 'required|string|in:adm,user',
        ]);

        $user = User::create([
            'cpf' => $request->cpf,
            'password' => Hash::make($request->password), // Alterado para "password"
            'nome' => $request->nome,
            'status' => $request->status,
        ]);

        // Criando o contato vinculado ao usuário
        $user->contato()->create([
            'email' => $request->email,
            'telefone' => $request->telefone,
        ]);

        // Associando cargo
        $cargo = Cargo::where('nome', $request->cargo)->firstOrFail();
        $user->cargo()->associate($cargo);
        $user->save();

        // Gerando token Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Usuário registrado com sucesso!',
            'token' => $token
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'cpf' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('cpf', $request->cpf)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'cpf' => ['As credenciais estão incorretas.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login realizado com sucesso!',
            'token' => $token
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logout realizado com sucesso'], 200);
    }
}
