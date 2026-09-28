<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowDownRight, ArrowUpRight, ArrowUpRight as ExternalArrow, Plus, TrendingUp, Package, WalletCards, GitBranch } from 'lucide-vue-next';
import AppLayout from '../../Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
const props = defineProps({ kpis: Array, salesTrend: Object, recentActivity: Array, topProducts: Array, branches: Array });
const iconMap = { TrendingUp, Package, WalletCards, GitBranch };
const chartPoints = computed(() => {
    const values = props.salesTrend.values || [];
    const max = Math.max(...values, 1);
    const width = 650;
    const height = 170;
    return values.map((value, index) => `${(index / Math.max(values.length - 1, 1)) * width},${height - (value / max) * 135 - 12}`).join(' ');
});
const areaPoints = computed(() => `0,170 ${chartPoints.value} 650,170`);
</script>

<template>
    <Head title="Overview" />
    <div class="page-heading">
        <div><p class="eyebrow"><span class="dot" /> Monday, 28 September 2026</p><h1>Good morning, admin.</h1><p>Your business is moving in the right direction. Here is the shape of your operation today.</p></div>
        <div class="heading-actions"><Link href="/inventory/create" class="btn btn-ghost"><Plus :size="15" /> Add stock item</Link><Link href="/sales" class="btn btn-primary">New sale <ExternalArrow :size="15" /></Link></div>
    </div>

    <div class="kpi-grid">
        <article v-for="kpi in kpis" :key="kpi.label" class="kpi-card"><div class="kpi-top"><span class="kpi-caption">{{ kpi.label }}</span><span class="kpi-icon" :class="kpi.tone"><component :is="iconMap[kpi.icon]" :size="17" /></span></div><div class="kpi-value">{{ kpi.value }}</div><span class="kpi-change" :class="{ down: kpi.change.startsWith('-'), neutral: kpi.change === 'All healthy' }"><ArrowDownRight v-if="kpi.change.startsWith('-')" :size="13" /><ArrowUpRight v-else-if="kpi.change.includes('%')" :size="13" />{{ kpi.change }}<span v-if="kpi.change.includes('%')">vs last month</span></span></article>
    </div>

    <div class="dashboard-grid">
        <div>
            <section class="card chart-card"><div class="card-header"><div><h2>Revenue pulse</h2><p>Gross sales across all branches · September 2026</p></div><div class="chart-legend"><span><i /> Gross sales</span><span>৳ BDT</span></div></div><div class="chart-wrap"><svg class="chart-svg" viewBox="0 0 650 205" preserveAspectRatio="none"><defs><linearGradient id="chartFade" x1="0" x2="0" y1="0" y2="1"><stop offset="0" stop-color="#d8f36b" stop-opacity=".45" /><stop offset="1" stop-color="#d8f36b" stop-opacity="0" /></linearGradient></defs><line v-for="y in [20,60,100,140,180]" :key="y" x1="0" :y1="y" x2="650" :y2="y" class="chart-gridline" /><polygon :points="areaPoints" class="chart-fill" /><polyline :points="chartPoints" class="chart-line" /><circle v-for="(point, index) in chartPoints.split(' ')" :key="index" :cx="point.split(',')[0]" :cy="point.split(',')[1]" r="4" class="chart-point" /></svg><div class="chart-labels"><span v-for="label in salesTrend.labels" :key="label">{{ label }}</span></div></div></section>
            <section class="card"><div class="card-header"><div><h2>Branch performance</h2><p>Where your momentum is coming from</p></div><Link class="card-link" href="/dashboard?view=branches">View all branches ↗</Link></div><div class="table-scroll"><table class="branch-table"><thead><tr><th>Branch</th><th>Sales</th><th>Target score</th><th>Status</th></tr></thead><tbody><tr v-for="branch in branches" :key="branch.code"><td><div class="branch-name"><span class="branch-badge">{{ branch.code.slice(-2) }}</span>{{ branch.name }}<span class="data-subtitle">{{ branch.code }}</span></div></td><td><strong>{{ branch.sales }}</strong></td><td><div class="score"><span class="score-track"><i class="score-fill" :style="{ width: `${branch.score}%` }" /></span>{{ branch.score }}%</div></td><td><span class="status-pill">{{ branch.status }}</span></td></tr></tbody></table></div></section>
        </div>
        <div>
            <section class="card"><div class="card-header"><div><h2>Live activity</h2><p>Latest movement in your workspace</p></div><button class="icon-button" style="color:#81908e"><ExternalArrow :size="15" /></button></div><div class="activity-list"><div v-for="item in recentActivity" :key="item.title" class="activity-row"><span class="activity-dot" :class="item.type" /><div class="activity-copy"><strong>{{ item.title }}</strong><span>{{ item.meta }}</span></div><span class="activity-amount" :class="item.type">{{ item.amount }}</span></div></div></section>
            <section class="card"><div class="card-header"><div><h2>Products to watch</h2><p>Based on velocity and reorder level</p></div><Link class="card-link" href="/inventory">Inventory ↗</Link></div><div class="product-list"><div v-for="product in topProducts" :key="product.name" class="product-row"><div class="product-line"><div class="product-copy"><strong>{{ product.name }}</strong><span>{{ product.category }} · {{ product.sold }}</span></div><span class="product-sold">{{ product.progress }}%</span></div><div class="progress-track"><div class="progress-bar" :class="product.color" :style="{ width: `${product.progress}%` }" /></div></div></div></section>
        </div>
    </div>
</template>
