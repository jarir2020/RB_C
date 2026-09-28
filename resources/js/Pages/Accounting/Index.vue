<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ArrowUpRight, FileText, Plus } from 'lucide-vue-next';
import AppLayout from '../../Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
defineProps({ accounts: Array, reports: Array, metrics: Array, recentEntries: Array });
</script>

<template>
    <Head title="Accounting" />
    <div class="page-heading"><div><p class="eyebrow"><span class="dot" /> Financial control</p><h1>Know what your business means.</h1><p>Make every receipt, payment, ledger and report part of one living financial picture.</p></div><div class="heading-actions"><Link href="/account/ledger/create" class="btn btn-ghost"><FileText :size="15" /> New voucher</Link><Link href="/account/ledger/create" class="btn btn-primary"><Plus :size="15" /> Add transaction</Link></div></div>
    <div class="metric-strip"><div v-for="metric in metrics" :key="metric.label" class="mini-metric"><span>{{ metric.label }}</span><strong>{{ metric.value }}</strong><em>{{ metric.note }}</em></div></div>
    <div class="dashboard-grid"><section class="card"><div class="card-header"><div><h2>Chart of accounts</h2><p>Primary control accounts and their posted balance</p></div><Link href="/account/ledger" class="card-link">Manage ledger ↗</Link></div><div class="table-scroll"><table class="data-table"><thead><tr><th>Code</th><th>Account</th><th>Type</th><th>Balance</th><th>Entries</th></tr></thead><tbody><tr v-for="account in accounts" :key="account.code"><td>{{ account.code }}</td><td><div class="data-title">{{ account.name }}</div></td><td><span class="status-pill neutral">{{ account.type }}</span></td><td><strong>{{ account.balance }}</strong></td><td class="kpi-change"><ArrowUpRight :size="13" />{{ account.entries }}</td></tr></tbody></table></div></section><section class="card"><div class="card-header"><div><h2>Management reports</h2><p>Board-ready views, always current</p></div><Link href="/reports" class="card-link">View reports ↗</Link></div><div class="activity-list"><Link v-for="report in reports" :key="report" href="/reports" class="activity-row"><span class="activity-dot positive" /><div class="activity-copy"><strong>{{ report }}</strong><span>Open the current management view</span></div><ArrowUpRight :size="15" style="color:#8e9d99" /></Link></div></section></div>
    <section class="card" style="margin-top:17px"><div class="card-header"><div><h2>Recent journal activity</h2><p>Latest posted vouchers across the network</p></div><Link href="/account/ledger" class="card-link">Open full journal ↗</Link></div><div class="activity-list"><div v-for="entry in recentEntries" :key="entry.reference + entry.description" class="activity-row"><span class="activity-dot positive" /><div class="activity-copy"><strong>{{ entry.description }}</strong><span>{{ entry.reference }} · {{ entry.account }} · {{ entry.date }}</span></div><span class="activity-amount">{{ entry.amount }}</span></div></div></section>
</template>
