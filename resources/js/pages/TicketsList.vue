<template>
	<section class="ticket-list">
		<div class="ticket-list_controls">
			<input v-model="search" class="ticket-list_search" placeholder="Search..." />
			<select v-model="status" class="select">
				<option value="">All Status</option>
				<option value="open">Open</option>
				<option value="in_progress">In Progress</option>
				<option value="resolved">Resolved</option>
				<option value="closed">Closed</option>
			</select>
			<select v-model="category" class="select">
				<option value="">All Categories</option>
				<option v-for="c in categories" :key="c" :value="c">{{ c }}</option>
			</select>
			<button class="btn" @click="fetchTickets">Apply</button>
			<button class="btn" @click="exportCSV">Export CSV</button>
			<button class="btn btn-primary" @click="showNew=true">New Ticket</button>
		</div>

		<table class="ticket-list_table">
			<thead class="ticket-list_thead">
				<tr>
					<th>Subject</th>
					<th>Status</th>
					<th>Category</th>
					<th>Conf</th>
					<th>Note</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="t in tickets" :key="t.id" class="ticket-list_row">
					<td class="ticket-list_cell">
						<a href="#" @click.prevent="$router.push('/tickets/'+t.id)">{{ t.subject }}</a>
						<span v-if="t.explanation" class="table_icon" :title="t.explanation">ℹ️</span>
					</td>
					<td class="ticket-list_cell"><span class="badge">{{ t.status }}</span></td>
					<td class="ticket-list_cell">
						<select class="select" v-model="t.category" @change="save(t, {category: t.category})">
							<option :value="null">—</option>
							<option v-for="c in categories" :key="c" :value="c">{{ c }}</option>
						</select>
					</td>
					<td class="ticket-list_cell">{{ formatConfidence(t.confidence) }}</td>
					<td class="ticket-list_cell">
						<span v-if="t.note" class="badge">note</span>
					</td>
					<td class="ticket-list_cell">
						<button class="btn" @click="classify(t)" :disabled="t._classifying">{{ t._classifying? 'Classifying…' : 'Classify' }}</button>
					</td>
				</tr>
			</tbody>
		</table>

		<div style="display:flex; gap:8px; margin-top:12px;">
			<button class="btn" @click="prev" :disabled="page<=1">Prev</button>
			<div>Page {{ page }} / {{ pages }}</div>
			<button class="btn" @click="next" :disabled="page>=pages">Next</button>
		</div>

		<div v-if="showNew" class="modal" @click.self="showNew=false">
			<div class="modal_card">
				<h3 style="margin-top:0">New Ticket</h3>
				<div class="form_group">
					<label>Subject</label>
					<input v-model="form.subject" class="input" />
				</div>
				<div class="form_group">
					<label>Body</label>
					<textarea v-model="form.body" class="textarea" />
				</div>
				<div style="display:flex; gap:8px;">
					<button class="btn btn-primary" @click="create">Create</button>
					<button class="btn" @click="showNew=false">Cancel</button>
				</div>
			</div>
		</div>
	</section>
</template>

<script>
	import api from '../api';
	import { toCSV } from '../polyfills';

	export default {
		data(){
			return {
				tickets: [],
				page: 1, pages: 1, per_page: 10,
				search: '', status: '', category: '',
				categories: ['bug','billing','feature_request','account','other'],
				showNew: false,
				form: { subject:'', body:'' },
			};
		},

		mounted(){ this.fetchTickets(); },

		methods: {
			async fetchTickets(){
				const params = {
					page:this.page,
					per_page:this.per_page,
					search:this.search || undefined,
					status:this.status||undefined,
					category:this.category||undefined
				};
				
				const data = await api.listTickets(params);
				this.tickets = data.data;
				this.page = data.current_page;
				this.pages = data.last_page;
				this.per_page = data.per_page;
			},

			next(){ this.page++; this.fetchTickets(); },

			prev(){ this.page--; this.fetchTickets(); },

			async save(t, patch){
				const updated = await api.updateTicket(t.id, patch);
				Object.assign(t, updated);
			},

			async classify(t){
				t._classifying = true;
				try {
					await api.classify(t.id);
				}
				finally {
					t._classifying = false; this.fetchTickets();
				}
			},

			formatConfidence(c){
				return c==null? '—' : Number(c).toFixed(2);
			},

			exportCSV(){
				const rows = this.tickets.map(({id,subject,status,category,confidence,note})=>({id,subject,status,category,confidence,note}));
				const csv = toCSV(rows);
				const blob = new Blob([csv], { type: 'text/csv' });
				const url = URL.createObjectURL(blob);
				const a = document.createElement('a');
				a.href = url; a.download = 'tickets.csv'; a.click(); URL.revokeObjectURL(url);
			},

			async create(){
				if(!this.form.subject || !this.form.body)
					return alert('Subject and Body are required');
				
				await api.createTicket(this.form);
				this.showNew=false; this.form={subject:'',body:''};
				this.page=1; this.fetchTickets();
			}
		}
	};
</script>