<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { CornerUpLeft, Plus } from 'lucide-vue-next';
import AppLayout from '../../Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
defineProps({ returns: Array, summary: Object });
</script>

<template>
    <Head title="Returns" />
    <div class="page-heading"><div><p class="eyebrow"><span class="dot" /> After-sales care</p><h1>Make the right thing easy to return.</h1><p>Keep the reason, item, refund and restock movement together so customer care does not become an inventory blind spot.</p></div><Link href="/returns/create" class="btn btn-primary"><Plus :size="15" /> Record return</Link></div>
    <div class="metric-strip"><div class="mini-metric"><span>Return records</span><strong>{{ summary.count }}</strong><em>Traceable cases</em></div><div class="mini-metric"><span>Units returned</span><strong>{{ summary.units }}</strong><em>Back in stock</em></div><div class="mini-metric"><span>Refund value</span><strong>{{ summary.refunds }}</strong><em>Customer resolution</em></div><div class="mini-metric"><span>Today</span><strong>{{ summary.today }}</strong><em>Cases opened</em></div></div>
    <section class="card"><div class="card-header"><div><h2>Return register</h2><p>Restocked items and refund context</p></div></div><div class="table-scroll"><table class="data-table"><thead><tr><th>Return</th><th>Invoice</th><th>Customer</th><th>Product</th><th>Qty</th><th>Refund</th><th>Status</th></tr></thead><tbody><tr v-for="item in returns" :key="item.number"><td><div class="payment-title"><span class="payment-icon return-icon"><CornerUpLeft :size="13" /></span><span class="data-title">{{ item.number }}</span></div><div class="data-subtitle">{{ item.date }}</div></td><td>{{ item.invoice }}</td><td>{{ item.customer }}</td><td><div class="data-title">{{ item.product }}</div><div class="data-subtitle">{{ item.reason }}</div></td><td>{{ item.quantity }}</td><td><strong>{{ item.refund }}</strong></td><td><span class="status-pill">{{ item.status }}</span></td></tr></tbody></table></div></section>
    </template>
