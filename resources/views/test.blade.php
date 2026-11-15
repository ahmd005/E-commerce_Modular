<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>اختبار Reverb</title>
</head>
<body style="background: #111; color: #fff; padding: 50px; font-family: sans-serif;">
    <h2>🚀 مراقب البث الفوري (Reverb Live)</h2>
    <div id="log" style="background: #222; border: 1px solid #444; padding: 20px; margin-top: 20px; color: #0f0; height: 200px; overflow-y: auto;">
        في انتظار وصول الأحداث...
    </div>

    @vite(['resources/js/app.js'])
    <script type="module">
        setTimeout(() => {
            window.Echo.channel('orders-channel')
                .listen('.order.updated', (e) => {
                    document.getElementById('log').innerHTML += `<p>✅ وصل حدث للأوردر رقم: ${e.order.id} | الحالة: ${e.order.status}</p>`;
                    console.log(e);
                });
            console.log("تم الاتصال بنجاح!");
        }, 500);
    </script>
</body>
</html>