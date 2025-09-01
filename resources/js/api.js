import axios from 'axios';
const api = axios.create({ baseURL: '/api' });

export default {
    listTickets(params) {
        return api.get('/tickets', { params }).then(r => r.data);
    },

    createTicket(data) {
        console.log("creating");
        return api.post('/tickets', data).then(r => r.data);
    },

    getTicket(id) {
        return api.get('/tickets/${id}').then(r => r.data);
    },

    updateTicket(id, data) {
        return api.patch('/tickets/${id}', data).then(r => r.data);
    },

    classify(id) {
        return api.post('/tickets/${id}/classify').then(r => r.data);
    },

    stats() {
        return api.get('/stats').then(r => r.data);
    },
};