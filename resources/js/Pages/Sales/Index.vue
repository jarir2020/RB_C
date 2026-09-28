<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { Download, Plus, ReceiptText } from 'lucide-vue-next';
import AppLayout from '../../Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
const props = defineProps({ summary: Object, orders: Array, filters: Object });
const filter = (status) => router.get('/sales', { status }, { preserveState: true, replace: true });
</script>

<template>
    <Head title="Sales" />
    <div class="page-heading"><div><p class="eyebrow"><span class="dot" /> Revenue engine</p><h1>Sales that stay in motion.</h1><p>Track quotations, invoices, delivery and collections without losing the human context.</p></div><div class="heading-actions"><button class="btn btn-ghost"><Download :size="15" /> Export report</button><Link href="/sales/create" class="btn btn-primary"><Plus :size="15" /> New sale</Link></div></div>
    <div class="metric-strip"><div v-for="(value, label) in { 'Gross sales': summary.gross, 'Invoices': summary.orders, 'Average invoice': summary.average, 'Outstanding': summary.due }" :key="label" class="mini-metric"><span>{{ label }}</span><strong>{{ value }}</strong><em>{{ label === 'Outstanding' ? '4.2% lower this month' : 'vs previous period' }}</em></div></div>
    <section class="card"><div class="card-header"><div><h2>Sales board</h2><p>All customer-facing orders across your branches</p></div><div class="filter-tabs"><button :class="{ active: !props.filters?.status }" @click="filter('')">All</button><button :class="{ active: props.filters?.status === 'Paid' }" @click="filter('Paid')">Paid</button><button :class="{ active: props.filters?.status === 'Pending' }" @click="filter('Pending')">Pending</button><button :class="{ active: props.filters?.status === 'Overdue' }" @click="filter('Overdue')">Overdue</button></div></div><div class="table-scroll"><table class="data-table"><thead><tr><th>Invoice</th><th>Customer</th><th>Date</th><th>Channel</th><th>Total</th><th>Status</th></tr></thead><tbody><tr v-for="order in orders" :key="order.number"><td><div class="data-title">{{ order.number }}</div><div class="data-subtitle">Sales invoice</div></td><td>{{ order.customer }}</td><td>{{ order.date }}</td><td>{{ order.channel }}</td><td><strong>{{ order.total }}</strong></td><td><span class="status-pill" :class="{ warning: order.status === 'Pending', danger: order.status === 'Overdue' }">{{ order.status }}</span></td></tr></tbody></table></div></section>
    <div class="empty-note"><ReceiptText :size="17" /><span>Approval workflows, delivery stages and customer portals will share this same calm transaction language.</span><Link href="/account" class="card-link">Open accounting ↗</Link></div>
</template>
