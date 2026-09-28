<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowRight, GitBranch, Plus } from 'lucide-vue-next';
import AppLayout from '../../Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
const props = defineProps({ transfers: Array, summary: Object });
const receive = (id) => router.post(`/transfers/${id}/receive`);
</script>

<template>
    <Head title="Stock transfers" />
    <div class="page-heading"><div><p class="eyebrow"><span class="dot" /> Branch movement</p><h1>Let stock follow the demand.</h1><p>Make branch-to-branch movement visible, accountable and easy to reconcile when the truck arrives.</p></div><Link href="/transfers/create" class="btn btn-primary"><Plus :size="15" /> Create transfer</Link></div>
    <div class="metric-strip"><div class="mini-metric"><span>Transfer records</span><strong>{{ summary.count }}</strong><em>Auditable movement</em></div><div class="mini-metric"><span>Units moving</span><strong>{{ summary.units }}</strong><em>Across branches</em></div><div class="mini-metric"><span>In transit</span><strong>{{ summary.inTransit }}</strong><em style="color:#b57e31">Follow-up needed</em></div><div class="mini-metric"><span>Received</span><strong>{{ summary.received }}</strong><em style="color:#6c8c1c">Movement complete</em></div></div>
    <section class="card"><div class="card-header"><div><h2>Movement log</h2><p>Product, origin, destination and current state</p></div></div><div class="table-scroll"><table class="data-table"><thead><tr><th>Transfer</th><th>Product</th><th>Route</th><th>Qty</th><th>Date</th><th>Status</th><th /></tr></thead><tbody><tr v-for="transfer in transfers" :key="transfer.number"><td><div class="payment-title"><span class="payment-icon"><GitBranch :size="13" /></span><span><div class="data-title">{{ transfer.number }}</div><small class="data-subtitle">{{ transfer.note }}</small></span></div></td><td><div class="data-title">{{ transfer.product }}</div><div class="data-subtitle">{{ transfer.sku }}</div></td><td><div class="transfer-route"><span>{{ transfer.from }}</span><ArrowRight :size="13" /><span>{{ transfer.to }}</span></div></td><td><strong>{{ transfer.quantity }}</strong></td><td>{{ transfer.date }}</td><td><span class="status-pill" :class="{ warning: transfer.status === 'In transit' }">{{ transfer.status }}</span></td><td><button v-if="transfer.status === 'In transit'" class="btn btn-ghost btn-small" type="button" @click="receive(transfer.id)">Mark received</button></td></tr></tbody></table></div></section>
</template>
