<?php

namespace App\Http\Controllers;

use App\Mail\TestEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class EmailTestController extends Controller
{
    public function show(): View
    {
        $mailer = config('mail.default');
        $smtp = config('mail.mailers.smtp');
        $sendmail = config('mail.mailers.sendmail');

        return view('dashboard.email-test', [
            'mailer' => $mailer,
            'host' => $smtp['host'] ?? '—',
            'port' => $smtp['port'] ?? '—',
            'username' => $smtp['username'] ?? null,
            'passwordConfigured' => filled($smtp['password'] ?? null),
            'scheme' => $smtp['scheme'] ?? env('MAIL_SCHEME'),
            'encryptionLabel' => self::encryptionLabel($smtp['scheme'] ?? env('MAIL_SCHEME'), (int) ($smtp['port'] ?? 0)),
            'sendmailPath' => $sendmail['path'] ?? null,
            'configCached' => file_exists(base_path('bootstrap/cache/config.php')),
            'from' => config('mail.from'),
            'defaultTo' => auth()->user()->email,
        ]);
    }

    public function send(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'to' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        try {
            Mail::to($validated['to'])->send(
                new TestEmail($validated['message'], $validated['subject'])
            );
        } catch (Throwable $e) {
            report($e);

            $message = 'Could not send email: '.$e->getMessage();
            if (file_exists(base_path('bootstrap/cache/config.php'))) {
                $message .= ' — config is cached; run php artisan config:clear on the server after changing .env.';
            }

            return back()
                ->withInput()
                ->with('error', $message);
        }

        $hint = config('mail.default') === 'log'
            ? ' (written to the Laravel log — switch MAIL_MAILER to smtp on production)'
            : '';

        return back()->with('status', 'Test email sent to '.$validated['to'].$hint);
    }

    private static function encryptionLabel(?string $scheme, int $port): string
    {
        if ($scheme === 'smtps' || $port === 465) {
            return 'SSL/TLS (smtps)';
        }

        if ($scheme === null || $scheme === 'smtp') {
            return $port === 25 ? 'None (port 25)' : 'STARTTLS (smtp)';
        }

        return $scheme;
    }
}
