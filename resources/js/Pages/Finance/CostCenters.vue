<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { Check, Target } from 'lucide-vue-next';
import AppLayout from '../../Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
defineProps({ centers: Array, summary: Object });
const form = useForm({ name: '', code: '', budget: '' });
const submit = () => form.post('/cost-centres', { onSuccess: () => form.reset() });
</script>
<template>
    <Head title="Cost centres" />
    <div class="page-heading"><div><p class="eyebrow"><span class="dot" /> Planning control</p><h1>Give every spend a home.</h1><p>Budgets become more useful when branches and teams can see what they own.</p></div></div>
    <div class="metric-strip"><div class="mini-metric"><span>Cost centres</span><strong>{{ summary.centers }}</strong><em>Active planning buckets</em></div><div class="mini-metric"><span>Budget</span><strong>{{ summary.budget }}</strong><em>Allocated across centres</em></div><div class="mini-metric"><span>Spent</span><strong>{{ summary.spent }}</strong><em>Recorded to date</em></div></div>
    <div class="dashboard-grid"><section class="card"><div class="card-header"><div><h2>Cost centre register</h2><p>Budget against recorded spend</p></div></div><div class="table-scroll"><table class="data-table"><thead><tr><th>Centre</th><th>Budget</th><th>Spent</th><th>Remaining</th><th>Status</th></tr></thead><tbody><tr v-for="center in centers" :key="center.code"><td><div class="data-title">{{ center.name }}</div><div class="data-subtitle">{{ center.code }}</div></td><td>{{ center.budget }}</td><td>{{ center.spent }}</td><td><strong>{{ center.remaining }}</strong></td><td><span class="status-pill">{{ center.status }}</span></td></tr></tbody></table></div></section><section class="card form-card"><div class="card-header" style="padding:0 0 18px"><div><h2>New cost centre</h2><p>Set a budget boundary for a team or initiative.</p></div><div class="kpi-icon violet"><Target :size="17" /></div></div><form class="form-grid" @submit.prevent="submit"><div class="field full"><label>Name</label><input v-model="form.name" placeholder="Branch marketing" /></div><div class="field"><label>Code</label><input v-model="form.code" placeholder="CC-MKT-01" /></div><div class="field"><label>Budget</label><input v-model="form.budget" type="number" min="0" step="0.01" placeholder="180000" /></div><div class="form-actions full"><button class="btn btn-primary" type="submit" :disabled="form.processing"><Check :size="15" /> Add centre</button></div></form></section></div>
</template>
