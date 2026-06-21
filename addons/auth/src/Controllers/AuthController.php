<?php
namespace Tuxxin\TiCore\Addons\Auth\Controllers;

use Tuxxin\TiCore\Addons\Auth\Auth;
use TiCore\Core\Http\Request;
use TiCore\Core\Http\Response;
use TiCore\Core\Security;

final class AuthController
{
    private function auth(): Auth { return new Auth(); }

    public function showLogin(): Response
    {
        return Response::view('auth::login', ['title' => 'Sign in', 'error' => null]);
    }

    public function login(Request $req): Response
    {
        if (!Security::csrfValid((string) $req->input('csrf_token'))) {
            return Response::make('Invalid CSRF token', 419);
        }
        $a = $this->auth();
        $user = $a->findByEmail((string) $req->input('email', ''));
        if (!$user || !$a->verifyPassword($user, (string) $req->input('password', ''))) {
            return Response::view('auth::login', ['title' => 'Sign in', 'error' => 'Invalid email or password.'], 401);
        }
        $a->login($user);
        return Response::redirect('/account');
    }

    public function showRegister(): Response
    {
        return Response::view('auth::register', ['title' => 'Create account', 'error' => null]);
    }

    public function register(Request $req): Response
    {
        if (!Security::csrfValid((string) $req->input('csrf_token'))) {
            return Response::make('Invalid CSRF token', 419);
        }
        $email = trim((string) $req->input('email', ''));
        $pw    = (string) $req->input('password', '');
        $name  = trim((string) $req->input('name', ''));
        $min   = (int) (config('auth')['password_min'] ?? 8);

        $err = null;
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))      $err = 'Enter a valid email address.';
        elseif (strlen($pw) < $min)                          $err = "Password must be at least {$min} characters.";
        if ($err) return Response::view('auth::register', ['title' => 'Create account', 'error' => $err], 422);

        $a = $this->auth();
        if ($a->findByEmail($email)) {
            return Response::view('auth::register', ['title' => 'Create account', 'error' => 'That email is already registered.'], 409);
        }
        $user = $a->create($email, $pw, $name !== '' ? $name : null);
        $a->login($user);
        return Response::redirect('/account');
    }

    public function logout(Request $req): Response
    {
        if (!Security::csrfValid((string) $req->input('csrf_token'))) {
            return Response::make('Invalid CSRF token', 419);
        }
        $this->auth()->logout();
        return Response::redirect('/login');
    }

    public function account(): Response
    {
        $user = $this->auth()->current();
        return Response::view('auth::account', ['title' => 'Account', 'user' => $user]);
    }
}
