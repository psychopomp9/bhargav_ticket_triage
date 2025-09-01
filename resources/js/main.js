import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import TicketsList from './pages/TicketsList.vue';
import TicketDetail from './pages/TicketDetail.vue';
import Dashboard from './pages/Dashboard.vue';
import './polyfills';

const routes = [
    { path: '/', redirect: '/tickets' },
    { path: '/tickets', component: TicketsList },
    { path: '/tickets/:id', component: TicketDetail },
    { path: '/dashboard', component: Dashboard },
];

const router = createRouter({ history: createWebHistory(), routes });

const App = {
    template: `
        <div class="app">
            <nav class="nav">
                <a class="nav_link" href="/tickets" @click.prevent="$router.push('/tickets')">Tickets</a>
                <a class="nav_link" href="/dashboard" @click.prevent="$router.push('/dashboard')">Dashboard</a>
                <button class="nav_link toggle" @click="toggleTheme">Toggle Theme</button>
            </nav>
            <router-view />
        </div>`,
    methods: {
        toggleTheme() {
            const html = document.documentElement;
            html.classList.toggle('light');
        }
    }
};

createApp(App).use(router).mount('#app');