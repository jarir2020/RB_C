<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { Check, Factory, Plus } from 'lucide-vue-next';
import AppLayout from '../../Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
defineProps({ orders: Array, boms: Array, summary: Object });
const complete = (id) => router.post(`/production/${id}/complete`);
</script>
<template>
    <Head title="Production" />
    <div class="page-heading"><div><p class="eyebrow"><span class="dot" /> Production control</p><h1>Turn recipes into accountable output.</h1><p>Keep BOMs, component consumption and finished stock in the same operational loop.</p></div><Link href="/production/create" class="btn btn-primary"><Plus :size="15" /> Plan production</Link></div>
    <div class="metric-strip"><div class="mini-metric"><span>Orders</span><strong>{{ summary.orders }}</strong><em>Production log</em></div><div class="mini-metric"><span>Planned</span><strong>{{ summary.planned }}</strong><em>Ready to consume</em></div><div class="mini-metric"><span>Completed</span><strong>{{ summary.completed }}</strong><em>Output posted</em></div><div class="mini-metric"><span>Recipes</span><strong>{{ summary.recipes }}</strong><em>Active BOMs</em></div></div>
    <section class="card"><div class="card-header"><div><h2>Production orders</h2><p>Complete an order only when its component stock is available.</p></div></div><div class="table-scroll"><table class="data-table"><thead><tr><th>Order</th><th>Recipe</th><th>Branch</th><th>Planned</th><th>Output</th><th>Status</th><th /></tr></thead><tbody><tr v-for="order in orders" :key="order.id"><td><div class="data-title">{{ order.number }}</div><div class="data-subtitle">{{ order.product }}</div></td><td>{{ order.bom }}</td><td>{{ order.branch }}</td><td>{{ order.date }}</td><td>{{ order.quantity }}</td><td><span class="status-pill" :class="{ warning: order.status === 'Planned' }">{{ order.status }}</span></td><td><button v-if="order.status === 'Planned'" class="btn btn-ghost btn-small" type="button" @click="complete(order.id)"><Check :size="13" /> Complete</button></td></tr><tr v-if="!orders.length"><td colspan="7" class="table-empty">No production orders yet.</td></tr></tbody></table></div></section>
    <section class="card" style="margin-top:17px"><div class="card-header"><div><h2>Bill of materials</h2><p>Component recipes used by production orders</p></div><Factory :size="18" style="color:#7b941d" /></div><div class="table-scroll"><table class="data-table"><thead><tr><th>Recipe</th><th>Finished product</th><th>Components</th><th>Output</th><th>Status</th></tr></thead><tbody><tr v-for="bom in boms" :key="bom.name"><td class="data-title">{{ bom.name }}</td><td>{{ bom.product }}</td><td>{{ bom.component }}</td><td>{{ bom.output }}</td><td><span class="status-pill">{{ bom.status }}</span></td></tr></tbody></table></div></section>
</template>
