<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ใบเสร็จรับเงิน - {{ $booking->job_number }}</title>
    
    {{-- Google Fonts: Sarabun --}}
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;700&display=swap" rel="stylesheet">

    <style>
        @page { size: A4; margin: 1.5cm; }
        body { font-family: 'Sarabun', sans-serif; font-size: 14pt; line-height: 1.4; color: #000; background: #fff; }
        .container { width: 100%; max-width: 800px; margin: 0 auto; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .header-table { width: 100%; margin-bottom: 20px; }
        .company-name { font-size: 22pt; font-weight: bold; color: #1B4D3E; } 
        .doc-title-box { border: 2px solid #000; padding: 10px 20px; display: inline-block; text-align: center; }
        .doc-title { font-size: 18pt; font-weight: bold; margin: 0; }
        .doc-subtitle { font-size: 10pt; font-weight: normal; margin: 0; text-transform: uppercase; }
        .info-box { border: 1px solid #000; border-radius: 5px; padding: 15px; margin-bottom: 20px; }
        .info-table { width: 100%; }
        .info-table td { vertical-align: top; padding: 2px 0; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .items-table th { background-color: #f0f0f0; border: 1px solid #000; padding: 10px; font-weight: bold; }
        .items-table td { border-left: 1px solid #000; border-right: 1px solid #000; padding: 8px 10px; vertical-align: top; }
        .items-table tr.last-item td { border-bottom: 1px solid #000; }
        .summary-table { width: 100%; border-collapse: collapse; }
        .summary-table td { padding: 5px 10px; }
        .grand-total-row { background-color: #e0e0e0; font-weight: bold; font-size: 16pt; border-top: 2px solid #000; border-bottom: 2px double #000; }
        .signature-section { margin-top: 60px; width: 100%; }
        .sign-box { text-align: center; vertical-align: top; }
        .sign-line { border-bottom: 1px dotted #000; display: inline-block; width: 80%; height: 30px; margin-bottom: 5px; }
        .stamp-paid { position: absolute; right: 120px; top: 220px; border: 4px double #d32f2f; color: #d32f2f; font-size: 28pt; font-weight: bold; padding: 10px 20px; transform: rotate(-15deg); opacity: 0.8; border-radius: 10px; z-index: -1; }
        
        @media print {
            .no-print { display: none; }
            body { -webkit-print-color-adjust: exact; }
        }
        .print-btn { position: fixed; top: 20px; right: 20px; background: #1B4D3E; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-family: 'Sarabun', sans-serif; font-weight: bold; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .print-btn:hover { background: #143a2f; }
    </style>
</head>
<body>

    <button onclick="window.print()" class="print-btn no-print">🖨️ พิมพ์ใบเสร็จ</button>

    <div class="container">
        
        @if ($booking->status == 'completed')
            <div class="stamp-paid">PAID / ชำระแล้ว</div>
        @endif

        {{-- Header --}}
        <table class="header-table">
            <tr>
                <td width="60%">
                    <div class="company-name">Agri-Equip Service</div>
                    <div style="font-size: 11pt; color: #555; margin-top: 5px;">
                        123 หมู่ 4 ต.รังสิต อ.ธัญบุรี จ.ปทุมธานี 12110<br>
                        เลขประจำตัวผู้เสียภาษี: 0-1234-xxxxx-xx-x <br>
                        โทรศัพท์: 02-xxx-xxxx | Email: service@agriequip.com
                    </div>
                </td>
                <td width="40%" class="text-right" style="vertical-align: top;">
                    <div class="doc-title-box">
                        <h1 class="doc-title">ใบเสร็จรับเงิน</h1>
                        <p class="doc-subtitle">Receipt / Tax Invoice</p>
                    </div>
                </td>
            </tr>
        </table>

        {{-- Info Box --}}
        <div class="info-box">
            <table class="info-table">
                <tr>
                    <td width="60%" style="border-right: 1px solid #ccc; padding-right: 15px;">
                        <strong>ลูกค้า (Customer):</strong> {{ $booking->customer->name }}<br>
                        <strong>ที่อยู่ (Address):</strong> {{ $booking->customer->address ?? '-' }}<br>
                        <strong>โทรศัพท์ (Tel):</strong> {{ $booking->customer->phone }}
                    </td>
                    <td width="40%" style="padding-left: 15px;">
                        <table width="100%">
                            <tr>
                                <td><strong>เลขที่ (No.):</strong></td>
                                <td class="text-right">{{ $booking->job_number }}</td>
                            </tr>
                            <tr>
                                <td><strong>วันที่ (Date):</strong></td>
                                <td class="text-right">{{ date('d/m/Y', strtotime($booking->created_at)) }}</td>
                            </tr>
                            <tr>
                                <td><strong>ผู้ออกเอกสาร:</strong></td>
                                <td class="text-right">Admin Office</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Items Table --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th width="5%" class="text-center">#</th>
                    <th width="45%">รายละเอียด (Description)</th>
                    <th width="15%" class="text-center">จำนวน (ไร่)</th>
                    <th width="15%" class="text-right">ราคา/ไร่</th>
                    <th width="20%" class="text-right">จำนวนเงิน (บาท)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">1</td>
                    <td>
                        <strong>ค่าบริการเครื่องจักร ({{ $booking->equipment->name }})</strong><br>
                        <span style="font-size: 10pt; color: #555;">
                            - รหัสอ้างอิง: {{ $booking->equipment->equipment_code }}<br>
                            - วันที่เริ่มงาน: {{ date('d/m/Y H:i', strtotime($booking->scheduled_start)) }}<br>
                            - พนักงานขับรถ: {{ $booking->assignedStaff->name ?? '-' }}
                        </span>
                    </td>
                    <td class="text-center">{{ number_format($booking->actual_area ?? $booking->estimated_area, 1) }}</td>
                    <td class="text-right">{{ number_format($booking->price_per_rai_at_booking ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($booking->total_price, 2) }}</td>
                </tr>
                
                {{-- Empty Rows to fill space --}}
                @for ($i = 0; $i < 3; $i++)
                <tr>
                    <td style="padding: 15px;"> </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                @endfor
                
                {{-- Closing Border --}}
                <tr class="last-item"><td colspan="5" style="padding: 0; border-top: 1px solid #000;"></td></tr>
            </tbody>
        </table>

        {{-- Footer Summary --}}
        <table style="width: 100%;">
            <tr>
                <td width="60%" style="vertical-align: top;">
                    <strong>จำนวนเงินตัวอักษร (Amount in words):</strong><br>
                    <div style="background: #f0f0f0; padding: 10px; border: 1px solid #ccc; margin-top: 5px; text-align: center; font-style: italic;">
                        ({{ $baht_text ?? '..........................................................' }})
                    </div>
                    <div style="margin-top: 10px; font-size: 10pt;">
                        <strong>หมายเหตุ:</strong><br>
                        - ใบเสร็จรับเงินนี้จะสมบูรณ์เมื่อบริษัทได้รับเงินเรียบร้อยแล้ว<br>
                        - ขอบคุณที่ใช้บริการ
                    </div>
                </td>
                <td width="40%" style="vertical-align: top; padding-left: 20px;">
                    <table class="summary-table">
                        <tr>
                            <td class="text-right">รวมเป็นเงิน</td>
                            <td class="text-right text-bold">{{ number_format($booking->total_price, 2) }}</td>
                        </tr>
                        @if ($booking->deposit_amount > 0)
                        <tr>
                            <td class="text-right" style="color: #d32f2f;">หักมัดจำ (Deposit)</td>
                            <td class="text-right" style="color: #d32f2f;">-{{ number_format($booking->deposit_amount, 2) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="text-right">ภาษีมูลค่าเพิ่ม 7%</td>
                            <td class="text-right">-</td>
                        </tr>
                        <tr class="grand-total-row">
                            <td class="text-right">ยอดชำระสุทธิ</td>
                            <td class="text-right">{{ number_format(($booking->total_price - $booking->deposit_amount), 2) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        {{-- Signatures --}}
        <table class="signature-section">
            <tr>
                <td class="sign-box" width="33%">
                    <div class="sign-line"></div><br>
                    ผู้รับวางบิล<br>
                    <span style="font-size: 10pt;">วันที่ ____/____/____</span>
                </td>
                <td class="sign-box" width="33%">
                    <div class="sign-line"></div><br>
                    ผู้ตรวจสอบ<br>
                    <span style="font-size: 10pt;">วันที่ ____/____/____</span>
                </td>
                <td class="sign-box" width="33%">
                    <div class="sign-line"></div><br>
                    ผู้รับเงิน / Authorized Signature<br>
                    <span style="font-size: 10pt;">วันที่ {{ date('d/m/Y') }}</span>
                </td>
            </tr>
        </table>

    </div>

</body>
</html>