# Security & Operations Runbook

## 1) Immediate Security Actions

1. Rotate exposed secrets now:
- `DB_PASSWORD`
- `LINE_CHANNEL_ACCESS_TOKEN`
- `LINE_ADMIN_USER_ID`

2. Update `.env` on server and clear cache:
```bash
php artisan optimize:clear
```

3. Keep production safe defaults:
- `APP_DEBUG=false`
- `LOG_LEVEL=info` (recommended for production)

## 2) Daily Database Backup

Run backup script manually:
```bash
bash /var/www/project2/scripts/backup_db.sh
```

Store to custom directory:
```bash
bash /var/www/project2/scripts/backup_db.sh /var/backups/project2
```

Suggested cron (02:10 every day):
```cron
10 2 * * * /bin/bash /var/www/project2/scripts/backup_db.sh /var/backups/project2 >> /var/log/project2-backup.log 2>&1
```

Suggested prune cron (02:40 every day, keep 14 days):
```cron
40 2 * * * /bin/bash /var/www/project2/scripts/prune_backups.sh /var/backups/project2 14 >> /var/log/project2-backup-prune.log 2>&1
```

## 3) Smoke Test After Deploy

Run:
```bash
bash /var/www/project2/scripts/smoke_test.sh
```

Or with custom URL:
```bash
bash /var/www/project2/scripts/smoke_test.sh https://your-domain
```

Checks:
- `/health` returns 200
- `/admin/login` returns 200
- protected routes require auth (302/401/403)

## 4) Monitoring Baseline

Monitor at minimum:
1. Health endpoint: `GET /health`
2. Deep health endpoint: `GET /health/deep`
3. App logs: `storage/logs/laravel.log`
4. Disk usage for backups and uploads
5. Queue failures if queue workers are enabled

Health alert script (LINE notify on degraded):
```bash
bash /var/www/project2/scripts/health_notify.sh http://127.0.0.1:8080/health/deep
```

Suggested cron (every 10 minutes):
```cron
*/10 * * * * /bin/bash /var/www/project2/scripts/health_notify.sh http://127.0.0.1:8080/health/deep >> /var/log/project2-health-alert.log 2>&1
```

Notes:
- Uses `.env` values: `LINE_CHANNEL_ACCESS_TOKEN`, `LINE_ADMIN_USER_ID`
- Sends alert only on transition to degraded (prevents duplicate spam)

## 5) Restore Drill (Monthly)

Validate restore process on staging:
```bash
gunzip -c /path/to/backup.sql.gz | mysql -u <user> -p <db_name>
```

Record:
- backup file used
- restore duration
- app verification result

## 6) Deployment Checklist

1. `php artisan migrate --force`
2. `php artisan optimize:clear`
3. `bash scripts/smoke_test.sh`
4. Verify login, dashboard, reports, maintenance close flow

## 7) Recommended Next Hardening

1. Add CI step to run `php artisan test`
2. Move sensitive `.env` values to secret manager
3. Add fail2ban / WAF / rate limiting for auth routes
4. Centralize logs and alerts (error rate, 5xx spike)
