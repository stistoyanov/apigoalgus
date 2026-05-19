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
            @elseif ($mailer === 'sendmail')
                <div class="meta-card">
                    <strong>Sendmail</strong>
                    <code class="sendmail-path">{{ $sendmailPath }}</code>
                </div>
            @endif
        </div>

        @if ($configCached)
            <div class="status-banner warn">
                Configuration is <strong>cached</strong> (deploy runs <code>config:cache</code>). After editing
                <code>.env</code> on the server, run <code>php artisan config:clear</code> or changes will not apply.
            </div>
        @endif

        @if (! in_array($mailer, ['log', 'smtp', 'sendmail', 'array'], true))
            <div class="status-banner error">
                <code>MAIL_MAILER={{ $mailer }}</code> is not valid. Use <code>smtp</code> or <code>sendmail</code> —
                <code>localhost</code> is a <strong>host</strong> (<code>MAIL_HOST</code>), not a mailer.
            </div>
        @endif

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

                <h2><code>MAIL_MAILER</code> vs <code>MAIL_HOST</code></h2>
                <ul>
                    <li><code>MAIL_MAILER</code> — driver: <code>smtp</code>, <code>sendmail</code>, or <code>log</code> (never <code>localhost</code>)</li>
                    <li><code>MAIL_HOST</code> — server name when using SMTP: <code>localhost</code> or <code>api.goalgus.bg</code></li>
                </ul>

                <h2>Production <code>.env</code> — try SMTP on localhost:25 first</h2>
                <pre class="env-sample">MAIL_MAILER=smtp
MAIL_HOST=localhost
MAIL_PORT=25
MAIL_SCHEME=null
MAIL_USERNAME=support@api.goalgus.bg
MAIL_PASSWORD=your-cpanel-email-password
MAIL_FROM_ADDRESS=support@api.goalgus.bg
MAIL_FROM_NAME="${APP_NAME}"</pre>

                <p>Do <strong>not</strong> set <code>MAIL_SCHEME=smtps</code> or port <code>465</code> on the server.</p>
                <p>After saving <code>.env</code>: <code>php artisan config:clear</code> (required — deploy caches config).</p>

                <h2>Fallback — sendmail (no SMTP host/port)</h2>
                <pre class="env-sample">MAIL_MAILER=sendmail
MAIL_FROM_ADDRESS=support@api.goalgus.bg
MAIL_FROM_NAME="${APP_NAME}"</pre>

                <h2>Email clients (Outlook, iPhone)</h2>
                <p>cPanel port <strong>465</strong> + SSL is only for mail apps on your computer/phone, not for PHP on Superhosting.</p>

                <h2>Also in cPanel</h2>
                <ul>
                    <li><strong>Email Deliverability</strong> — enable SPF and DKIM for <code>api.goalgus.bg</code></li>
                    <li><strong>Local Docker</strong> — keep <code>MAIL_MAILER=log</code>; real delivery is tested on production</li>
                </ul>
            </div>
        </details>
    </div>
@endsection
