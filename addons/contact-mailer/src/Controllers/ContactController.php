<?php
namespace Tuxxin\TiCore\Addons\ContactMailer\Controllers;

use Tuxxin\TiCore\Addons\ContactMailer\Mailer;
use TiCore\Core\Http\Request;
use TiCore\Core\Http\Response;
use TiCore\Core\Security;
use TiCore\Core\Logger;

final class ContactController
{
    /** GET /contact — render the form. */
    public function show(): Response
    {
        return Response::view('contact-mailer::contact', [
            'title'  => 'Contact us',
            'error'  => null,
            'old'    => ['name' => '', 'email' => '', 'message' => ''],
        ]);
    }

    /** POST /contact/submit — honeypot + validate + send. (csrf middleware also applied) */
    public function submit(Request $req): Response
    {
        // Defense in depth: the route already runs the 'csrf' middleware, but
        // re-check here so the controller is safe regardless of how it's wired.
        if (!Security::csrfValid((string) $req->input('csrf_token'))) {
            return Response::make('Invalid CSRF token', 419);
        }

        // Honeypot — bots fill the hidden "website" field. Pretend success.
        if (trim((string) $req->input('website', '')) !== '') {
            Logger::info('contact-mailer: honeypot tripped from ' . $req->ip());
            return Response::view('contact-mailer::thanks', ['title' => 'Thank you']);
        }

        $name    = trim((string) $req->input('name', ''));
        $email   = trim((string) $req->input('email', ''));
        $message = trim((string) $req->input('message', ''));

        $error = null;
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } elseif ($message === '') {
            $error = 'Please enter a message.';
        }

        if ($error !== null) {
            return Response::view('contact-mailer::contact', [
                'title' => 'Contact us',
                'error' => $error,
                'old'   => ['name' => $name, 'email' => $email, 'message' => $message],
            ], 422);
        }

        $result = (new Mailer())->sendContact($name, $email, $message);

        if (!$result['configured']) {
            // Unconfigured: log a warning but STILL thank the visitor (don't error).
            Logger::warning('contact-mailer: MAILERSEND_API_KEY not set — message from '
                . $email . ' was not delivered.');
        } elseif (!$result['ok']) {
            // Configured but the API call failed — surface a soft error.
            return Response::view('contact-mailer::contact', [
                'title' => 'Contact us',
                'error' => 'Sorry — sending failed. Please try again shortly.',
                'old'   => ['name' => $name, 'email' => $email, 'message' => $message],
            ], 502);
        }

        return Response::view('contact-mailer::thanks', ['title' => 'Thank you']);
    }

    /** GET /contact/thanks — standalone thanks page (e.g. after a redirect). */
    public function thanks(): Response
    {
        return Response::view('contact-mailer::thanks', ['title' => 'Thank you']);
    }
}
