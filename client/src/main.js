import { createApp } from 'vue'
import App from './App.vue'
import Echo from "laravel-echo";
import Pusher from 'pusher-js'

window.Pusher = Pusher

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: '1iropoxbul5jxv8is8cg',
    wsHost: 'localhost',
    wsPort: 8080,
    wssPort: 8080,
    forceTLS: false,
    enabledTransports: ['ws', 'wss'],
});

createApp(App).mount('#app')
