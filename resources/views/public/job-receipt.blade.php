<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ใบเสร็จและหลักฐานงาน #{{ $booking->job_number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        :root {
            --bg: #f4f8f5;
            --card: #ffffff;
            --ink: #1b2b23;
            --muted: #60706a;
            --brand: #156b45;
            --brand-2: #dff4ea;
            --line: #dbe7e1;
            --warn: #d88a00;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Kanit', sans-serif;
            background:
                radial-gradient(circle at 10% 0%, #e8f6ee 0%, transparent 35%),
                radial-gradient(circle at 90% 10%, #f4efe2 0%, transparent 32%),
                var(--bg);
            color: var(--ink);
            line-height: 1.6;
        }

        .wrap {
            width: min(980px, 92vw);
            margin: 32px auto 40px;
        }

        .hero {
            background: linear-gradient(120deg, #14563b 0%, #1f7a52 100%);
            color: #fff;
            border-radius: 22px;
            padding: 24px;
            box-shadow: 0 14px 30px rgba(16, 58, 40, 0.25);
            margin-bottom: 18px;
            animation: reveal 380ms ease-out;
        }

        .hero h1 {
            margin: 0;
            font-size: clamp(24px, 3.4vw, 34px);
            line-height: 1.2;
        }

        .hero h1 i {
            margin-right: 8px;
        }

        .hero p {
            margin: 6px 0 0;
            opacity: 0.92;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            padding: 7px 12px;
            border-radius: 999px;
            margin-top: 14px;
            background: rgba(255, 255, 255, 0.16);
            border: 1px solid rgba(255, 255, 255, 0.36);
        }

        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 18px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.04);
            animation: reveal 460ms ease-out;
        }

        .card + .card {
            margin-top: 14px;
        }

        .title {
            margin: 0 0 12px;
            font-size: 20px;
            color: #174d35;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .title i {
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: #e8f6ee;
            color: #156b45;
            font-size: 14px;
        }

        .grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 14px;
        }

        .kv {
            display: grid;
            grid-template-columns: 160px 1fr;
            gap: 6px 10px;
            font-size: 15px;
        }

        .k {
            color: var(--muted);
            font-weight: 500;
        }

        .money {
            display: grid;
            gap: 10px;
            background: var(--brand-2);
            border: 1px solid #caeada;
            border-radius: 14px;
            padding: 14px;
        }

        .money .row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 15px;
        }

        .money .row strong {
            font-size: 16px;
        }

        .money .net {
            border-top: 1px dashed #9bcfb8;
            padding-top: 10px;
            margin-top: 2px;
            font-size: 20px;
            font-weight: 700;
            color: var(--brand);
        }

        .proof-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .proof-grid-top {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            margin-top: 6px;
        }

        .proof-item {
            border: 1px solid var(--line);
            border-radius: 14px;
            overflow: hidden;
            background: #fcfefd;
        }

        .proof-item .label {
            font-size: 14px;
            padding: 10px 12px;
            border-bottom: 1px solid var(--line);
            color: #2f3a36;
            background: #f2f7f4;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .proof-item .hint {
            font-size: 12px;
            color: #61716b;
            padding: 0 12px 10px;
            border-bottom: 1px solid var(--line);
            background: #f2f7f4;
        }

        .proof-item .body {
            padding: 12px;
        }

        .proof-item img {
            width: 100%;
            height: min(62vw, 460px);
            object-fit: contain;
            background: #fff;
            border-radius: 10px;
            border: 1px solid #e2ebe6;
            display: block;
        }

        .proof-item a {
            display: inline-flex;
            margin-top: 10px;
            font-size: 14px;
            font-weight: 600;
            color: var(--brand);
            text-decoration: none;
        }

        .proof-item a:hover {
            text-decoration: underline;
        }

        .open-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
            padding: 8px 12px;
            border-radius: 10px;
            background: #e8f6ee;
            border: 1px solid #c8e6d7;
            color: #0f5b3a;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
        }

        .open-btn:hover {
            background: #d8f0e4;
        }

        .empty {
            color: #6a756f;
            font-size: 14px;
            padding: 14px;
            border: 1px dashed #cfded7;
            border-radius: 10px;
            background: #f8fbfa;
        }

        .warn {
            margin-top: 8px;
            color: #7b5709;
            background: #fff7e8;
            border: 1px solid #f0d9a9;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 13px;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        .k i {
            width: 16px;
            margin-right: 6px;
            color: #4e6960;
        }

        .foot {
            margin-top: 12px;
            font-size: 12px;
            color: #6a7771;
            text-align: center;
        }

        @keyframes reveal {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 860px) {
            .grid {
                grid-template-columns: 1fr;
            }

            .proof-grid {
                grid-template-columns: 1fr;
            }

            .proof-grid-top {
                grid-template-columns: 1fr;
            }

            .kv {
                grid-template-columns: 1fr;
                gap: 2px;
            }
        }
    </style>
</head>

<body>
    <div class="wrap">
        <section class="hero">
            <h1><i class="fa-solid fa-file-invoice"></i>ใบเสร็จและหลักฐานจบงาน</h1>
            <p>งานเลขที่ #{{ $booking->job_number }}</p>
            <div class="badge">
                <i class="fa-solid fa-shield-heart"></i>
                สถานะ:
                @if ($booking->status === 'completed')
                    ชำระสมบูรณ์
                @else
                    รอตรวจสอบโดยแอดมิน
                @endif
            </div>
        </section>

        <section class="card">
            <h2 class="title"><i class="fa-solid fa-images"></i>รูปหลักฐานจบงาน</h2>
            <div class="proof-grid-top">
                <article class="proof-item">
                    <div class="label"><i class="fa-solid fa-camera-retro"></i>ภาพหน้างานหลังจบงาน</div>
                    <div class="hint">รูปจากพนักงานตอนปิดงาน</div>
                    <div class="body">
                        @if ($booking->image_path)
                            <img src="{{ asset('storage/' . $booking->image_path) }}" alt="หลักฐานจบงาน">
                            <a class="open-btn" href="{{ asset('storage/' . $booking->image_path) }}" target="_blank" rel="noopener"><i class="fa-solid fa-up-right-from-square"></i>เปิดภาพเต็ม</a>
                        @else
                            <div class="empty">ยังไม่มีการแนบภาพหลักฐานจบงาน</div>
                        @endif
                    </div>
                </article>

                <article class="proof-item">
                    <div class="label"><i class="fa-solid fa-receipt"></i>หลักฐานการชำระเงิน</div>
                    <div class="hint">สลิปหรือภาพหลักฐานการจ่ายเงิน</div>
                    <div class="body">
                        @if ($booking->payment_proof)
                            <img src="{{ asset('storage/' . $booking->payment_proof) }}" alt="หลักฐานการชำระเงิน">
                            <a class="open-btn" href="{{ asset('storage/' . $booking->payment_proof) }}" target="_blank" rel="noopener"><i class="fa-solid fa-up-right-from-square"></i>เปิดภาพเต็ม</a>
                        @else
                            <div class="empty">ไม่มีไฟล์หลักฐานการชำระเงิน</div>
                        @endif
                    </div>
                </article>
            </div>
        </section>

        <section class="card">
            <h2 class="title"><i class="fa-solid fa-list-check"></i>ข้อมูลรายการ</h2>
            <div class="grid">
                <div class="kv">
                    <div class="k"><i class="fa-solid fa-user"></i>ชื่อลูกค้า</div>
                    <div>{{ $booking->customer->name ?? '-' }}</div>

                    <div class="k"><i class="fa-solid fa-hashtag"></i>หมายเลขงาน</div>
                    <div>{{ $booking->job_number ?? '-' }}</div>

                    <div class="k"><i class="fa-solid fa-tractor"></i>เครื่องจักร</div>
                    <div>{{ $booking->equipment->name ?? '-' }}</div>

                    <div class="k"><i class="fa-solid fa-user-gear"></i>พนักงานผู้รับผิดชอบ</div>
                    <div>{{ $booking->assignedStaff->name ?? '-' }}</div>

                    <div class="k"><i class="fa-solid fa-hourglass-start"></i>วันเริ่มงาน</div>
                    <div>{{ $booking->actual_start ? \Carbon\Carbon::parse($booking->actual_start)->format('d/m/Y H:i') : '-' }}</div>

                    <div class="k"><i class="fa-solid fa-flag-checkered"></i>วันจบงาน</div>
                    <div>{{ $booking->actual_end ? \Carbon\Carbon::parse($booking->actual_end)->format('d/m/Y H:i') : '-' }}</div>

                    <div class="k"><i class="fa-solid fa-map-location-dot"></i>พื้นที่ปฏิบัติงานจริง</div>
                    <div>{{ number_format((float) ($booking->actual_area ?? 0), 1) }} ไร่</div>
                </div>

                <div class="money">
                    <div class="row"><span><i class="fa-solid fa-sack-dollar"></i> ยอดงานรวม</span><strong>{{ number_format((float) $booking->total_price, 2) }} บาท</strong></div>
                    <div class="row"><span><i class="fa-solid fa-hand-holding-dollar"></i> มัดจำ</span><strong>{{ number_format((float) $booking->deposit_amount, 2) }} บาท</strong></div>
                    <div class="row net"><span><i class="fa-solid fa-coins"></i> ยอดสุทธิ</span><span>{{ number_format((float) $net_total, 2) }} บาท</span></div>
                    <div class="row"><span><i class="fa-solid fa-wallet"></i> วิธีชำระ</span><strong>{{ $booking->payment_method ?: '-' }}</strong></div>
                </div>
            </div>
        </section>

        <section class="card">
            <h2 class="title"><i class="fa-solid fa-link"></i>ข้อมูลลิงก์</h2>
            <div class="warn">
                <i class="fa-solid fa-triangle-exclamation"></i>
                ลิงก์หน้านี้เป็นลิงก์ชั่วคราวเพื่อความปลอดภัย หากหมดอายุให้ขอลิงก์ใหม่จากระบบ
            </div>
        </section>

        <div class="foot">Agri Equip Service • ออกเมื่อ {{ now()->format('d/m/Y H:i') }}</div>
    </div>
</body>

</html>
