<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ArrowUpRight, BarChart3, Download } from 'lucide-vue-next';
import AppLayout from '../../Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
defineProps({ cards: Array });
</script>
<template>
    <Head title="Reports" />
    <div class="page-heading"><div><p class="eyebrow"><span class="dot" /> Management intelligence</p><h1>Reports with a point of view.</h1><p>Turn operational detail into the conversations your next decision deserves.</p></div><Link href="/account/ledger" class="btn btn-ghost"><Download :size="15" /> Open ledger</Link></div>
    <div class="report-grid"><article v-for="card in cards" :key="card.title" class="card report-card"><div class="report-icon" :class="card.tone"><BarChart3 :size="18" /></div><p class="eyebrow" style="margin-top:22px">{{ card.title }}</p><h2>{{ card.value }}</h2><p>{{ card.description }}</p><span class="kpi-change neutral"><ArrowUpRight :size="13" />{{ card.change }}</span><Link href="/account/ledger" class="card-link">Open ledger <ArrowUpRight :size="13" /></Link></article></div>
</template>
