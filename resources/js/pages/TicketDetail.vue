<template>
    <section class="ticket-list">
        <div class="ticket-list_controls">
            <button class="btn" @click="$router.back()">← Back</button>
            <button class="btn" @click="classify" :disabled="loading">{{ loading? 'Classifying…':'Run Classification' }}</button>
        </div>

        <div class="chart-card">
            <h2 style="margin:0 0 8px 0">{{ ticket.subject }}</h2>
            <p style="white-space: pre-wrap;">{{ ticket.body }}</p>
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-top:12px;">
                <div>
                    <label>Status</label>
                    <select class="select" v-model="ticket.status" @change="save({status: ticket.status})">
                        <option value="open">Open</option>
                        <option value="in_progress">In Progress</option>
                        <option value="resolved">Resolved</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
                <div>
                    <label>Category</label>
                    <select class="select" v-model="ticket.category" @change="save({category: ticket.category})">
                        <option :value="null">—</option>
                        <option v-for="c in categories" :key="c" :value="c">{{ c }}</option>
                    </select>
                </div>
            </div>

            <div class="form_group">
                <label>Internal Note</label>
                <textarea class="textarea" v-model="ticket.note" @blur="save({note: ticket.note})"></textarea>
            </div>

            <div style="display:flex; gap:12px;">
                <div>
                    <strong>Confidence:</strong> {{ ticket.confidence==null? '—' : Number(ticket.confidence).toFixed(2) }}
                </div>
                <div>
                    <strong>Explanation:</strong> <span :title="ticket.explanation">{{ shorten(ticket.explanation) }}</span>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    import api from '../api';
    export default {
        data(){
            return { ticket: {}, loading:false, categories:['bug','billing','feature_request','account','other'] };
        },
        
        async mounted(){
            await this.load();
        },

        methods: {
            async load(){
                this.ticket = await api.getTicket(this.$route.params.id);
            },

            async save(patch){
                this.ticket = await api.updateTicket(this.ticket.id, patch);
            },

            async classify(){
                this.loading=true;
                try{
                    await api.classify(this.ticket.id);
                    await this.load();
                }
                finally {
                    this.loading=false;
                }
            },

            shorten(s){
                if(!s)
                    return '—';

                return s.length>80 ? s.slice(0,80)+'…' : s;
            }
        }
    };
</script>
