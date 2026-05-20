<nav class="tab-nav" aria-label="Site sections">
    <a href="{{ route('dashboard.sites.content', ['site' => $site->slug, 'locale' => $locale ?? $site->default_locale]) }}"
       class="tab-link {{ request()->routeIs('dashboard.sites.content*') ? 'is-active' : '' }}">
        Content
    </a>
    <a href="{{ route('dashboard.sites.media', $site) }}"
       class="tab-link {{ request()->routeIs('dashboard.sites.media*') ? 'is-active' : '' }}">
        Media
    </a>
    <a href="{{ route('dashboard.sites.settings', $site) }}"
       class="tab-link {{ request()->routeIs('dashboard.sites.settings*') ? 'is-active' : '' }}">
        Settings
    </a>
    <a href="{{ route('dashboard.sites.tokens', $site) }}"
       class="tab-link {{ request()->routeIs('dashboard.sites.tokens*') ? 'is-active' : '' }}">
        API Tokens
    </a>
</nav>
