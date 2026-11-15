<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تأكيد الطلب</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; text-align: right;">
    <div style="max-width: 600px; margin: auto; background: #ffffff; padding: 25px; border-radius: 8px; border: 1px solid #ddd;">
        <h2 style="color: #2c3e50;">شكراً لطلبك، {{ $order->user->name ?? 'عميلنا العزيز' }}!</h2>
        <p style="font-size: 16px; color: #555;">تم استلام طلبك بنجاح وهو الآن قيد المعالجة.</p>
        
        <div style="background: #f9f9f9; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p><strong>رقم الطلب:</strong> {{ $order->id }}</p>
            <p><strong>الحالة الحالية:</strong> {{ $order->status->value }}</p>
            <p><strong>المبلغ الإجمالي:</strong> ${{ $order->total_price }}</p>
        </div>

        <h3 style="color: #2c3e50;">تفاصيل المنتجات:</h3>
        <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
            <thead>
                <tr style="background-color: #2c3e50; color: #ffffff;">
                    <th style="padding: 10px; border: 1px solid #ddd;">المنتج</th>
                    <th style="padding: 10px; border: 1px solid #ddd;">الكمية</th>
                    <th style="padding: 10px; border: 1px solid #ddd;">السعر</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td style="padding: 10px; border: 1px solid #ddd;">{{ $item->product->name ?? 'منتج' }}</td>
                    <td style="padding: 10px; border: 1px solid #ddd;">{{ $item->quantity }}</td>
                    <td style="padding: 10px; border: 1px solid #ddd;">${{ $item->price }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <p style="margin-top: 25px; font-size: 14px; color: #888;">شكراً لتسوقك معنا!</p>
    </div>
</body>
</html>