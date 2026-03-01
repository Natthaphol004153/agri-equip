# System Process Flow (Agri-Equip)

เอกสารนี้สรุปกระบวนการทำงานของระบบจากโค้ดปัจจุบัน (as-is) เพื่อใช้คุยทีม, onboard dev, และตรวจความถูกต้องเชิงธุรกิจ

## 1) ภาพรวมสถาปัตยกรรมการไหลของงาน

- ฝั่ง Web (Blade): ลูกค้า / พนักงาน / แอดมิน ใช้งานผ่าน `routes/web.php`
- ฝั่ง API: Mobile/Integrations ใช้งานผ่าน `routes/api.php`
- Logic กลางสำคัญ: `BookingService`, `MaintenanceService`, `PromptPayService`
- Data หลักที่เชื่อมกัน: `bookings`, `equipment`, `maintenance_logs`, `fuel_logs`, `fuel_tanks`, `customers`, `users`

Flow หลักของระบบ:

1. Login ตามบทบาท
2. สร้างงานจอง (Booking)
3. ตรวจซ้อนคิวรถ/พนักงาน/ซ่อมบำรุง
4. มอบหมายงานและเริ่มงาน
5. ปิดงาน + ชำระเงิน + อนุมัติ
6. อัปเดตสถานะเครื่องจักร/เลขมิเตอร์/ต้นทุน

---

## 2) Authentication & Authorization

### 2.1 Admin/Staff (Web)

- Route: `/admin/login` (`login`) และ `/staff/login`
- Controller: `Web\AuthController`, `Web\StaffLoginController`
- กลไก:
  - Admin/Staff login ผ่านตาราง `users`
  - Staff login แบบ PIN (hash check)
  - Login สำเร็จ redirect ตาม role (`admin.dashboard` หรือ `staff.dashboard`)

### 2.2 Customer (Web)

- Route: `/customer/login`
- Guard แยก: `customer`
- Controller: `Web\CustomerAuthController`
- กลไก:
  - Login ด้วย `phone + password`
  - ใช้ session guard แยกจาก admin/staff

### 2.3 API Auth

- Route: `POST /api/login`
- Controller: `Api\AuthController`
- Response หลัก (หลังปรับให้ผ่านเทสต์): ส่ง `access_token`, `token`, `user`, `role`

---

## 3) Booking Lifecycle (หัวใจระบบ)

## สถานะ Booking ที่ใช้งานจริง

- `scheduled` = งานถูกนัดหมายแล้ว
- `in_progress` = พนักงานเริ่มงานแล้ว
- `completed_pending_approval` = พนักงานส่งงาน รอแอดมินตรวจ
- `completed` = แอดมินอนุมัติจบงาน
- `cancelled` = ยกเลิก
- มี enum เพิ่มเติม (`paused`, `closed`) แต่ flow หลักยังไม่ได้ใช้หนัก

## สถานะ Equipment ที่ใช้งานจริง

- `available` = พร้อมรับงาน
- `in_use` = กำลังใช้งาน
- `maintenance` = เข้าซ่อมตามแผน
- `breakdown` = เสียฉุกเฉิน
- `booked` มีใน enum แต่ flow ปัจจุบันใช้ไม่มาก

### 3.1 การจองโดยลูกค้า (Web)

Controller: `Web\Customer\DashboardController@store`

ลำดับทำงาน:

1. Validate `equipment_id`, `start_date`, `start_time`, `end_time`
2. รวมวัน+เวลาเป็น `scheduled_start`/`scheduled_end`
3. เรียก `BookingService::createBooking()` (ไม่เขียนลง DB ตรง)
4. Service ตรวจความพร้อมรถ/พนักงาน/งานซ้อน/ซ่อม
5. Service generate `job_number` และตั้งค่าเริ่มต้น
6. บันทึก booking พร้อมสถานะเริ่มต้นที่ฝั่งลูกค้าส่งมา (`pending_approval`)

หมายเหตุ: เทสต์ลูกค้าตรวจเส้นทางชื่อ `customer.bookings.store` ซึ่งระบบรองรับแล้วผ่าน alias route

### 3.2 การสร้างงานโดยแอดมิน (Web)

Controller: `Web\JobController@store`

ลำดับทำงาน:

1. Validate ข้อมูลหลัก + พื้นที่ (`actual_area`)
2. ดึง `price_per_rai` จากเครื่องจักร
3. คำนวณ `total_price` ฝั่ง server
4. ส่งข้อมูลเข้า `BookingService::createBooking()`
5. กำหนด `payment_status` ตามเงินมัดจำ

### 3.3 การสร้างงานผ่าน API

Controller: `BookingController@store`

ลำดับทำงาน:

1. Validate ลูกค้า/เครื่องจักร/เวลานัดหมาย/พื้นที่
2. คำนวณราคาใน backend
3. เรียก `BookingService` เพื่อทำธุรกรรมจริง
4. Response JSON พร้อมข้อมูล booking

---

## 4) BookingService (ศูนย์กลางกติกาคิว)

ไฟล์: `app/Services/BookingService.php`

### 4.1 createBooking

- ทำงานใน DB transaction
- ดึงข้อมูลเครื่องจักร และคำนวณราคาแบบพื้นที่ (`area * price_per_rai`)
- snapshot ราคาต่อไร่ตอนจอง (`price_per_rai_at_booking`)
- ตั้งค่า default status/payment/deposit เมื่อไม่ส่งมา
- generate เลขงาน `JOB-YYYYMMDD-XXX` แบบ lock ป้องกันชนเลข

### 4.2 checkEquipmentAvailability

ตรวจ 3 ชั้น:

1. สถานะเครื่องจักรห้ามใช้งาน (`out_of_service`, `retired`, `inactive`)
2. มี booking ซ้อนช่วงเวลาไหม (ยกเว้น `cancelled`, `completed`)
3. มี maintenance ที่ทับเวลาหรือยังไม่เสร็จไหม

### 4.3 checkStaffAvailability

ตรวจ 2 ชั้น:

1. พนักงานมีงานซ้อนช่วงเวลาไหม
2. พนักงานลาที่อนุมัติแล้วทับช่วงไหม

---

## 5) ฝั่งพนักงาน (Execution Flow)

Controller หลัก: `Web\StaffJobController`, `Api\StaffController`

### 5.1 เริ่มงาน

- เปลี่ยน booking -> `in_progress`
- บันทึก `actual_start`
- เก็บ `meter_before_start` จากค่าปัจจุบันของเครื่องจักร

### 5.2 จบงาน

- Validate รูปหลักฐาน + meter + ข้อมูลชำระเงิน (กรณีค้างยอด)
- ตรวจ `meter_reading >= meter_before_start`
- เปลี่ยน booking -> `completed_pending_approval`
- เก็บรูปหน้างาน/หมายเหตุ/เวลาเสร็จ
- (API บางจุด) คืนสถานะเครื่องจักรเป็น `available` หลังจบงาน

### 5.3 งานรอแอดมิน

- แอดมินเข้าหน้าตรวจงาน (`Web\JobController@review`)
- อนุมัติผ่าน `approve`:
  - booking -> `completed`
  - payment_status -> `paid`
  - อัปเดตเลขชั่วโมงหรือกิโลเมตรของเครื่องจักรจาก meter ล่าสุด

---

## 6) Payment Flow

### 6.1 PromptPay QR

- Service: `PromptPayService::generatePayload()`
- ใช้เบอร์/เลขบัตรสร้าง payload ตามมาตรฐาน Thai QR
- ฝั่งลูกค้าสร้างลิงก์ QR เพื่อจ่ายยอดที่ต้องชำระ

### 6.2 หลักฐานการโอน

- ลูกค้า upload slip -> booking อัปเดต `payment_proof`, `payment_status = pending_approval`
- เมื่อแอดมินอนุมัติงานหรือยืนยันครบเงื่อนไข -> `payment_status = paid`

---

## 7) Maintenance Flow

Controller: `Web\MaintenanceController`, `Api\MaintenanceController`

### 7.1 เปิดงานซ่อม

- สร้าง `maintenance_log`
- เปลี่ยนสถานะเครื่องจักร -> `maintenance` หรือ `breakdown`
- รถที่ซ่อมอยู่จะถูกบล็อกจากการจองใหม่ผ่าน `BookingService`

### 7.2 ปิดงานซ่อม

- บันทึกต้นทุนซ่อม, ผู้ให้บริการ, เวลาเสร็จ, optional reset counter
- ปลดล็อกรถกลับ `available`
- หาก reset counter: ตั้งค่าชั่วโมงเริ่มใหม่ตามเงื่อนไข

---

## 8) Fuel & Cost Flow

Controller: `Web\FuelController`, `Web\FuelStockController`

### 8.1 เติมน้ำมันจากถังบริษัท (internal)

- lock แถวถังน้ำมัน (`lockForUpdate`)
- ตรวจยอดคงเหลือพอจ่าย
- คิดต้นทุนจาก `average_price`
- ตัด stock และบันทึก `fuel_logs`

### 8.2 ซื้อน้ำมันเข้าถัง (stock-in)

- บันทึก `fuel_purchases`
- คำนวณต้นทุนเฉลี่ยใหม่แบบ Weighted Average:

  - มูลค่าเดิม = `current_balance * average_price`
  - มูลค่าใหม่ = `liters * price_per_liter`
  - ราคาเฉลี่ยใหม่ = `(มูลค่าเดิม + มูลค่าใหม่) / (ลิตรเดิม + ลิตรใหม่)`

### 8.3 เติมจากปั๊มนอก (external)

- ไม่ตัด stock บริษัท
- บันทึกค่าใช้จ่ายจริงและหลักฐานใบเสร็จ

---

## 9) Approval & Closing Loop (ภาพรวมจบงาน)

1. สร้าง booking (customer/admin/api)
2. ผ่านกติกาคิวจาก `BookingService`
3. พนักงานเริ่มงาน (`in_progress`)
4. พนักงานส่งงาน (`completed_pending_approval`)
5. แอดมินตรวจและอนุมัติ (`completed`)
6. อัปเดตมิเตอร์เครื่องจักร + ปิดการชำระเงินเป็น `paid`
7. งานเข้าสู่ประวัติ/รายงาน

---

## 10) จุดแข็งเชิง Logic ปัจจุบัน

- ใช้ service layer สำหรับ booking ช่วยรวมกติกาธุรกิจไว้จุดเดียว
- มี transaction ในจุดสำคัญ (booking, fuel)
- ป้องกันงานซ้อนทั้งรถและพนักงาน
- แยก guard ลูกค้าออกจาก user ภายใน ลดความเสี่ยง role ปนกัน

---

## 11) จุดที่ควรระวัง/ตรวจต่อ (As-Is Gaps)

1. มีการใช้คำสถานะบางจุดไม่สม่ำเสมอระหว่าง enum กับ string literal หลายไฟล์
2. ฝั่ง API บาง route ยังไม่บังคับ auth middleware ทุก endpoint
3. flow ชำระเงินมีหลายทาง (ลูกค้า/พนักงาน/แอดมิน) ควรล็อกกติกา source-of-truth ให้ชัด
4. route naming เคยมี singular/plural ไม่ตรงเทสต์ (แก้แล้ว) ควรกำหนดมาตรฐานเดียว
5. บางจุดมี logic ซ้ำระหว่าง API และ Web controller อาจพิจารณาย้ายเข้าบริการกลางเพิ่ม

---

## 12) ข้อเสนอรูปแบบเอกสารต่อเนื่อง

ถ้าจะต่อยอดเอกสารนี้ให้นำไปใช้กับทีมจริง แนะนำเพิ่ม:

- State transition diagram ของ `booking` และ `equipment`
- ตารางสิทธิ์แต่ละ role ต่อ action
- API contract ฉบับ stable (request/response/error)
- business rule matrix (กรณีขอบ เช่น งานซ้อน, meter ผิด, จ่ายบางส่วน)
