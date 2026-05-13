<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * Controlador de autenticação.
 * Gerencia registro, login e logout de usuários.
 */
class AuthController extends Controller
{
    /**
     * Exibe o formulário de registro.
     *
     * @return \Illuminate\View\View
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Registra um novo usuário no sistema.
     *
     * @param RegisterRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(RegisterRequest $request)
    {
        try {
            // Cria novo usuário com valores validados
            $user = User::create([
                'name' => $request->validated('name'),
                'email' => $request->validated('email'),
                'password' => $request->validated('password'),
                'user_type' => 'Cidadão', // Novo usuário é cidadão por padrão
                'is_active' => true,
            ]);

            // Realiza login automático após o registro
            Auth::login($user);

            return redirect()
                ->route('citizen.home')
                ->with('success', 'Cadastro realizado com sucesso! Bem-vindo ao Cedro Reporta.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ocorreu um erro ao registrar o usuário. Tente novamente.');
        }
    }

    /**
     * Exibe o formulário de login.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Autentica o usuário no sistema.
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(LoginRequest $request)
    {
        // Extrai credenciais validadas
        $credentials = $request->validated();

        // Tenta autenticar o usuário
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Credenciais inválidas, tente novamente',
            ]);
        }

        // Regenera a sessão para segurança
        $request->session()->regenerate();

        /** @var User $user */
        $user = Auth::user();

        // Redireciona conforme o tipo de usuário
        if ($user->canAccessAdminPanel()) {
            return redirect()
                ->route('admin.dashboard')
                ->with('success', 'Login realizado com sucesso!');
        }

        return redirect()
            ->route('citizen.home')
            ->with('success', 'Login realizado com sucesso!');
    }

    /**
     * Realiza logout do usuário.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        // Realiza logout
        Auth::logout();

        // Invalida a sessão
        $request->session()->invalidate();

        // Regenera o token CSRF para segurança
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'Você foi desconectado com sucesso!');
    }
}
