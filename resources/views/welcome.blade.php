<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Goalgus API — backend services for goalgus.bg">
    <title>{{ config('app.name', 'Goalgus API') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/GoalgusLogoIcon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>
<body>
    <div class="nav-backdrop" aria-hidden="true"></div>

    <header class="site-header">
        <div class="container header-inner">
            <a href="{{ url('/') }}" class="brand">
                <img src="{{ asset('images/GoalgusLogo.png') }}" alt="Goalgus" width="160" height="44">
            </a>
            <button type="button" class="nav-toggle" aria-label="Open menu" aria-expanded="false">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <nav class="nav" aria-label="Main">
                <a href="#features">Features</a>
                <a href="#status">Status</a>
                <a href="https://goalgus.bg" target="_blank" rel="noopener noreferrer">goalgus.bg</a>
                <a href="{{ url('/up') }}" class="btn btn-primary">API health</a>
            </nav>
        </div>
    </header>

    <section class="hero">
        <div class="hero-bg" aria-hidden="true">
            <img src="{{ asset('images/GoalgusBackground.jpg') }}" alt="">
        </div>
        <div class="hero-overlay" aria-hidden="true"></div>
        <div class="container hero-content">
            <div class="hero-badge">
                <img src="{{ asset('images/GoalgusLogoIcon.png') }}" alt="">
                <span>api.goalgus.bg</span>
            </div>
            <h1>{{ config('app.name', 'Goalgus API') }}</h1>
            <p>
                Reliable backend services for the Goalgus platform. Built with Laravel,
                designed for clarity, security, and performance.
            </p>
            <div class="hero-actions">
                <a href="{{ url('/up') }}" class="btn btn-primary">Check API status</a>
                <a href="https://goalgus.bg" class="btn btn-outline" target="_blank" rel="noopener noreferrer">Visit goalgus.bg</a>
            </div>
        </div>
    </section>

    <section class="features" id="features">
        <div class="container">
            <div class="section-head">
                <h2>Built for production</h2>
                <p>A focused API layer — no unnecessary complexity, no frontend build step.</p>
            </div>
            <div class="feature-grid">
                <article class="feature-card">
                    <div class="feature-icon" aria-hidden="true">⚡</div>
                    <h3>Synchronous processing</h3>
                    <p>Jobs run inline for predictable behaviour and simpler operations on shared hosting.</p>
                </article>
                <article class="feature-card">
                    <div class="feature-icon" aria-hidden="true">🔒</div>
                    <h3>Secure by default</h3>
                    <p>Environment isolation between local Docker and live Superhosting deployments.</p>
                </article>
                <article class="feature-card">
                    <div class="feature-icon" aria-hidden="true">📡</div>
                    <h3>Scheduled tasks</h3>
                    <p>Cron-driven scheduler in production; dedicated container locally for parity.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="status-strip" id="status">
        <div class="container">
            <div class="status-inner">
                <div>
                    <h2>System health</h2>
                    <p>Laravel {{ Illuminate\Foundation\Application::VERSION }} · PHP {{ PHP_VERSION }}</p>
                </div>
                <div>
                    <span class="status-pill">
                        <span class="status-dot" aria-hidden="true"></span>
                        Endpoint: /up
                    </span>
                </div>
            </div>
        </div>
    </section>

    <footer class="site-footer">
        <div class="container footer-inner">
            <img src="{{ asset('images/GoalgusLogo.png') }}" alt="Goalgus">
            <nav class="footer-links" aria-label="Footer">
                <a href="https://goalgus.bg" target="_blank" rel="noopener noreferrer">goalgus.bg</a>
                <a href="{{ url('/up') }}">Health check</a>
            </nav>
            <p>&copy; <span id="year"></span> Goalgus. All rights reserved.</p>
        </div>
    </footer>

    <script src="{{ asset('js/landing.js') }}" defer></script>
</body>
</html>
