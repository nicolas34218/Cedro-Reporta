<?php

use App\Models\Admin;
use App\Models\Citizen;
use App\Models\Secretary;
use Illuminate\Support\Facades\Hash;

describe('Password Reset', function () {
    beforeEach(function () {
        $this->citizen = Citizen::factory()->create([
            'email' => 'cidadao@example.com',
            'password' => 'senha-antiga',
        ]);

        $this->secretary = Secretary::create([
            'name' => 'Secretaria de Teste',
            'email' => 'secretaria@example.com',
            'password' => bcrypt('senha-antiga'),
            'is_active' => true,
        ]);

        $this->admin = Admin::create([
            'name' => 'Administrador',
            'email' => 'admin@example.com',
            'password' => bcrypt('senha-antiga'),
            'is_active' => true,
        ]);
    });

    it('shows the recovery password form', function () {
        $response = $this->get(route('password.forgot'));

        $response->assertOk();
        $response->assertSee('Esqueci minha senha');
        $response->assertSee('E-mail cadastrado');
    });

    it('rejects recovery when the e-mail is not registered', function () {
        $response = $this->post(route('password.recovery.verify'), [
            'email' => 'inexistente@example.com',
        ]);

        $response->assertSessionHasErrors('email');
    });

    it('starts recovery when the e-mail exists', function () {
        $response = $this->post(route('password.recovery.verify'), [
            'email' => 'cidadao@example.com',
        ]);

        $response->assertRedirect(route('password.recovery.reset'));
        $response->assertSessionHas('password_recovery.email', 'cidadao@example.com');
    });

    it('shows the reset password step after email verification', function () {
        $this->withSession([
            'password_recovery.email' => 'cidadao@example.com',
            'password_recovery.guard' => 'citizen',
        ]);

        $response = $this->get(route('password.recovery.reset'));

        $response->assertOk();
        $response->assertSee('Redefinição de senha');
        $response->assertSee('cidadao@example.com');
    });

    it('shows the success page for the recovery flow', function () {
        $this->withSession([
            'password_recovery.completed' => true,
        ]);

        $response = $this->get(route('password.recovery.success'));

        $response->assertOk();
        $response->assertSee('Senha redefinida com sucesso');
        $response->assertSee('Voltar para o Login');
    });

    it('redefines the password after recovery verification', function () {
        $this->withSession([
            'password_recovery.email' => 'cidadao@example.com',
            'password_recovery.guard' => 'citizen',
        ]);

        $response = $this->post(route('password.recovery.update'), [
            'password' => 'senha-nova-123',
            'password_confirmation' => 'senha-nova-123',
        ]);

        $response->assertRedirect(route('password.recovery.success'));
        $response->assertSessionHas('password_recovery.completed', true);

        expect(Hash::check('senha-nova-123', $this->citizen->refresh()->password))->toBeTrue();
    });

    it('shows the forgot password link on the login page', function () {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee(route('password.forgot'), false);
    });

    it('allows a citizen to change their password with email and current password', function () {
        $response = $this->post(route('password.update'), [
            'email' => 'cidadao@example.com',
            'current_password' => 'senha-antiga',
            'password' => 'senha-nova-123',
            'password_confirmation' => 'senha-nova-123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');

        expect(Hash::check('senha-nova-123', $this->citizen->refresh()->password))->toBeTrue();
        expect(Hash::check('senha-antiga', $this->citizen->password))->toBeFalse();
    });

    it('allows a secretary to change their password with email and current password', function () {
        $response = $this->post(route('password.update'), [
            'email' => 'secretaria@example.com',
            'current_password' => 'senha-antiga',
            'password' => 'senha-nova-123',
            'password_confirmation' => 'senha-nova-123',
        ]);

        $response->assertRedirect(route('login'));
        expect(Hash::check('senha-nova-123', $this->secretary->refresh()->password))->toBeTrue();
    });

    it('allows an admin to change their password with email and current password', function () {
        $response = $this->post(route('password.update'), [
            'email' => 'admin@example.com',
            'current_password' => 'senha-antiga',
            'password' => 'senha-nova-123',
            'password_confirmation' => 'senha-nova-123',
        ]);

        $response->assertRedirect(route('login'));
        expect(Hash::check('senha-nova-123', $this->admin->refresh()->password))->toBeTrue();
    });

    it('lets the citizen log in with the new password after the change', function () {
        $this->post(route('password.update'), [
            'email' => 'cidadao@example.com',
            'current_password' => 'senha-antiga',
            'password' => 'senha-nova-123',
            'password_confirmation' => 'senha-nova-123',
        ]);

        $response = $this->post(route('login.store'), [
            'email' => 'cidadao@example.com',
            'password' => 'senha-nova-123',
        ]);

        $response->assertRedirect(route('citizen.home'));
        $this->assertAuthenticatedAs($this->citizen, 'citizen');
    });

    it('rejects the change when the current password is wrong', function () {
        $response = $this->post(route('password.update'), [
            'email' => 'cidadao@example.com',
            'current_password' => 'senha-errada',
            'password' => 'senha-nova-123',
            'password_confirmation' => 'senha-nova-123',
        ]);

        $response->assertSessionHasErrors('current_password');
        expect(Hash::check('senha-antiga', $this->citizen->refresh()->password))->toBeTrue();
    });

    it('rejects the change when the email does not belong to any account', function () {
        $response = $this->post(route('password.update'), [
            'email' => 'inexistente@example.com',
            'current_password' => 'qualquer-coisa',
            'password' => 'senha-nova-123',
            'password_confirmation' => 'senha-nova-123',
        ]);

        $response->assertSessionHasErrors('current_password');
    });

    it('requires the new password to follow the same rules used at account creation', function () {
        $response = $this->post(route('password.update'), [
            'email' => 'cidadao@example.com',
            'current_password' => 'senha-antiga',
            'password' => '123',
            'password_confirmation' => '123',
        ]);

        $response->assertSessionHasErrors('password');
        expect(Hash::check('senha-antiga', $this->citizen->refresh()->password))->toBeTrue();
    });

    it('requires the new password confirmation to match', function () {
        $response = $this->post(route('password.update'), [
            'email' => 'cidadao@example.com',
            'current_password' => 'senha-antiga',
            'password' => 'senha-nova-123',
            'password_confirmation' => 'outra-coisa',
        ]);

        $response->assertSessionHasErrors('password');
        expect(Hash::check('senha-antiga', $this->citizen->refresh()->password))->toBeTrue();
    });

    it('requires all fields to be filled', function () {
        $response = $this->post(route('password.update'), []);

        $response->assertSessionHasErrors(['email', 'current_password', 'password']);
    });
});
