# Laravel queue workers (Supervisor on Cloudways)

Supervisor configs for Laravel workers using **Redis** (`QUEUE_CONNECTION=redis`).

| File | Purpose |
|------|---------|
| `laravel-worker-staging.conf` | Staging: 1 process, smaller logs |
| `laravel-worker-production.conf` | Production: 2 processes, larger logs |

---

## Server `.env`

On staging and production, ensure:

```env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_CLIENT=phpredis
```

Use `predis` only if that is what the app installs and `config/database.php` expects.

---

## Placeholders in each `.conf`

| Placeholder | Where to get it |
|-------------|-----------------|
| `<STAGING_APP_FOLDER>` / `<PROD_APP_FOLDER>` | Cloudways → Application → **Access Details** → *Application Folder* |
| `<STAGING_APP_USER>` / `<PROD_APP_USER>` | Cloudways → **Access Details** → *Username* (e.g. `master_…`) |
| `<PHP_BINARY>` | SSH: `which php8.2` (match the PHP version selected for the app) |

---

## Install via SSH

```bash
ssh <USERNAME>@<SERVER_IP>
sudo nano /etc/supervisor/conf.d/laravel-worker-staging.conf
# paste contents with placeholders replaced

sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker-staging:*
sudo supervisorctl status
```

Repeat with `laravel-worker-production.conf` on the production server (`laravel-worker-production:*`).

---

## After every deploy

Workers keep old code in memory until restarted:

```bash
cd /home/master/applications/<APP_FOLDER>/public_html
php artisan queue:restart
```

---

## Useful commands

```bash
sudo supervisorctl status
sudo supervisorctl restart laravel-worker-staging:*
sudo supervisorctl tail -f laravel-worker-staging:laravel-worker-staging_00 stdout
```

Logs: app `storage/logs/worker.log`, Laravel `storage/logs/laravel.log`, Supervisor `/var/log/supervisor/supervisord.log`.

---

## Cloudways UI (optional)

If **Application Management → Supervisor Queue** is available, you can register the same command without editing files:

`queue:work redis --sleep=3 --tries=3 --max-time=3600 --backoff=5 --timeout=60 --queue=default`

Enable auto-start and auto-restart; set process count to 1 (staging) or 2+ (production) as needed.
