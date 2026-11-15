<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تحديث حالة الطلب</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; text-align: right;">
    <div style="max-width: 600px; margin: auto; background: #ffffff; padding: 25px; border-radius: 8px; border: 1px solid #ddd;">
        <h2 style="color: #2c3e50;">مرحباً {{ $order->user->name ?? 'عميلنا العزيز' }}،</h2>
        <p style="font-size: 16px; color: #555;">نود إعلامك بأنه تم تحديث حالة طلبك.</p>
        
        <div style="background: #eef7ff; padding: 15px; border-right: 4px solid #3498db; margin: 20px 0;">
            <p><strong>رقم الطلب:</strong> {{ $order->id }}</p>
            <p><strong>الحالة الجديدة:</strong> <span style="color: #2980b9; font-weight: bold;">{{ $newStatus }}</span></p>
            <p><strong>المبلغ الإجمالي:</strong> ${{ $order->total_price }}</p>
        </div>

        <p style="margin-top: 25px; font-size: 14px; color: #888;">إذا كان لديك أي استفسار، لا تتردد بالتواصل معنا.</p>
    </div>
</body>
</html>