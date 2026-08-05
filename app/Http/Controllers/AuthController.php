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
     *
     * @return \Illuminate\View\View
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Registra um novo cidadão no sistema.
     *
     * @param RegisterRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(RegisterRequest $request)
    {
        try {
            // Cria novo cidadão com valores validados
            $citizen = Citizen::create([
                'name' => $request->validated('name'),
                'email' => $request->validated('email'),
                'password' => $request->validated('password'),
                'is_active' => true,
            ]);

            // Realiza login automático após o registro
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
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Exibe o formulário de recuperação de senha.
     *
     * @return \Illuminate\View\View
     */
    public function showPasswordRecoveryForm()
    {
        return view('auth.password-recovery');
    }

    /**
     * Verifica se o e-mail informado pertence a uma conta cadastrada.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyPasswordRecoveryEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $recoveryTarget = $this->findRecoverableUserByEmail($validated['email']);

        if (!$recoveryTarget) {
            throw ValidationException::withMessages([
                'email' => 'Nenhuma conta foi encontrada com esse e-mail.',
            ]);
        }

        $request->session()->put([
            'password_recovery.email' => $validated['email'],
            'password_recovery.guard' => $recoveryTarget['guard'],
        ]);

        return redirect()
            ->to('/recuperar-senha/redefinir')
            ->with('success', 'Conta localizada. Defina uma nova senha para continuar.');
    }

    /**
     * Exibe a tela de redefinição após a verificação do e-mail.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function showPasswordRecoveryResetForm(Request $request)
    {
        $recoveryEmail = $request->session()->get('password_recovery.email');

        if (!$recoveryEmail) {
            return redirect()
                ->to('/esqueci-senha')
                ->withErrors([
                    'email' => 'Informe seu e-mail para iniciar a recuperação de senha.',
                ]);
        }

        return view('auth.password-reset', [
            'recoveryEmail' => $recoveryEmail,
        ]);
    }

    /**
     * Atualiza a senha da conta localizada na etapa de recuperação.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateRecoveredPassword(Request $request)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',
        ]);

        $recoveryEmail = $request->session()->get('password_recovery.email');

        if (!$recoveryEmail) {
            return redirect()
                ->to('/esqueci-senha')
                ->withErrors([
                    'email' => 'A sessão de recuperação expirou. Informe seu e-mail novamente.',
                ]);
        }

        $recoveryTarget = $this->findRecoverableUserByEmail($recoveryEmail);

        if (!$recoveryTarget) {
            $request->session()->forget([
                'password_recovery.email',
                'password_recovery.guard',
                'password_recovery.completed',
            ]);

            return redirect()
                ->to('/esqueci-senha')
                ->withErrors([
                    'email' => 'Nenhuma conta foi encontrada com esse e-mail.',
                ]);
        }

        $recoveryTarget['user']->password = $validated['password'];
        $recoveryTarget['user']->save();

        $request->session()->put('password_recovery.completed', true);
        $request->session()->forget([
            'password_recovery.email',
            'password_recovery.guard',
        ]);

        return redirect()
            ->to('/recuperar-senha/sucesso')
            ->with('success', 'Senha redefinida com sucesso. Agora você já pode voltar ao login.');
    }

    /**
     * Exibe a tela final de sucesso da recuperação.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function showPasswordRecoverySuccess(Request $request)
    {
        $completed = $request->session()->pull('password_recovery.completed', false);

        if (!$completed) {
            return redirect()
                ->to('/esqueci-senha')
                ->withErrors([
                    'email' => 'Conclua a redefinição de senha para acessar esta tela.',
                ]);
        }

        return view('auth.password-success');
    }

    /**
     * Autentica o usuário no sistema.
     * Tenta autenticar como cidadão, admin ou secretário.
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\RedirectResponse
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
            return redirect()
                ->route('admin.dashboard')
                ->with('success', 'Login realizado com sucesso!');
        }

        // Tenta autenticar como secretário
        $secretary = Secretary::where('email', $email)->first();
        if ($secretary && Hash::check($password, $secretary->password)) {
            Auth::guard('secretary')->login($secretary, $remember);
            $request->session()->regenerate();
            return redirect()
                ->route('secretary.dashboard')
                ->with('success', 'Login realizado com sucesso!');
        }

        // Tenta autenticar como cidadão
        $citizen = Citizen::where('email', $email)->first();
        if ($citizen && Hash::check($password, $citizen->password)) {
            Auth::guard('citizen')->login($citizen, $remember);
            $request->session()->regenerate();
            return redirect()
                ->route('citizen.home')
                ->with('success', 'Login realizado com sucesso!');
        }

        // Nenhuma autenticação bem-sucedida
        throw ValidationException::withMessages([
            'email' => 'Credenciais inválidas, tente novamente',
        ]);
    }

    /**
     * Exibe o formulário de redefinição de senha.
     *
     * @return \Illuminate\View\View
     */
    public function showResetPasswordForm()
    {
        return view('auth.reset-password');
    }

    /**
     * Redefine a senha do usuário (admin, secretário ou cidadão), mediante
     * confirmação do e-mail e da senha atual. A nova senha segue as mesmas
     * regras de validação aplicadas na criação da conta.
     *
     * @param ResetPasswordRequest $request
     * @return \Illuminate\Http\RedirectResponse
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
        $admin = Admin::where('email', $email)->first();

        if ($admin) {
            return [
                'user' => $admin,
                'guard' => 'admin',
            ];
        }

        $secretary = Secretary::where('email', $email)->first();

        if ($secretary) {
            return [
                'user' => $secretary,
                'guard' => 'secretary',
            ];
        }

        $citizen = Citizen::where('email', $email)->first();

        if ($citizen) {
            return [
                'user' => $citizen,
                'guard' => 'citizen',
            ];
        }

        return null;
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

