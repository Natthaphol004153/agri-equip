# Deploy Precheck (Production)

ใช้รายการนี้ก่อน deploy ทุกครั้ง เพื่อให้ระบบปลอดภัยและลดโอกาสพังหน้างาน

## 1) Environment

- ตั้งค่า `.env` ให้ถูกต้อง
  - `APP_ENV=production`
  - `APP_DEBUG=false`
  - `APP_URL` เป็นโดเมนจริง
  - ค่าฐานข้อมูล/เมล/queue/storage ครบ
- ยืนยันว่า `APP_KEY` ถูกตั้งแล้ว

## 2) Build & Test

1. ติดตั้ง dependency
   - `composer install --no-dev --optimize-autoloader`
   - `npm ci`
2. Build frontend
   - `npm run build`
3. รันเทสต์
   - `php artisan test`

## 3) Database & Cache

1. สำรองฐานข้อมูลก่อน
2. รัน migration
   - `php artisan migrate --force`
3. ออปติไมซ์แคช
   - `php artisan config:cache`
   - `php artisan route:cache`
   - `php artisan view:cache`

## 4) Runtime Services

- ตั้ง queue worker (Supervisor / system service)
  - `php artisan queue:work --tries=3 --timeout=120`
- ตั้ง scheduler ที่เครื่อง deploy
  - `* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1`

## 5) Security Checks

- ใช้ HTTPS ที่ reverse proxy/web server และตั้ง `FORCE_HTTPS=true` เมื่อปลายทางเป็น HTTPS จริง
- ตรวจ cookie/session เป็น secure mode
- ตรวจ API sensitive routes ว่าใช้ token + role middleware ใน production
- ทดสอบ login และ role access จริง 3 บทบาท (admin/staff/customer)

## 6) Post-Deploy Smoke Test

- เปิดหน้า login ได้
- ลูกค้าสร้าง booking ได้
- staff เริ่มงาน/จบงานได้
- admin อนุมัติงานได้
- upload สลิป/รูปหลักฐานได้
- dashboard ไม่ error

## 7) Rollback Plan

- มี backup DB ก่อน deploy ทุกครั้ง
- หาก fail หลัง deploy:
  1. rollback release
  2. restore DB ตามจุด backup
  3. clear/rebuild cache
     - `php artisan optimize:clear`
     - `php artisan config:cache`
