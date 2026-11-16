import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import apiClient from './config/apiClient.js'; // تأكد من صحة المسار النسبي لملف apiClient.js

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
    enabledTransports: ['ws', 'wss'],
    authorizer: (channel) => {
        return {
            authorize: (socketId, callback) => {
                // نستخدم apiClient نفسه لضمان إرسال نفس التوكن والهيدرز المعتمدة في التطبيق
                apiClient.post('/broadcasting/auth', {
                    socket_id: socketId,
                    channel_name: channel.name,
                }, {
                    // إذا كان baseURL في apiClient ينتهي بـ /api، هذا السطر يضمن الوصول لـ /broadcasting/auth في الجذر
                    baseURL: apiClient.defaults.baseURL?.replace(/\/api\/?$/, ''),
                })
                .then((response) => {
                    callback(false, response.data);
                })
                .catch((error) => {
                    callback(true, error);
                });
            },
        };
    },
});