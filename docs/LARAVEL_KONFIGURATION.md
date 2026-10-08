# Laravel-Konfigurationsreferenz

Stand: 2026-10-08. Automatisch aus der lokalen Laravel-Konfiguration erzeugt.

Diese Referenz umfasst alle aktuell geladenen Konfigurationsgruppen einschließlich Paketdefaults. Werte stammen aus dem CLI-Bootstrap; Middleware kann sie pro Request ändern. Keine vollständige `.env` wird kopiert. Zugangsdaten, Schlüssel und URLs werden vorsorglich maskiert; maskierte Werte ausschließlich lokal prüfen. Pfade sind Momentaufnahmen dieses Rechners. Objektwerte werden typisiert dargestellt.

Aktualisieren: `php scripts/Export-LaravelConfiguration.php`. Übersicht und Nutzung: [Laravel-Handbuch](../LARAVEL_HANDBUCH.md). Maßgeblich bleiben `config/`, `bootstrap/` und die lokale Umgebung.

## app

```json
{
    "name": "Laravel",
    "env": "local",
    "debug": true,
    "url": "[REDACTED: Wert lokal prüfen]",
    "frontend_url": "http://localhost:3000",
    "asset_url": null,
    "timezone": "UTC",
    "locale": "en",
    "fallback_locale": "en",
    "faker_locale": "en_US",
    "cipher": "AES-256-CBC",
    "key": "[REDACTED: Wert lokal prüfen]",
    "previous_keys": "[REDACTED: Wert lokal prüfen]",
    "maintenance": {
        "driver": "file",
        "store": "database"
    },
    "providers": [
        "Illuminate\\Auth\\AuthServiceProvider",
        "Illuminate\\Broadcasting\\BroadcastServiceProvider",
        "Illuminate\\Bus\\BusServiceProvider",
        "Illuminate\\Cache\\CacheServiceProvider",
        "Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider",
        "Illuminate\\Concurrency\\ConcurrencyServiceProvider",
        "Illuminate\\Cookie\\CookieServiceProvider",
        "Illuminate\\Database\\DatabaseServiceProvider",
        "Illuminate\\Encryption\\EncryptionServiceProvider",
        "Illuminate\\Filesystem\\FilesystemServiceProvider",
        "Illuminate\\Image\\ImageServiceProvider",
        "Illuminate\\Foundation\\Providers\\FoundationServiceProvider",
        "Illuminate\\Hashing\\HashServiceProvider",
        "Illuminate\\Mail\\MailServiceProvider",
        "Illuminate\\Notifications\\NotificationServiceProvider",
        "Illuminate\\Pagination\\PaginationServiceProvider",
        "Illuminate\\Auth\\Passwords\\PasswordResetServiceProvider",
        "Illuminate\\Pipeline\\PipelineServiceProvider",
        "Illuminate\\Queue\\QueueServiceProvider",
        "Illuminate\\Redis\\RedisServiceProvider",
        "Illuminate\\Session\\SessionServiceProvider",
        "Illuminate\\Translation\\TranslationServiceProvider",
        "Illuminate\\Validation\\ValidationServiceProvider",
        "Illuminate\\View\\ViewServiceProvider",
        "App\\Providers\\AppServiceProvider",
        "App\\Providers\\FortifyServiceProvider",
        "App\\Providers\\Filament\\AdminPanelProvider"
    ],
    "aliases": {
        "App": "Illuminate\\Support\\Facades\\App",
        "Arr": "Illuminate\\Support\\Arr",
        "Artisan": "Illuminate\\Support\\Facades\\Artisan",
        "Auth": "Illuminate\\Support\\Facades\\Auth",
        "Benchmark": "Illuminate\\Support\\Benchmark",
        "Blade": "Illuminate\\Support\\Facades\\Blade",
        "Broadcast": "Illuminate\\Support\\Facades\\Broadcast",
        "Bus": "Illuminate\\Support\\Facades\\Bus",
        "Cache": "Illuminate\\Support\\Facades\\Cache",
        "Concurrency": "Illuminate\\Support\\Facades\\Concurrency",
        "Config": "Illuminate\\Support\\Facades\\Config",
        "Context": "Illuminate\\Support\\Facades\\Context",
        "Cookie": "Illuminate\\Support\\Facades\\Cookie",
        "Crypt": "Illuminate\\Support\\Facades\\Crypt",
        "Date": "Illuminate\\Support\\Facades\\Date",
        "DB": "Illuminate\\Support\\Facades\\DB",
        "Eloquent": "Illuminate\\Database\\Eloquent\\Model",
        "Event": "Illuminate\\Support\\Facades\\Event",
        "File": "Illuminate\\Support\\Facades\\File",
        "Gate": "Illuminate\\Support\\Facades\\Gate",
        "Hash": "Illuminate\\Support\\Facades\\Hash",
        "Http": "Illuminate\\Support\\Facades\\Http",
        "Image": "Illuminate\\Support\\Facades\\Image",
        "Js": "Illuminate\\Support\\Js",
        "Lang": "Illuminate\\Support\\Facades\\Lang",
        "Log": "Illuminate\\Support\\Facades\\Log",
        "Mail": "Illuminate\\Support\\Facades\\Mail",
        "Notification": "Illuminate\\Support\\Facades\\Notification",
        "Number": "Illuminate\\Support\\Number",
        "Password": "[REDACTED: Wert lokal prüfen]",
        "Process": "Illuminate\\Support\\Facades\\Process",
        "Queue": "Illuminate\\Support\\Facades\\Queue",
        "RateLimiter": "Illuminate\\Support\\Facades\\RateLimiter",
        "Redirect": "Illuminate\\Support\\Facades\\Redirect",
        "Request": "Illuminate\\Support\\Facades\\Request",
        "Response": "Illuminate\\Support\\Facades\\Response",
        "Route": "Illuminate\\Support\\Facades\\Route",
        "Schedule": "Illuminate\\Support\\Facades\\Schedule",
        "Schema": "Illuminate\\Support\\Facades\\Schema",
        "Session": "Illuminate\\Support\\Facades\\Session",
        "Storage": "Illuminate\\Support\\Facades\\Storage",
        "Str": "Illuminate\\Support\\Str",
        "Uri": "Illuminate\\Support\\Uri",
        "URL": "[REDACTED: Wert lokal prüfen]",
        "Validator": "Illuminate\\Support\\Facades\\Validator",
        "View": "Illuminate\\Support\\Facades\\View",
        "Vite": "Illuminate\\Support\\Facades\\Vite"
    }
}
```

## artisan-runner-ui

```json
{
    "enabled": true,
    "path": "artisan",
    "middleware": [
        "web",
        "auth:web",
        "can:run-artisan-runner-ui"
    ],
    "environments": [
        "local",
        "qa",
        "staging",
        "uat"
    ],
    "allowed": [],
    "denied": [
        "tinker",
        "serve",
        "down",
        "up",
        "db:wipe",
        "migrate:fresh",
        "migrate:reset",
        "migrate:refresh",
        "migrate:rollback",
        "key:generate",
        "env:*",
        "queue:work",
        "queue:listen",
        "schedule:work",
        "schedule:run",
        "vendor:publish",
        "package:discover",
        "runner:create-user",
        "admin:grant-access",
        "make:filament-user"
    ]
}
```

## auth

```json
{
    "defaults": {
        "guard": "web",
        "passwords": "users"
    },
    "guards": {
        "web": {
            "driver": "session",
            "provider": "users"
        },
        "sanctum": {
            "driver": "sanctum",
            "provider": null
        }
    },
    "providers": {
        "users": {
            "driver": "eloquent",
            "model": "App\\Models\\User"
        }
    },
    "passwords": {
        "users": {
            "provider": "users",
            "table": "password_reset_tokens",
            "expire": 60,
            "throttle": 60
        }
    },
    "password_timeout": 10800
}
```

## blade-heroicons

```json
{
    "prefix": "heroicon",
    "fallback": "",
    "class": "",
    "attributes": []
}
```

## blade-icons

```json
{
    "sets": [],
    "class": "",
    "attributes": [],
    "fallback": "",
    "components": {
        "disabled": false,
        "default": "icon"
    }
}
```

## broadcasting

```json
{
    "default": "log",
    "connections": {
        "reverb": {
            "driver": "reverb",
            "key": "[REDACTED: Wert lokal prüfen]",
            "secret": "[REDACTED: Wert lokal prüfen]",
            "app_id": null,
            "options": {
                "host": null,
                "port": 443,
                "scheme": "https",
                "useTLS": true
            },
            "client_options": []
        },
        "pusher": {
            "driver": "pusher",
            "key": "[REDACTED: Wert lokal prüfen]",
            "secret": "[REDACTED: Wert lokal prüfen]",
            "app_id": null,
            "options": {
                "cluster": null,
                "host": "api-mt1.pusher.com",
                "port": 443,
                "scheme": "https",
                "encrypted": true,
                "useTLS": true
            },
            "client_options": []
        },
        "ably": {
            "driver": "ably",
            "key": "[REDACTED: Wert lokal prüfen]"
        },
        "mercure": {
            "driver": "mercure",
            "url": "[REDACTED: Wert lokal prüfen]",
            "public_url": null,
            "secret": "[REDACTED: Wert lokal prüfen]",
            "encryption_key": null,
            "claims": {
                "iss": null,
                "client_id": "Laravel"
            },
            "cookie_name": null,
            "subscribe_expiration": 5
        },
        "log": {
            "driver": "log"
        },
        "null": {
            "driver": "null"
        }
    }
}
```

## cache

```json
{
    "default": "database",
    "stores": {
        "array": {
            "driver": "array",
            "serialize": false
        },
        "session": {
            "driver": "session",
            "key": "[REDACTED: Wert lokal prüfen]"
        },
        "database": {
            "driver": "database",
            "connection": null,
            "table": "cache",
            "lock_connection": null,
            "lock_table": null
        },
        "file": {
            "driver": "file",
            "path": "C:\\Entwicklung\\Projekte\\Boilerplates\\LaravelAPI\\storage\\framework/cache/data",
            "lock_path": "C:\\Entwicklung\\Projekte\\Boilerplates\\LaravelAPI\\storage\\framework/cache/locks"
        },
        "storage": {
            "driver": "storage",
            "disk": null,
            "path": "framework/cache/data"
        },
        "memcached": {
            "driver": "memcached",
            "persistent_id": null,
            "sasl": [
                null,
                null
            ],
            "options": [],
            "servers": [
                {
                    "host": "127.0.0.1",
                    "port": 11211,
                    "weight": 100
                }
            ]
        },
        "redis": {
            "driver": "redis",
            "connection": "cache",
            "lock_connection": "default"
        },
        "dynamodb": {
            "driver": "dynamodb",
            "key": "[REDACTED: Wert lokal prüfen]",
            "secret": "[REDACTED: Wert lokal prüfen]",
            "region": "us-east-1",
            "table": "cache",
            "endpoint": null
        },
        "octane": {
            "driver": "octane"
        },
        "failover": {
            "driver": "failover",
            "stores": [
                "database",
                "array"
            ]
        }
    },
    "prefix": "laravel-cache-",
    "serializable_classes": false
}
```

## concurrency

```json
{
    "default": "process"
}
```

## cors

```json
{
    "paths": [
        "api/*",
        "sanctum/csrf-cookie"
    ],
    "allowed_methods": [
        "*"
    ],
    "allowed_origins": [
        "http://localhost:9200"
    ],
    "allowed_origins_patterns": [],
    "allowed_headers": [
        "*"
    ],
    "exposed_headers": [],
    "max_age": 0,
    "supports_credentials": true
}
```

## database

```json
{
    "default": "sqlite",
    "connections": {
        "sqlite": {
            "driver": "sqlite",
            "url": "[REDACTED: Wert lokal prüfen]",
            "database": "C:\\Entwicklung\\Projekte\\Boilerplates\\LaravelAPI\\database\\database.sqlite",
            "prefix": "",
            "foreign_key_constraints": true,
            "busy_timeout": null,
            "journal_mode": null,
            "synchronous": null,
            "transaction_mode": "DEFERRED"
        },
        "mysql": {
            "driver": "mysql",
            "url": "[REDACTED: Wert lokal prüfen]",
            "host": "127.0.0.1",
            "port": "3306",
            "database": "laravel",
            "username": "[REDACTED: Wert lokal prüfen]",
            "password": "[REDACTED: Wert lokal prüfen]",
            "unix_socket": "",
            "charset": "utf8mb4",
            "collation": "utf8mb4_unicode_ci",
            "prefix": "",
            "prefix_indexes": true,
            "strict": true,
            "engine": null,
            "options": []
        },
        "mariadb": {
            "driver": "mariadb",
            "url": "[REDACTED: Wert lokal prüfen]",
            "host": "127.0.0.1",
            "port": "3306",
            "database": "laravel",
            "username": "[REDACTED: Wert lokal prüfen]",
            "password": "[REDACTED: Wert lokal prüfen]",
            "unix_socket": "",
            "charset": "utf8mb4",
            "collation": "utf8mb4_unicode_ci",
            "prefix": "",
            "prefix_indexes": true,
            "strict": true,
            "engine": null,
            "options": []
        },
        "pgsql": {
            "driver": "pgsql",
            "url": "[REDACTED: Wert lokal prüfen]",
            "host": "127.0.0.1",
            "port": "5432",
            "database": "laravel",
            "username": "[REDACTED: Wert lokal prüfen]",
            "password": "[REDACTED: Wert lokal prüfen]",
            "charset": "utf8",
            "prefix": "",
            "prefix_indexes": true,
            "search_path": "public",
            "sslmode": "prefer"
        },
        "sqlsrv": {
            "driver": "sqlsrv",
            "url": "[REDACTED: Wert lokal prüfen]",
            "host": "localhost",
            "port": "1433",
            "database": "laravel",
            "username": "[REDACTED: Wert lokal prüfen]",
            "password": "[REDACTED: Wert lokal prüfen]",
            "charset": "utf8",
            "prefix": "",
            "prefix_indexes": true
        },
        "laravel-brain": {
            "driver": "mysql",
            "host": "127.0.0.1",
            "port": "3306",
            "database": null,
            "username": "[REDACTED: Wert lokal prüfen]",
            "password": "[REDACTED: Wert lokal prüfen]",
            "unix_socket": "",
            "charset": "utf8mb4",
            "collation": "utf8mb4_unicode_ci",
            "prefix": ""
        }
    },
    "migrations": {
        "table": "migrations",
        "update_date_on_publish": true
    },
    "redis": {
        "client": "phpredis",
        "options": {
            "cluster": "redis",
            "prefix": "laravel-database-",
            "persistent": false
        },
        "default": {
            "url": "[REDACTED: Wert lokal prüfen]",
            "host": "127.0.0.1",
            "username": "[REDACTED: Wert lokal prüfen]",
            "password": "[REDACTED: Wert lokal prüfen]",
            "port": "6379",
            "database": "0",
            "max_retries": 3,
            "backoff_algorithm": "decorrelated_jitter",
            "backoff_base": 100,
            "backoff_cap": 1000
        },
        "cache": {
            "url": "[REDACTED: Wert lokal prüfen]",
            "host": "127.0.0.1",
            "username": "[REDACTED: Wert lokal prüfen]",
            "password": "[REDACTED: Wert lokal prüfen]",
            "port": "6379",
            "database": "1",
            "max_retries": 3,
            "backoff_algorithm": "decorrelated_jitter",
            "backoff_base": 100,
            "backoff_cap": 1000
        }
    }
}
```

## filament

```json
{
    "broadcasting": [],
    "default_filesystem_disk": "local",
    "temporary_file_url_expiry_minutes": 30,
    "assets_path": null,
    "cache_path": "C:\\Entwicklung\\Projekte\\Boilerplates\\LaravelAPI\\bootstrap/cache/filament",
    "livewire_loading_delay": "default",
    "file_generation": {
        "flags": []
    },
    "system_route_prefix": "filament"
}
```

## filesystems

```json
{
    "default": "local",
    "disks": {
        "local": {
            "driver": "local",
            "root": "C:\\Entwicklung\\Projekte\\Boilerplates\\LaravelAPI\\storage\\app/private",
            "serve": true,
            "throw": false,
            "report": false
        },
        "public": {
            "driver": "local",
            "root": "C:\\Entwicklung\\Projekte\\Boilerplates\\LaravelAPI\\storage\\app/public",
            "url": "[REDACTED: Wert lokal prüfen]",
            "visibility": "public",
            "throw": false,
            "report": false
        },
        "s3": {
            "driver": "s3",
            "key": "[REDACTED: Wert lokal prüfen]",
            "secret": "[REDACTED: Wert lokal prüfen]",
            "region": "us-east-1",
            "bucket": "",
            "url": "[REDACTED: Wert lokal prüfen]",
            "endpoint": null,
            "use_path_style_endpoint": false,
            "throw": false,
            "report": false
        }
    },
    "links": {
        "C:\\Entwicklung\\Projekte\\Boilerplates\\LaravelAPI\\public\\storage": "C:\\Entwicklung\\Projekte\\Boilerplates\\LaravelAPI\\storage\\app/public"
    }
}
```

## fortify

```json
{
    "guard": "web",
    "middleware": [
        "api"
    ],
    "auth_middleware": "auth",
    "passwords": "users",
    "username": "[REDACTED: Wert lokal prüfen]",
    "email": "email",
    "views": false,
    "home": "/api/user",
    "prefix": "api",
    "domain": null,
    "lowercase_usernames": false,
    "limiters": {
        "login": "login"
    },
    "paths": {
        "login": null,
        "logout": null,
        "password": "[REDACTED: Wert lokal prüfen]",
        "register": null,
        "verification": {
            "notice": null,
            "verify": null,
            "send": null
        },
        "user-profile-information": {
            "update": null
        },
        "user-password": {
            "update": null
        },
        "two-factor": {
            "login": null,
            "enable": null,
            "confirm": null,
            "disable": null,
            "qr-code": null,
            "secret-key": "[REDACTED: Wert lokal prüfen]",
            "recovery-codes": null
        },
        "passkey": {
            "login-options": null,
            "login": null,
            "confirm-options": null,
            "confirm": null,
            "registration-options": null,
            "store": null,
            "destroy": null
        }
    },
    "redirects": {
        "login": null,
        "logout": null,
        "password-confirmation": null,
        "register": null,
        "email-verification": null,
        "password-reset": null
    },
    "passkeys": {
        "relying_party_id": "localhost",
        "allowed_origins": [
            "http://localhost:8000"
        ],
        "user_handle_secret": "[REDACTED: Wert lokal prüfen]",
        "timeout": 60000
    },
    "features": []
}
```

## hashing

```json
{
    "driver": "bcrypt",
    "bcrypt": {
        "rounds": "12",
        "verify": true,
        "limit": null
    },
    "argon": {
        "memory": 65536,
        "threads": 1,
        "time": 4,
        "verify": true
    },
    "rehash_on_login": true
}
```

## images

```json
{
    "default": "gd"
}
```

## laravel-brain

```json
{
    "driver": "file",
    "database": {
        "table": "laravel_brain_graphs",
        "connection": null,
        "connections": {
            "laravel-brain": {
                "driver": "mysql",
                "host": "127.0.0.1",
                "port": "3306",
                "database": null,
                "username": "[REDACTED: Wert lokal prüfen]",
                "password": "[REDACTED: Wert lokal prüfen]",
                "unix_socket": "",
                "charset": "utf8mb4",
                "collation": "utf8mb4_unicode_ci",
                "prefix": ""
            }
        }
    },
    "memory_limit": "1024M",
    "execution_time_limit": 300,
    "auto_discover_routes": false,
    "auto_discover_exclude_vendor": true,
    "security": {
        "auth_middleware": [],
        "throttle_middleware": [],
        "trusted_route_names": [],
        "trusted_route_uris": []
    },
    "route_paths": [
        "routes/*/*.php"
    ],
    "channel_paths": [
        "routes/*/*.php"
    ],
    "channel_registrars": [],
    "dispatch": {
        "helpers": []
    },
    "job_groups": {
        "enabled": true
    },
    "events": {
        "enabled": true,
        "paths": [
            "app/Events"
        ]
    },
    "listeners": {
        "paths": [
            "app/Listeners"
        ],
        "provider_paths": [
            "app/Providers"
        ]
    },
    "actions": {
        "enabled": true,
        "paths": [
            "app/Actions"
        ]
    },
    "transactions": {
        "enabled": true
    },
    "broadcasting": {
        "enabled": true,
        "paths": [
            "app/Events"
        ]
    },
    "macros": {
        "enabled": true,
        "paths": [
            "app"
        ]
    },
    "observers": {
        "model_paths": [
            "app/Models"
        ],
        "provider_paths": [
            "app/Providers"
        ]
    },
    "policies": {
        "provider_paths": [
            "app/Providers"
        ]
    },
    "commands": {
        "console_route_paths": [
            "routes/*/*.php"
        ],
        "class_paths": [
            "app/Console/Commands/*/*.php"
        ],
        "kernel_paths": [
            "app/Console/Kernel.php",
            "bootstrap/app.php"
        ]
    },
    "models": {
        "paths": [
            "app/Models"
        ]
    },
    "morph_map": {
        "enabled": true
    },
    "filament": {
        "panel_paths": [
            "app/Providers/Filament"
        ],
        "paths": [
            "app/Filament"
        ]
    },
    "ai": {
        "enabled": true
    },
    "container_bindings": {
        "provider_paths": [
            "app/Providers"
        ]
    },
    "service_providers": {
        "enabled": true
    },
    "facades": {
        "paths": [
            "app"
        ]
    },
    "source_paths": [
        "app",
        "src"
    ],
    "reachability": {
        "enabled": false
    },
    "watch_paths": [
        "app",
        "routes",
        "config"
    ],
    "views": {
        "paths": [
            "resources/views"
        ]
    },
    "table_stats": {
        "enabled": true,
        "connection": null
    },
    "schema": {
        "enabled": true,
        "connection": null,
        "timeout": 2
    },
    "churn": {
        "enabled": true,
        "since": "1 year ago",
        "max_output_bytes": 20000000,
        "limit": 50
    },
    "livewire": {
        "component_paths": [
            "app/Http/Livewire",
            "app/Livewire",
            "app/View/Components"
        ]
    },
    "cache_operations": {
        "enabled": true
    },
    "outgoing_http": {
        "enabled": true
    },
    "mcp": {
        "enabled": true
    }
}
```

## livewire

```json
{
    "component_locations": [
        "C:\\Entwicklung\\Projekte\\Boilerplates\\LaravelAPI\\resources\\views/components",
        "C:\\Entwicklung\\Projekte\\Boilerplates\\LaravelAPI\\resources\\views/livewire"
    ],
    "component_namespaces": {
        "layouts": "C:\\Entwicklung\\Projekte\\Boilerplates\\LaravelAPI\\resources\\views/layouts",
        "pages": "C:\\Entwicklung\\Projekte\\Boilerplates\\LaravelAPI\\resources\\views/pages"
    },
    "component_layout": "layouts::app",
    "component_placeholder": null,
    "make_command": {
        "type": "sfc",
        "emoji": true,
        "with": {
            "js": false,
            "css": false,
            "test": false
        }
    },
    "class_namespace": "App\\Livewire",
    "class_path": "C:\\Entwicklung\\Projekte\\Boilerplates\\LaravelAPI\\app\\Livewire",
    "view_path": "C:\\Entwicklung\\Projekte\\Boilerplates\\LaravelAPI\\resources\\views/livewire",
    "temporary_file_upload": {
        "disk": null,
        "rules": null,
        "directory": null,
        "middleware": null,
        "preview_mimes": [
            "png",
            "gif",
            "bmp",
            "svg",
            "wav",
            "mp4",
            "mov",
            "avi",
            "wmv",
            "mp3",
            "m4a",
            "jpg",
            "jpeg",
            "mpga",
            "webp",
            "wma"
        ],
        "max_upload_time": 5,
        "cleanup": true
    },
    "render_on_redirect": false,
    "legacy_model_binding": false,
    "inject_assets": true,
    "navigate": {
        "show_progress_bar": true,
        "progress_bar_color": "#2299dd"
    },
    "inject_morph_markers": true,
    "smart_wire_keys": true,
    "pagination_theme": "tailwind",
    "release_token": "a",
    "csp_safe": false,
    "payload": {
        "max_size": 1048576,
        "max_nesting_depth": 10,
        "max_calls": 50,
        "max_components": 200
    }
}
```

## logging

```json
{
    "default": "stack",
    "deprecations": {
        "channel": null,
        "trace": false
    },
    "channels": {
        "stack": {
            "driver": "stack",
            "channels": [
                "single"
            ],
            "ignore_exceptions": false
        },
        "single": {
            "driver": "single",
            "path": "C:\\Entwicklung\\Projekte\\Boilerplates\\LaravelAPI\\storage\\logs/laravel.log",
            "level": "debug",
            "replace_placeholders": true
        },
        "daily": {
            "driver": "daily",
            "path": "C:\\Entwicklung\\Projekte\\Boilerplates\\LaravelAPI\\storage\\logs/laravel.log",
            "level": "debug",
            "max_files": 14,
            "replace_placeholders": true
        },
        "monthly": {
            "driver": "monthly",
            "path": "C:\\Entwicklung\\Projekte\\Boilerplates\\LaravelAPI\\storage\\logs/laravel.log",
            "level": "debug",
            "max_files": 3,
            "replace_placeholders": true
        },
        "slack": {
            "driver": "slack",
            "url": "[REDACTED: Wert lokal prüfen]",
            "username": "[REDACTED: Wert lokal prüfen]",
            "emoji": ":boom:",
            "level": "debug",
            "replace_placeholders": true
        },
        "papertrail": {
            "driver": "monolog",
            "level": "debug",
            "handler": "Monolog\\Handler\\SyslogUdpHandler",
            "handler_with": {
                "host": null,
                "port": null,
                "connectionString": "tls://:"
            },
            "processors": [
                "Monolog\\Processor\\PsrLogMessageProcessor"
            ]
        },
        "stderr": {
            "driver": "monolog",
            "level": "debug",
            "handler": "Monolog\\Handler\\StreamHandler",
            "handler_with": {
                "stream": "php://stderr"
            },
            "formatter": null,
            "processors": [
                "Monolog\\Processor\\PsrLogMessageProcessor"
            ]
        },
        "syslog": {
            "driver": "syslog",
            "level": "debug",
            "facility": 8,
            "replace_placeholders": true
        },
        "errorlog": {
            "driver": "errorlog",
            "level": "debug",
            "replace_placeholders": true
        },
        "null": {
            "driver": "monolog",
            "handler": "Monolog\\Handler\\NullHandler"
        },
        "emergency": {
            "path": "C:\\Entwicklung\\Projekte\\Boilerplates\\LaravelAPI\\storage\\logs/laravel.log"
        }
    }
}
```

## mail

```json
{
    "default": "log",
    "mailers": {
        "smtp": {
            "transport": "smtp",
            "scheme": null,
            "url": "[REDACTED: Wert lokal prüfen]",
            "host": "127.0.0.1",
            "port": "2525",
            "username": "[REDACTED: Wert lokal prüfen]",
            "password": "[REDACTED: Wert lokal prüfen]",
            "timeout": null,
            "local_domain": "localhost"
        },
        "ses": {
            "transport": "ses"
        },
        "postmark": {
            "transport": "postmark"
        },
        "resend": {
            "transport": "resend"
        },
        "sendmail": {
            "transport": "sendmail",
            "path": "/usr/sbin/sendmail -bs -i"
        },
        "log": {
            "transport": "log",
            "channel": null
        },
        "array": {
            "transport": "array"
        },
        "failover": {
            "transport": "failover",
            "mailers": [
                "smtp",
                "log"
            ],
            "retry_after": 60
        },
        "roundrobin": {
            "transport": "roundrobin",
            "mailers": [
                "ses",
                "postmark"
            ],
            "retry_after": 60
        }
    },
    "from": {
        "address": "hello@example.com",
        "name": "Laravel"
    },
    "markdown": {
        "theme": "default",
        "paths": [
            "C:\\Entwicklung\\Projekte\\Boilerplates\\LaravelAPI\\resources\\views/vendor/mail"
        ],
        "extensions": []
    }
}
```

## media-library

```json
{
    "disk_name": "public",
    "conversions_disk_name": null,
    "max_file_size": 10485760,
    "disallowed_extensions": [
        "php",
        "php3",
        "php4",
        "php5",
        "php6",
        "php7",
        "php8",
        "phtml",
        "phtm",
        "pht",
        "phps",
        "phar",
        "shtml",
        "shtm",
        "stm",
        "htaccess",
        "htpasswd",
        "cgi",
        "pl",
        "asp",
        "aspx",
        "jsp",
        "jspx"
    ],
    "allowed_extensions": null,
    "queue_connection_name": "database",
    "queue_name": "",
    "queue_conversions_by_default": true,
    "queue_conversions_after_database_commit": true,
    "media_model": "Spatie\\MediaLibrary\\MediaCollections\\Models\\Media",
    "media_observer": "Spatie\\MediaLibrary\\MediaCollections\\Models\\Observers\\MediaObserver",
    "use_default_collection_serialization": false,
    "temporary_upload_model": "Spatie\\MediaLibraryPro\\Models\\TemporaryUpload",
    "enable_temporary_uploads_session_affinity": true,
    "generate_thumbnails_for_temporary_uploads": true,
    "file_namer": "Spatie\\MediaLibrary\\Support\\FileNamer\\DefaultFileNamer",
    "path_generator": "Spatie\\MediaLibrary\\Support\\PathGenerator\\DefaultPathGenerator",
    "file_remover_class": "Spatie\\MediaLibrary\\Support\\FileRemover\\DefaultFileRemover",
    "custom_path_generators": [],
    "url_generator": "Spatie\\MediaLibrary\\Support\\UrlGenerator\\DefaultUrlGenerator",
    "moves_media_on_update": false,
    "version_urls": false,
    "image_optimizers": {
        "Spatie\\ImageOptimizer\\Optimizers\\Jpegoptim": [
            "-m85",
            "--force",
            "--strip-all",
            "--all-progressive"
        ],
        "Spatie\\ImageOptimizer\\Optimizers\\Pngquant": [
            "--force"
        ],
        "Spatie\\ImageOptimizer\\Optimizers\\Optipng": [
            "-i0",
            "-o2",
            "-quiet"
        ],
        "Spatie\\ImageOptimizer\\Optimizers\\Svgo": [
            "--disable=cleanupIDs"
        ],
        "Spatie\\ImageOptimizer\\Optimizers\\Gifsicle": [
            "-b",
            "-O3"
        ],
        "Spatie\\ImageOptimizer\\Optimizers\\Cwebp": [
            "-m 6",
            "-pass 10",
            "-mt",
            "-q 90"
        ],
        "Spatie\\ImageOptimizer\\Optimizers\\Avifenc": [
            "-a cq-level=23",
            "-j all",
            "--min 0",
            "--max 63",
            "--minalpha 0",
            "--maxalpha 63",
            "-a end-usage=q",
            "-a tune=ssim"
        ]
    },
    "image_generators": [
        "Spatie\\MediaLibrary\\Conversions\\ImageGenerators\\Image",
        "Spatie\\MediaLibrary\\Conversions\\ImageGenerators\\Webp",
        "Spatie\\MediaLibrary\\Conversions\\ImageGenerators\\Avif",
        "Spatie\\MediaLibrary\\Conversions\\ImageGenerators\\Pdf",
        "Spatie\\MediaLibrary\\Conversions\\ImageGenerators\\Svg",
        "Spatie\\MediaLibrary\\Conversions\\ImageGenerators\\Video"
    ],
    "temporary_directory_path": null,
    "image_driver": "gd",
    "ffmpeg_path": "/usr/bin/ffmpeg",
    "ffprobe_path": "/usr/bin/ffprobe",
    "ffmpeg_timeout": 900,
    "ffmpeg_threads": 0,
    "jobs": {
        "perform_conversions": "Spatie\\MediaLibrary\\Conversions\\Jobs\\PerformConversionsJob",
        "generate_responsive_images": "Spatie\\MediaLibrary\\ResponsiveImages\\Jobs\\GenerateResponsiveImagesJob"
    },
    "media_downloader": "Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader",
    "media_downloader_ssl": true,
    "temporary_url_default_lifetime": 5,
    "remote": {
        "extra_headers": {
            "CacheControl": "max-age=604800"
        }
    },
    "responsive_images": {
        "width_calculator": "Spatie\\MediaLibrary\\ResponsiveImages\\WidthCalculator\\FileSizeOptimizedWidthCalculator",
        "use_tiny_placeholders": true,
        "tiny_placeholder_generator": "Spatie\\MediaLibrary\\ResponsiveImages\\TinyPlaceholderGenerator\\Blurred"
    },
    "enable_vapor_uploads": false,
    "default_loading_attribute_value": null,
    "prefix": "",
    "force_lazy_loading": true
}
```

## passkeys

```json
{
    "relying_party_id": "localhost",
    "allowed_origins": [
        "http://localhost:8000"
    ],
    "user_handle_secret": "[REDACTED: Wert lokal prüfen]",
    "timeout": 60000,
    "guard": "web",
    "middleware": [
        "api"
    ],
    "management_middleware": [
        "password.confirm"
    ],
    "throttle": null,
    "redirect": "/api/user"
}
```

## permission

```json
{
    "models": {
        "permission": "Spatie\\Permission\\Models\\Permission",
        "role": "Spatie\\Permission\\Models\\Role",
        "team": null,
        "default_model": null
    },
    "table_names": {
        "roles": "roles",
        "permissions": "permissions",
        "model_has_permissions": "model_has_permissions",
        "model_has_roles": "model_has_roles",
        "role_has_permissions": "role_has_permissions"
    },
    "column_names": {
        "role_pivot_key": null,
        "permission_pivot_key": null,
        "model_morph_key": "model_id",
        "team_foreign_key": "team_id"
    },
    "register_permission_check_method": true,
    "register_octane_reset_listener": false,
    "events_enabled": false,
    "teams": false,
    "team_resolver": "Spatie\\Permission\\DefaultTeamResolver",
    "use_passport_client_credentials": false,
    "display_permission_in_exception": false,
    "display_role_in_exception": false,
    "enable_wildcard_permission": false,
    "cache": {
        "expiration_time": {
            "type": "DateInterval",
            "interval": "P0Y0M0DT24H0M0S"
        },
        "key": "[REDACTED: Wert lokal prüfen]",
        "store": "default"
    }
}
```

## queue

```json
{
    "default": "database",
    "connections": {
        "sync": {
            "driver": "sync"
        },
        "database": {
            "driver": "database",
            "connection": null,
            "table": "jobs",
            "queue": "default",
            "retry_after": 90,
            "after_commit": false
        },
        "beanstalkd": {
            "driver": "beanstalkd",
            "host": "localhost",
            "queue": "default",
            "retry_after": 90,
            "block_for": 0,
            "after_commit": false
        },
        "sqs": {
            "driver": "sqs",
            "key": "[REDACTED: Wert lokal prüfen]",
            "secret": "[REDACTED: Wert lokal prüfen]",
            "prefix": "https://sqs.us-east-1.amazonaws.com/your-account-id",
            "queue": "default",
            "suffix": null,
            "region": "us-east-1",
            "after_commit": false
        },
        "redis": {
            "driver": "redis",
            "connection": "default",
            "queue": "default",
            "retry_after": 90,
            "block_for": null,
            "after_commit": false
        },
        "deferred": {
            "driver": "deferred"
        },
        "failover": {
            "driver": "failover",
            "connections": [
                "database",
                "deferred"
            ]
        },
        "background": {
            "driver": "background"
        }
    },
    "batching": {
        "database": "sqlite",
        "table": "job_batches"
    },
    "failed": {
        "driver": "database-uuids",
        "database": "sqlite",
        "table": "failed_jobs"
    }
}
```

## sanctum

```json
{
    "stateful": [
        "localhost:9200"
    ],
    "guard": [
        "web"
    ],
    "expiration": null,
    "token_prefix": "",
    "middleware": {
        "authenticate_session": "Laravel\\Sanctum\\Http\\Middleware\\AuthenticateSession",
        "encrypt_cookies": "Illuminate\\Cookie\\Middleware\\EncryptCookies",
        "validate_csrf_token": "Illuminate\\Foundation\\Http\\Middleware\\ValidateCsrfToken"
    }
}
```

## services

```json
{
    "postmark": {
        "key": "[REDACTED: Wert lokal prüfen]"
    },
    "resend": {
        "key": "[REDACTED: Wert lokal prüfen]"
    },
    "ses": {
        "key": "[REDACTED: Wert lokal prüfen]",
        "secret": "[REDACTED: Wert lokal prüfen]",
        "region": "us-east-1"
    },
    "slack": {
        "notifications": {
            "bot_user_oauth_token": "[REDACTED: Wert lokal prüfen]",
            "channel": null
        }
    }
}
```

## session

```json
{
    "driver": "database",
    "lifetime": 120,
    "expire_on_close": false,
    "encrypt": false,
    "files": "C:\\Entwicklung\\Projekte\\Boilerplates\\LaravelAPI\\storage\\framework/sessions",
    "connection": null,
    "table": "sessions",
    "store": null,
    "lottery": [
        2,
        100
    ],
    "cookie": "laravel-session",
    "path": "/",
    "domain": null,
    "secure": null,
    "http_only": true,
    "same_site": "lax",
    "partitioned": false,
    "serialization": "json"
}
```

## telescope

```json
{
    "enabled": true,
    "domain": null,
    "path": "telescope",
    "driver": "database",
    "storage": {
        "database": {
            "connection": "sqlite",
            "chunk": 1000
        }
    },
    "queue": {
        "connection": null,
        "queue": null,
        "delay": 10
    },
    "middleware": [
        "web",
        "Laravel\\Telescope\\Http\\Middleware\\Authorize"
    ],
    "only_paths": [],
    "ignore_paths": [
        "livewire*",
        "nova-api*",
        "pulse*",
        "_boost*",
        ".well-known*"
    ],
    "ignore_commands": [],
    "watchers": {
        "Laravel\\Telescope\\Watchers\\BatchWatcher": true,
        "Laravel\\Telescope\\Watchers\\CacheWatcher": {
            "enabled": true,
            "hidden": [],
            "ignore": []
        },
        "Laravel\\Telescope\\Watchers\\ClientRequestWatcher": {
            "enabled": true,
            "ignore_hosts": []
        },
        "Laravel\\Telescope\\Watchers\\CommandWatcher": {
            "enabled": true,
            "ignore": []
        },
        "Laravel\\Telescope\\Watchers\\DumpWatcher": {
            "enabled": true,
            "always": false
        },
        "Laravel\\Telescope\\Watchers\\EventWatcher": {
            "enabled": true,
            "ignore": []
        },
        "Laravel\\Telescope\\Watchers\\ExceptionWatcher": true,
        "Laravel\\Telescope\\Watchers\\GateWatcher": {
            "enabled": true,
            "ignore_abilities": [],
            "ignore_packages": true,
            "ignore_paths": []
        },
        "Laravel\\Telescope\\Watchers\\JobWatcher": true,
        "Laravel\\Telescope\\Watchers\\LogWatcher": {
            "enabled": true,
            "level": "error"
        },
        "Laravel\\Telescope\\Watchers\\MailWatcher": true,
        "Laravel\\Telescope\\Watchers\\ModelWatcher": {
            "enabled": true,
            "events": [
                "eloquent.*"
            ],
            "hydrations": true
        },
        "Laravel\\Telescope\\Watchers\\NotificationWatcher": true,
        "Laravel\\Telescope\\Watchers\\QueryWatcher": {
            "enabled": true,
            "ignore_packages": true,
            "ignore_paths": [],
            "slow": 100
        },
        "Laravel\\Telescope\\Watchers\\RedisWatcher": true,
        "Laravel\\Telescope\\Watchers\\RequestWatcher": {
            "enabled": true,
            "size_limit": 64,
            "ignore_http_methods": [],
            "ignore_status_codes": []
        },
        "Laravel\\Telescope\\Watchers\\ScheduleWatcher": true,
        "Laravel\\Telescope\\Watchers\\ViewWatcher": true
    }
}
```

## tinker

```json
{
    "commands": [],
    "alias": [],
    "dont_alias": [
        "App\\Nova"
    ],
    "trust_project": "always"
}
```

## view

```json
{
    "paths": [
        "C:\\Entwicklung\\Projekte\\Boilerplates\\LaravelAPI\\resources\\views"
    ],
    "compiled": "C:\\Entwicklung\\Projekte\\Boilerplates\\LaravelAPI\\storage\\framework\\views"
}
```

