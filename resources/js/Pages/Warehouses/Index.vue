<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { Boxes, Plus } from 'lucide-vue-next';
import AppLayout from '../../Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
defineProps({ warehouses: Array, summary: Object });
</script>

<template>
    <Head title="Warehouses" />
    <div class="page-heading"><div><p class="eyebrow"><span class="dot" /> Stock network</p><h1>Space for stock to move.</h1><p>Understand where inventory sits, how much room remains and which branch owns the next movement.</p></div><Link href="/warehouses/create" class="btn btn-primary"><Plus :size="15" /> Add warehouse</Link></div>
    <div class="metric-strip"><div class="mini-metric"><span>Storage points</span><strong>{{ summary.total }}</strong><em>Active network</em></div><div class="mini-metric"><span>Total capacity</span><strong>{{ summary.capacity }}</strong><em>Registered space</em></div><div class="mini-metric"><span>Currently used</span><strong>{{ summary.used }}</strong><em>Stock in motion</em></div><div class="mini-metric"><span>Available</span><strong>{{ summary.available }}</strong><em style="color:#6c8c1c">Room to grow</em></div></div>
    <section class="card"><div class="card-header"><div><h2>Warehouse network</h2><p>Capacity and utilization by storage point</p></div></div><div class="table-scroll"><table class="data-table"><thead><tr><th>Warehouse</th><th>Branch</th><th>Capacity</th><th>Utilization</th><th>Status</th></tr></thead><tbody><tr v-for="warehouse in warehouses" :key="warehouse.code"><td><div class="branch-name"><span class="branch-badge"><Boxes :size="14" /></span><span><strong>{{ warehouse.name }}</strong><small class="data-subtitle">{{ warehouse.code }}</small></span></div></td><td>{{ warehouse.branch }}</td><td>{{ warehouse.capacity }}</td><td><div class="score"><span class="score-track warehouse-track"><i class="score-fill" :style="{ width: `${warehouse.utilization}%` }" /></span>{{ warehouse.utilization }}%</div></td><td><span class="status-pill">{{ warehouse.status }}</span></td></tr></tbody></table></div></section>
</template>
