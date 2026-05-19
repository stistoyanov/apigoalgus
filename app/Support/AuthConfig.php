<?php

namespace App\Support;

final class AuthConfig
{
    /** Session idle timeout in minutes (24 hours). Not configurable via .env. */
    public const SESSION_LIFETIME_MINUTES = 1440;
}
