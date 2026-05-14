<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\Citizen;
use App\Models\Secretary;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;

/**
 * UserProvider customizado que suporta múltiplos modelos Authenticatable.
 * Tenta localizar usuários em Admin, Secretary e Citizen, nessa ordem.
 */
class MultiModelUserProvider extends EloquentUserProvider
{
    private $models = [Admin::class, Secretary::class, Citizen::class];

    /**
     * Construtor
     */
    public function __construct(HasherContract $hasher)
    {
        // Não chama o construtor pai
        $this->hasher = $hasher;
    }

    /**
     * Recupera um usuário pelo ID, tentando todos os modelos possíveis.
     *
     * @param  mixed  $id
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($id)
    {
        foreach ($this->models as $modelClass) {
            $user = $modelClass::find($id);
            if ($user) {
                return $user;
            }
        }
        return null;
    }

    /**
     * Recupera um usuário por credenciais.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials) ||
           (count($credentials) === 1 && array_key_exists('password', $credentials))) {
            return null;
        }

        // Tenta todos os modelos
        foreach ($this->models as $modelClass) {
            $query = $modelClass::query();

            foreach ($credentials as $key => $value) {
                if ($key !== 'password') {
                    $query->where($key, $value);
                }
            }

            $user = $query->first();
            if ($user) {
                return $user;
            }
        }

        return null;
    }

    /**
     * Recupera um usuário pelo seu token "remember me".
     *
     * @param  string  $identifier
     * @param  string  $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        foreach ($this->models as $modelClass) {
            $user = $modelClass::where($modelClass::getRememberTokenName(), $token)->first();
            if ($user && $user->getRememberToken() === $token) {
                return $user;
            }
        }

        return null;
    }

    /**
     * Valida as credenciais do usuário.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        $plain = $credentials['password'];
        return $this->hasher->check($plain, $user->getAuthPassword());
    }
}
