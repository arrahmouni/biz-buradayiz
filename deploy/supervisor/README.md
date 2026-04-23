# Laravel Queue Worker (Supervisor) on Cloudways

Supervisor configuration files for running Laravel queue workers against a **Redis** queue (`QUEUE_CONNECTION=redis`) on Cloudways servers.

- `laravel-worker-staging.conf` — single worker process, lower log retention
- `laravel-worker-production.conf` — two worker processes, higher log retention

---

## 1. Prerequisites on Cloudways

1. The server plan must support Supervisor (all standard Cloudways servers do).
2. Redis must be enabled for the application
   - Cloudways Console > Server Management > Packages — ensure Redis is installed.
   - Cloudways Console > Application > Application Settings > Advanced — enable the Redis extension for the app's PHP.
3. The app's `.env` on the server must use Redis for queues:
   ```env
   QUEUE_CONNECTION=redis
   REDIS_CLIENT=phpredis        # or predis (match what's installed)
   REDIS_HOST=127.0.0.1
   REDIS_PASSWORD=null
   REDIS_PORT=6379
   ```

---

## 2. Gather the placeholder values

Each `.conf` file contains three placeholders you must replace:

| Placeholder             | Where to find it                                                                 | Example             |
| ----------------------- | -------------------------------------------------------------------------------- | ------------------- |
| `<STAGING_APP_FOLDER>` / `<PROD_APP_FOLDER>` | Cloudways > Application > **Access Details** > *Application Folder* | `abcdefghij`        |
| `<STAGING_APP_USER>` / `<PROD_APP_USER>`     | Cloudways > Application > **Access Details** > *Username* (Master or App user) | `master_xxxxxxxx`   |
| `<PHP_BINARY>`          | SSH into server and run `which php8.2` (replace version with yours)              | `/usr/bin/php8.2`   |

---

## 3. Install the config on the server

SSH into the Cloudways server as the **master user** (required for `sudo`):

```bash
ssh master_xxxxxxxx@<server-ip>
```

### Staging

```bash
sudo nano /etc/supervisor/conf.d/laravel-worker-staging.conf
# paste the contents of laravel-worker-staging.conf (with placeholders replaced)
```

### Production

```bash
sudo nano /etc/supervisor/conf.d/laravel-worker-production.conf
# paste the contents of laravel-worker-production.conf (with placeholders replaced)
```

### Reload Supervisor

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker-staging:*     # staging server
sudo supervisorctl start laravel-worker-production:*  # production server
```

Check status:

```bash
sudo supervisorctl status
```

You should see something like:

```
laravel-worker-production:laravel-worker-production_00   RUNNING   pid 12345, uptime 0:00:05
laravel-worker-production:laravel-worker-production_01   RUNNING   pid 12346, uptime 0:00:05
```

---

## 4. Restart workers after every deploy

Queue workers hold code in memory, so you must tell them to gracefully restart after a deploy. In your deploy script (or as the last step of your pipeline), run:

```bash
cd /home/master/applications/<APP_FOLDER>/public_html
php artisan queue:restart
```

Supervisor will automatically start fresh worker processes.

---

## 5. Logs & troubleshooting

- Worker output: `storage/logs/worker.log` (in the app's `public_html`)
- Supervisor log: `/var/log/supervisor/supervisord.log`
- Laravel log:  `storage/logs/laravel.log`

Common commands:

```bash
sudo supervisorctl status
sudo supervisorctl restart laravel-worker-staging:*
sudo supervisorctl stop laravel-worker-production:*
sudo supervisorctl tail -f laravel-worker-production:laravel-worker-production_00 stdout
```

---

## 6. Alternative — Cloudways "Supervisor Queue" UI

Newer Cloudways servers expose a **Supervisor Queue** tab under *Application Management*. If available, you can configure the same worker there without SSH:

- **Command**: `queue:work redis --sleep=3 --tries=3 --max-time=3600 --backoff=5 --timeout=60 --queue=default`
- **Processes**: `1` for staging, `2` for production
- Enable **Auto Start** and **Auto Restart**

The `.conf` files in this folder remain the source of truth for reproducibility.
