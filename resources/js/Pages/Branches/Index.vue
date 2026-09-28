<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { GitBranch, Plus } from 'lucide-vue-next';
import AppLayout from '../../Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
defineProps({ branches: Array });
</script>
<template>
    <Head title="Branches" />
    <div class="page-heading"><div><p class="eyebrow"><span class="dot" /> Network view</p><h1>Every branch, one rhythm.</h1><p>See the health of each location without losing the local context that makes it work.</p></div><Link href="/branches/create" class="btn btn-primary"><Plus :size="15" /> Add branch</Link></div>
    <div class="metric-strip"><div class="mini-metric"><span>Active locations</span><strong>{{ branches.length }}</strong><em>All reporting today</em></div><div class="mini-metric"><span>Network sales</span><strong>৳ 4.82M</strong><em>+18.6% this month</em></div><div class="mini-metric"><span>On target</span><strong>{{ branches.filter(branch => branch.score >= 80).length }}</strong><em>Performance signal</em></div><div class="mini-metric"><span>Coverage</span><strong>64%</strong><em>Across Bangladesh</em></div></div>
    <section class="card"><div class="card-header"><div><h2>Branch performance</h2><p>Sales against target, with room to act</p></div></div><div class="table-scroll"><table class="data-table"><thead><tr><th>Branch</th><th>City</th><th>Sales</th><th>Target</th><th>Achievement</th><th>Status</th></tr></thead><tbody><tr v-for="branch in branches" :key="branch.code"><td><div class="branch-name"><span class="branch-badge"><GitBranch :size="13" /></span><span><strong>{{ branch.name }}</strong><small class="data-subtitle">{{ branch.code }}</small></span></div></td><td>{{ branch.city }}</td><td><strong>{{ branch.sales }}</strong></td><td>{{ branch.target }}</td><td><div class="score"><span class="score-track"><i class="score-fill" :style="{ width: `${branch.score}%` }" /></span>{{ branch.score }}%</div></td><td><span class="status-pill" :class="{ warning: branch.score < 70 }">{{ branch.status }}</span></td></tr></tbody></table></div></section>
</template>
