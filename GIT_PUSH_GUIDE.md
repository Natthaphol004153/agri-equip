# คู่มือการเอาไฟล์ขึ้น Git สำหรับโปรเจกต์ Laravel

ไฟล์นี้อธิบายว่าโฟลเดอร์ไหนควรเอาขึ้น (Push) ไปที่ Git และโฟลเดอร์ไหนควรบล็อคพ (Ignore) ไม่ให้ขึ้นไปรกบน Repository

## 🟢 ส่วนที่ "ต้องเอาขึ้น" Git (ไฟล์ที่เราเขียนเอง)
นี่คือส่วนที่เราทำงานและแก้ไขเป็นประจำ:
- `app/` (Models, Controllers, Middleware, Services ฯลฯ)
- `routes/` (`web.php`, `api.php`, ฯลฯ)
- `database/` (Migrations, Seeders, Factories)
- `resources/` (ไฟล์ `.blade.php`, โค้ด CSS/JS ดิบก่อนคอมไพล์)
- `config/` (ไฟล์ตั้งค่าในโปรเจกต์)
- `composer.json` และ `composer.lock` (รายชื่อ package PHP ที่ต้องใช้)
- `package.json` และ `package-lock.json` (รายชื่อ package Node.js)
- `tests/` (ไฟล์ทดสอบระบบ)

## 🔴 ส่วนที่ "ต้องบล็อค" Git (ไฟล์ที่ระบบสร้างและไฟล์ความลับ)
ส่วนนี้ปกติจะต้องอยู่ในไฟล์ `.gitignore` เพื่อป้องกันไม่ให้เผลอเอาขึ้น Git:
- `vendor/` ❌ (โฟลเดอร์เก็บ Library ของ PHP ที่ใหญ่มาก - ให้รัน `composer install` ใหม่เอาเอง)
- `node_modules/` ❌ (โฟลเดอร์เก็บ Library ของ JS/CSS - ให้รัน `npm install` ใหม่เอาเอง)
- `.env` ❌ (ไฟล์เก็บรหัสผ่าน Database และ Secret ต่างๆ **ห้ามเอาขึ้นเด็ดขาด!**)
- โฟลเดอร์ `storage/` ❌ (พวก logs, รูปภาพที่อัปโหลด, หรือ cache ต่างๆ)
- `public/build/` หรือ `public/hot/` ❌ (ไฟล์ asset ที่คอมไพล์แล้ว)
- `*.log` ❌ (ไฟล์เก็บประวัติ Error ต่างๆ)

---

## 🔧 วิธีแก้ปัญหาถ้าเผลอเอาโฟลเดอร์ที่ไม่ควรเอาขึ้นไปแล้ว
หากคุณมีไฟล์ `vendor/` หรือ `node_modules/` ติดขึ้นไปบน Git แล้ว ให้ใช้คำสั่งเหล่านี้ใน Terminal เพื่อลบออกจาก Git (ไฟล์ในเครื่องยังอยู่ปกติ):

```bash
# 1. เอาออกจากประวัติ Git
git rm -r --cached vendor
git rm -r --cached node_modules
git rm --cached .env

# 2. บันทึกการเปลี่ยนแปลง
git add .
git commit -m "Remove vendor, node_modules, and .env from git tracking"

# 3. ดันขึ้นไปเพื่อลบบน remote
git push
```
