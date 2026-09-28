<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { Download, Plus } from 'lucide-vue-next';
import AppLayout from '../../Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
const props = defineProps({ summary: Object, orders: Array, filters: Object });
const filter = (status) => router.get('/purchase', { status }, { preserveState: true, replace: true });
</script>

<template>
    <Head title="Purchases" />
    <div class="page-heading"><div><p class="eyebrow"><span class="dot" /> Supply network</p><h1>Purchasing with a clear next step.</h1><p>From purchase order to goods receipt, keep vendor commitments visible before they become surprises.</p></div><div class="heading-actions"><button class="btn btn-ghost"><Download :size="15" /> Export</button><Link href="/purchase/create" class="btn btn-primary"><Plus :size="15" /> New purchase</Link></div></div>
    <div class="metric-strip"><div v-for="(value, label) in { 'Purchases': summary.purchases, 'Received': summary.received, 'In transit': summary.pending, 'Active vendors': summary.vendors }" :key="label" class="mini-metric"><span>{{ label }}</span><strong>{{ value }}</strong><em>{{ label === 'In transit' ? 'Needs follow-up' : 'Across all branches' }}</em></div></div>
    <section class="card"><div class="card-header"><div><h2>Purchase orders</h2><p>Supplier commitments and goods received</p></div><div class="filter-tabs"><button :class="{ active: !props.filters?.status }" @click="filter('')">All</button><button :class="{ active: props.filters?.status === 'Received' }" @click="filter('Received')">Received</button><button :class="{ active: props.filters?.status === 'In transit' }" @click="filter('In transit')">In transit</button><button :class="{ active: props.filters?.status === 'Draft' }" @click="filter('Draft')">Draft</button></div></div><div class="table-scroll"><table class="data-table"><thead><tr><th>Order</th><th>Vendor</th><th>Date</th><th>Items</th><th>Total</th><th>Status</th></tr></thead><tbody><tr v-for="order in orders" :key="order.number"><td><div class="data-title">{{ order.number }}</div><div class="data-subtitle">Purchase order</div></td><td>{{ order.vendor }}</td><td>{{ order.date }}</td><td>{{ order.items }} items</td><td><strong>{{ order.total }}</strong></td><td><span class="status-pill" :class="{ warning: order.status === 'In transit', neutral: order.status === 'Draft' }">{{ order.status }}</span></td></tr></tbody></table></div></section>
</template>
