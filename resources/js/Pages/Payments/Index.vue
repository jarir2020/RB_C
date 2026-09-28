<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ArrowDownLeft, Plus } from 'lucide-vue-next';
import AppLayout from '../../Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
defineProps({ payments: Array, summary: Object });
</script>

<template>
    <Head title="Payments" />
    <div class="page-heading"><div><p class="eyebrow"><span class="dot" /> Cash movement</p><h1>Make collection feel visible.</h1><p>Every payment gets a home, a reference and a relationship back to the invoice it moves forward.</p></div><Link href="/payments/create" class="btn btn-primary"><Plus :size="15" /> Record payment</Link></div>
    <div class="metric-strip"><div class="mini-metric"><span>Total collected</span><strong>{{ summary.collected }}</strong><em>All recorded payments</em></div><div class="mini-metric"><span>Payment records</span><strong>{{ summary.count }}</strong><em>Traceable movements</em></div><div class="mini-metric"><span>Collected today</span><strong>{{ summary.today }}</strong><em style="color:#6c8c1c">Current day</em></div><div class="mini-metric"><span>Methods used</span><strong>{{ summary.methods }}</strong><em>Flexible collection</em></div></div>
    <section class="card"><div class="card-header"><div><h2>Payment activity</h2><p>Recent collections across the workspace</p></div></div><div class="table-scroll"><table class="data-table"><thead><tr><th>Payment</th><th>Invoice</th><th>Customer</th><th>Date</th><th>Method</th><th>Amount</th><th>Status</th></tr></thead><tbody><tr v-for="payment in payments" :key="payment.number"><td><div class="payment-title"><span class="payment-icon"><ArrowDownLeft :size="13" /></span><span class="data-title">{{ payment.number }}</span></div></td><td>{{ payment.invoice }}</td><td>{{ payment.customer }}</td><td>{{ payment.date }}</td><td>{{ payment.method }}</td><td><strong>{{ payment.amount }}</strong></td><td><span class="status-pill">{{ payment.status }}</span></td></tr></tbody></table></div></section>
</template>
