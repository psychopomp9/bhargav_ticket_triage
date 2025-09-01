<template>
    <section>
        <div class="ticket-list_controls">
            <button class="btn" @click="load">Refresh</button>
        </div>

        <div class="chart-card">
            <h3 style="margin-top:0">Counters</h3>
            <div style="display:flex; gap:12px; flex-wrap:wrap;">
                <div class="badge" v-for="(v,k) in stats.perStatus" :key="k">{{ k }}: {{ v }}</div>
                <div class="badge">Total: {{ stats.total }}</div>
            </div>
        </div>

        <div class="chart-card">
            <h3 style="margin-top:0">Per Category</h3>
            <canvas id="catChart"></canvas>
        </div>
    </section>
</template>

<script>
    import api from '../api';
    import { Chart, ArcElement, BarElement, BarController, CategoryScale, LinearScale, Tooltip, Legend } from 'chart.js';
    Chart.register(ArcElement, BarElement, BarController, CategoryScale, LinearScale, Tooltip, Legend);

    export default {
        data(){
            return { stats: { perStatus:{}, perCategory:{} } }; 
        },

        mounted(){
            this.load();
        },

        methods:{
            async load(){
                this.stats = await api.stats();
                this.$nextTick(()=>this.renderChart());
            },
            renderChart(){
                const el = document.getElementById('catChart');
                if(!el) 
                    return;
                if(this._chart) {
                    this._chart.destroy();
                }

                const labels = Object.keys(this.stats.perCategory);
                const data = Object.values(this.stats.perCategory);
                this._chart = new Chart(el.getContext('2d'), {
                    type: 'bar',
                    data: { 
                        labels,
                        datasets: [{ label: 'Tickets', data }] 
                    },
                    options: { 
                        responsive: true,
                        plugins: {
                            legend: { display: false } 
                        } 
                    }
                });
            }
        }
    };
</script>
