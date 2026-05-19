@extends('layouts.dashboard')

@section('title', 'Email test')

@section('content')
    <div class="panel">
        <h1>Email test</h1>
        <p class="lead">Send a test message through the configured mail driver.</p>

        <div class="meta-grid">
            <div class="meta-card">
                <strong>Mailer</strong>
                <code>{{ $mailer }}</code>
            </div>
            <div class="meta-card">
                <strong>From</strong>
                {{ $from['address'] }}
                @if ($from['name'])
                    <span class="text-muted">({{ $from['name'] }})</span>
                @endif
            </div>
            @if ($mailer === 'smtp')
                <div class="meta-card">
                    <strong>SMTP host</strong>
                    {{ $host }}:{{ $port }}
                    <span class="text-muted">· {{ $encryptionLabel }}</span>
                </div>
                <div class="meta-card">
                    <strong>SMTP user</strong>
                    {{ $username ?: 'Not set' }}
                    @if ($passwordConfigured)
                        · password set
                    @else
                        · <span class="text-warn">no password</span>
                    @endif
                </div>
            @endif
        </div>

        @if ($mailer === 'log')
            <div class="status-banner warn">
                <code>MAIL_MAILER=log</code> — messages are not delivered; they are written to
                <a href="{{ route('dashboard.logs.show', 'laravel.log') }}">laravel.log</a>.
                Use SMTP on production (see setup below).
            </div>
        @endif

        <form class="email-test-form" method="POST" action="{{ route('dashboard.email.send') }}">
            @csrf

            <div class="field">
                <label for="to">To</label>
                <input type="email" id="to" name="to" value="{{ old('to', $defaultTo) }}" required autocomplete="email">
                @error('to')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label for="subject">Subject</label>
                <input type="text" id="subject" name="subject" value="{{ old('subject', 'Goalgus API — test email') }}" required maxlength="200">
                @error('subject')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label for="message">Message</label>
                <textarea id="message" name="message" rows="5" required maxlength="5000">{{ old('message', "This is a test email from the Goalgus API dashboard.\n\nIf you received this, mail is configured correctly.") }}</textarea>
                @error('message')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Send test email</button>
        </form>

        <details class="setup-guide">
            <summary>Superhosting setup (support@api.goalgus.bg)</summary>
            <div class="setup-guide-body">
                <h2>cPanel vs Laravel — what applies?</h2>
                <p>The screen cPanel shows after creating a mailbox is mainly for <strong>email clients</strong> (Outlook, iPhone Mail, Thunderbird). Laravel only needs the <strong>outgoing SMTP</strong> part.</p>

                <div class="table-scroll">
                    <table class="setup-table">
                        <thead>
                            <tr>
                                <th>cPanel setting</th>
                                <th>Used by Laravel?</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>IMAP port 993 / POP3 995 (incoming)</td>
                                <td>No — only for reading mail in a client</td>
                            </tr>
                            <tr>
                                <td><strong>SMTP port 465</strong> (outgoing, SSL/TLS)</td>
                                <td>For email clients only — <strong>not</strong> for PHP on Superhosting (connection refused)</td>
                            </tr>
                            <tr>
                                <td><strong>SMTP port 25</strong> (on-server scripts)</td>
                                <td><strong>Yes — use this in Laravel on production</strong></td>
                            </tr>
                            <tr>
                                <td>CalDAV / CardDAV (ports 2080 / 2079)</td>
                                <td>No — calendar &amp; contacts only</td>
                            </tr>
                            <tr>
                                <td><code>mail.api.goalgus.bg</code> (non-SSL)</td>
                                <td>No — not recommended; use <code>api.goalgus.bg</code></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h2>Production <code>.env</code> (Superhosting — PHP on the server)</h2>
                <p>On the server only. Port <strong>465</strong> works from your Mac/phone but is <strong>refused</strong> when PHP on Superhosting connects — use port <strong>25</strong> without SSL per <a href="https://help.superhosting.bg/smtp-settings-in-script.html" target="_blank" rel="noopener">their script guide</a>:</p>

                <pre class="env-sample">MAIL_MAILER=smtp
MAIL_HOST=api.goalgus.bg
MAIL_PORT=25
MAIL_SCHEME=null
MAIL_USERNAME=support@api.goalgus.bg
MAIL_PASSWORD=your-cpanel-email-password
MAIL_FROM_ADDRESS=support@api.goalgus.bg
MAIL_FROM_NAME="${APP_NAME}"</pre>

                <p>SSH: <code>php artisan config:clear</code> after saving <code>.env</code>, then test again here.</p>

                <h2>Email clients (Outlook, iPhone)</h2>
                <p>cPanel “Secure SSL/TLS” with port <strong>465</strong> is correct for those apps — keep that in your mail app, not in Laravel on the server.</p>

                <h2>Also in cPanel</h2>
                <ul>
                    <li><strong>Email Deliverability</strong> — enable SPF and DKIM for <code>api.goalgus.bg</code></li>
                    <li><strong>Local Docker</strong> — keep <code>MAIL_MAILER=log</code>; real delivery is tested on production</li>
                </ul>
            </div>
        </details>
    </div>
@endsection
