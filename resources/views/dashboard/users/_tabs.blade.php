<nav class="tab-nav" aria-label="Users sections">
    <a href="{{ route('dashboard.users') }}"
       class="tab-link {{ request()->routeIs('dashboard.users') ? 'is-active' : '' }}">
        Users
    </a>
    <a href="{{ route('dashboard.users.roles') }}"
       class="tab-link {{ request()->routeIs('dashboard.users.roles') ? 'is-active' : '' }}">
        Roles
    </a>
</nav>
