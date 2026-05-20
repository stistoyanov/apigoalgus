@extends('layouts.dashboard')

@section('title', $site->name.' — API Tokens')

@section('content')
    <div class="panel">
        <div class="panel-head">
            <div>
                <h1>{{ $site->name }}</h1>
                <p class="lead">Bearer tokens for {{ $site->domain }} to fetch content from the API.</p>
            </div>
        </div>

        @include('dashboard.sites._tabs', ['locale' => $site->default_locale])

        @if ($rawToken)
            <div class="status-banner" role="alert">
                <strong>New token — copy now:</strong>
                <div class="share-link" style="margin-top: 0.5rem;">
                    <input type="text" value="{{ $rawToken }}" readonly>
                    <button type="button" class="btn btn-outline btn-sm" data-copy="{{ $rawToken }}">Copy</button>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('dashboard.sites.tokens.store', $site) }}" class="toolbar">
            @csrf
            <label>
                Token name
                <input type="text" name="name" placeholder="barbergarage.bg production" required maxlength="120">
            </label>
            <button type="submit" class="btn btn-primary">Create token</button>
        </form>

        <p class="muted small">API endpoint: <code>{{ url('/api/v1/site/content') }}</code> or <code>{{ url('/api/v1/site/content/bg') }}</code></p>

        @if ($tokens->isEmpty())
            <p class="muted-block">No tokens yet. Create one for barbergarage.bg to connect.</p>
        @else
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Token</th>
                            <th>Last used</th>
                            <th>Status</th>
                            <th class="col-actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tokens as $token)
                            @php $raw = $token->rawToken(); @endphp
                            <tr>
                                <td>{{ $token->name }}</td>
                                <td>
                                    <div class="token-cell">
                                        @if ($raw)
                                            <input type="password" class="token-input" value="{{ $raw }}" readonly data-token-input>
                                            <button type="button" class="btn btn-outline btn-sm" data-token-reveal aria-label="Show token">Show</button>
                                            <button type="button" class="btn btn-outline btn-sm" data-copy="{{ $raw }}">Copy</button>
                                        @else
                                            <span class="mono muted">{{ $token->prefix }}…</span>
                                            <span class="muted small">(legacy — revoke & recreate to view)</span>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $token->last_used_at?->format('Y-m-d H:i') ?? '—' }}</td>
                                <td>
                                    @if ($token->revoked_at)
                                        <span class="muted">Revoked</span>
                                    @elseif ($token->expires_at && $token->expires_at->isPast())
                                        <span class="muted">Expired</span>
                                    @else
                                        <span class="badge badge-success">Active</span>
                                    @endif
                                </td>
                                <td class="col-actions">
                                    @if ($token->isActive())
                                        <form method="POST" action="{{ route('dashboard.sites.tokens.revoke', [$site, $token]) }}" class="inline-form" onsubmit="return confirm('Revoke this token?');">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm">Revoke</button>
                                        </form>
                                    @else
                                        <span class="muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('[data-copy]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var text = btn.getAttribute('data-copy');
            var done = function () {
                var original = btn.textContent;
                btn.textContent = 'Copied!';
                setTimeout(function () { btn.textContent = original; }, 1500);
            };
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(done).catch(function () { fallback(text, done); });
            } else {
                fallback(text, done);
            }
        });
    });

    function fallback(text, done) {
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.select();
        try { document.execCommand('copy'); done(); } catch (e) { /* noop */ }
        document.body.removeChild(ta);
    }

    document.querySelectorAll('[data-token-reveal]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = btn.parentElement.querySelector('[data-token-input]');
            if (!input) return;
            var hidden = input.type === 'password';
            input.type = hidden ? 'text' : 'password';
            btn.textContent = hidden ? 'Hide' : 'Show';
        });
    });
</script>
@endpush
