<?php

declare(strict_types=1);

namespace Avc\Controllers\Admin;

use Avc\Core\Request;
use Avc\Core\Response;
use Avc\Services\Auth\AdminAuthService;
use Avc\Support\PageRenderer;

final class AuthController
{
    public function __construct(
        private array $config,
        private Request $request,
        private Response $response
    ) {
    }

    public function loginForm(): never
    {
        $auth = new AdminAuthService($this->config, $this->request);

        if ($auth->check()) {
            $this->response->redirect('/admin');
        }

        $error = (string) $this->request->input('error', '');
        $redirect = $this->sanitizeRedirect((string) $this->request->input('redirect', '/admin'));
        $notice = $error === 'invalid'
            ? '<div class="notice" style="background:#fff0ee;color:#8d3e32;border-color:#f1c4bd">Neispravan email ili lozinka.</div>'
            : '';

        $body = '<div class="shell"><section class="hero" style="min-height:100vh;display:grid;align-items:center">'
            . '<div class="hero-panel" style="max-width:620px;margin:0 auto">'
            . '<span class="hero-kicker">Admin login</span>'
            . '<div class="content-prose"><h1>Prijava u novu AVC platformu</h1></div>'
            . '<p class="muted">Ovo je lokalna testna verzija admin sučelja za referral postavke, leadove i routing.</p>'
            . $notice
            . '<form method="post" action="/admin/login">'
            . '<input type="hidden" name="redirect" value="' . htmlspecialchars($redirect, ENT_QUOTES, 'UTF-8') . '">'
            . '<label>Email<input type="email" name="email" required placeholder="admin@aloevera-centar.local"></label>'
            . '<label>Lozinka<input type="password" name="password" required placeholder="Unesi lozinku"></label>'
            . '<button class="button button-primary" type="submit">Prijavi se</button>'
            . '</form>'
            . '<p class="muted" style="margin-top:16px"><a href="/">Povratak na lokalni site</a></p>'
            . '</div></section></div>';

        $this->response->html(PageRenderer::render('AVC Admin Login', $body, [
            'lang' => 'hr',
            'robots' => 'noindex,nofollow',
        ]));
    }

    public function login(): never
    {
        $email = trim((string) $this->request->input('email', ''));
        $password = (string) $this->request->input('password', '');
        $redirect = $this->sanitizeRedirect((string) $this->request->input('redirect', '/admin'));

        $auth = new AdminAuthService($this->config, $this->request);

        if ($auth->attempt($email, $password)) {
            $this->response->redirect($redirect);
        }

        $this->response->redirect('/admin/login?error=invalid&redirect=' . rawurlencode($redirect));
    }

    public function logout(): never
    {
        (new AdminAuthService($this->config, $this->request))->logout();

        $this->response->redirect('/admin/login');
    }

    private function sanitizeRedirect(string $redirect): string
    {
        $redirect = trim($redirect);

        if ($redirect === '' || !str_starts_with($redirect, '/admin')) {
            return '/admin';
        }

        return $redirect;
    }
}
