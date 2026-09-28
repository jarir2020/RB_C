<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { CreditCard, Download, Plus } from 'lucide-vue-next';
import AppLayout from '../../Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
const props = defineProps({ invoices: Array, filters: Object, summary: Object });
const filter = (status) => router.get('/invoices', { status }, { preserveState: true, replace: true });
</script>

<template>
    <Head title="Invoices" />
    <div class="page-heading"><div><p class="eyebrow"><span class="dot" /> Receivables control</p><h1>Every invoice, one clear story.</h1><p>Keep the promise, collection and remaining balance visible without separating finance from the customer context.</p></div><div class="heading-actions"><button class="btn btn-ghost"><Download :size="15" /> Export</button><Link href="/payments/create" class="btn btn-primary"><Plus :size="15" /> Record payment</Link></div></div>
    <div class="metric-strip"><div class="mini-metric"><span>Total invoices</span><strong>{{ summary.total }}</strong><em>Across all branches</em></div><div class="mini-metric"><span>Paid</span><strong>{{ summary.paid }}</strong><em style="color:#6c8c1c">Closed cleanly</em></div><div class="mini-metric"><span>Open</span><strong>{{ summary.open }}</strong><em style="color:#b57e31">Needs attention</em></div><div class="mini-metric"><span>Amount due</span><strong>{{ summary.due }}</strong><em>Outstanding balance</em></div></div>
    <section class="card"><div class="card-header"><div><h2>Invoice register</h2><p>Customer invoices and collected amounts</p></div><div class="filter-tabs"><button :class="{ active: !props.filters?.status }" @click="filter('')">All</button><button :class="{ active: props.filters?.status === 'Paid' }" @click="filter('Paid')">Paid</button><button :class="{ active: props.filters?.status === 'Pending' }" @click="filter('Pending')">Pending</button><button :class="{ active: props.filters?.status === 'Overdue' }" @click="filter('Overdue')">Overdue</button></div></div><div class="table-scroll"><table class="data-table"><thead><tr><th>Invoice</th><th>Customer</th><th>Branch</th><th>Date</th><th>Total</th><th>Collected</th><th>Status</th></tr></thead><tbody><tr v-for="invoice in invoices" :key="invoice.number"><td><div class="data-title">{{ invoice.number }}</div><div class="data-subtitle">Customer invoice</div></td><td>{{ invoice.customer }}</td><td>{{ invoice.branch }}</td><td>{{ invoice.date }}</td><td><strong>{{ invoice.total }}</strong></td><td>{{ invoice.collected }}</td><td><span class="status-pill" :class="{ warning: invoice.status === 'Pending', danger: invoice.status === 'Overdue' }">{{ invoice.status }}</span></td></tr></tbody></table></div></section>
    <div class="empty-note"><CreditCard :size="17" /><span>Payment records now sit beside invoices, so collection progress can become a real workflow.</span><Link href="/payments" class="card-link">View payments ↗</Link></div>
</template>
