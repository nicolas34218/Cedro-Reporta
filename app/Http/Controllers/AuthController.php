<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\Citizen;
use App\Models\Admin;
use App\Models\Secretary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Controlador de autenticação para múltiplos tipos de usuários.
 * Gerencia registro, login e logout de cidadãos, admins e secretários.
 */
class AuthController extends Controller
{
    /**
     * Exibe o formulário de registro (apenas para cidadãos).
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Registra um novo cidadão no sistema.
     */
    public function register(RegisterRequest $request)
    {
        try {
            $citizen = Citizen::create([
                'name' => $request->validated('name'),
                'email' => $request->validated('email'),
                'password' => $request->validated('password'),
                'is_active' => true,
            ]);

            Auth::guard('citizen')->login($citizen);
            
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
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Exibe o formulário de recuperação de senha (agora numa única etapa).
     */
    public function showPasswordRecoveryForm()
    {
        return view('auth.password-recovery');
    }

    /**
     * Altera a senha diretamente usando apenas o e-mail informado no formulário.
     * (Substitui as antigas etapas complexas de validação por sessão).
     */
    public function processDirectPasswordRecovery(Request $request)
    {
        // 1. Validação dos dados que vêm do formulário
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Insira um e-mail válido.',
            'password.required' => 'A nova senha é obrigatória.',
            'password.min' => 'A senha deve ter no mínimo 6 caracteres.',
            'password.confirmed' => 'As senhas não coincidem.',
        ]);

        // 2. Busca a qual tipo de utilizador este e-mail pertence
        $recoveryTarget = $this->findRecoverableUserByEmail($validated['email']);

        // 3. Se não encontrar, retorna erro
        if (!$recoveryTarget) {
            return back()
                ->withInput()
                ->with('error', 'Nenhuma conta foi encontrada com esse e-mail.');
        }

        // 4. Salva a nova senha
        $user = $recoveryTarget['user'];
        $user->password = $validated['password'];
        $user->save();

        // 5. Redireciona para o login com sucesso
        return redirect()
            ->route('login')
            ->with('success', 'Senha redefinida com sucesso. Agora você já pode fazer login com a sua nova senha.');
    }

    /**
     * Autentica o usuário no sistema.
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $email = $credentials['email'];
        $password = $credentials['password'];
        $remember = $request->boolean('remember');

        // Tenta autenticar como admin
        $admin = Admin::where('email', $email)->first();
        if ($admin && Hash::check($password, $admin->password)) {
            Auth::guard('admin')->login($admin, $remember);
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard')->with('success', 'Login realizado com sucesso!');
        }

        // Tenta autenticar como secretário
        $secretary = Secretary::where('email', $email)->first();
        if ($secretary && Hash::check($password, $secretary->password)) {
            Auth::guard('secretary')->login($secretary, $remember);
            $request->session()->regenerate();
            return redirect()->route('secretary.dashboard')->with('success', 'Login realizado com sucesso!');
        }

        // Tenta autenticar como cidadão
        $citizen = Citizen::where('email', $email)->first();
        if ($citizen && Hash::check($password, $citizen->password)) {
            Auth::guard('citizen')->login($citizen, $remember);
            $request->session()->regenerate();
            return redirect()->route('citizen.home')->with('success', 'Login realizado com sucesso!');
        }

        throw ValidationException::withMessages([
            'email' => 'Credenciais inválidas, tente novamente',
        ]);
    }

    /**
     * Exibe o formulário de redefinição de senha (quando logado).
     */
    public function showResetPasswordForm()
    {
        return view('auth.reset-password');
    }

    /**
     * Redefine a senha do usuário (quando logado).
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $validated = $request->validated();

        $user = Admin::where('email', $validated['email'])->first()
            ?? Secretary::where('email', $validated['email'])->first()
            ?? Citizen::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'E-mail ou senha atual incorretos.',
            ]);
        }

        $user->password = $validated['password'];
        $user->save();

        return redirect()
            ->route('login')
            ->with('success', 'Senha alterada com sucesso! Faça login com sua nova senha.');
    }

    /**
     * Localiza uma conta de admin, secretária ou cidadão pelo e-mail.
     *
     * @param string $email
     * @return array{user:\App\Models\Admin|\App\Models\Secretary|\App\Models\Citizen, guard:string}|null
     */
    private function findRecoverableUserByEmail(string $email): ?array
    {
        if ($admin = Admin::where('email', $email)->first()) {
            return ['user' => $admin, 'guard' => 'admin'];
        }

        if ($secretary = Secretary::where('email', $email)->first()) {
            return ['user' => $secretary, 'guard' => 'secretary'];
        }

        if ($citizen = Citizen::where('email', $email)->first()) {
            return ['user' => $citizen, 'guard' => 'citizen'];
        }

        return null;
    }

    /**
     * Realiza logout do usuário.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'Você foi desconectado com sucesso!');
    }
}