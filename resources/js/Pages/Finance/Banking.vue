<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowRight, Landmark, Plus } from 'lucide-vue-next';
import AppLayout from '../../Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
defineProps({ accounts: Array, summary: Object });
const form = useForm({ name: '', bank: '', account_no: '', type: 'Current', opening_balance: '' });
const submit = () => form.post('/banking', { onSuccess: () => form.reset() });
</script>
<template>
    <Head title="Banking" />
    <div class="page-heading"><div><p class="eyebrow"><span class="dot" /> Treasury control</p><h1>Know where the money moves.</h1><p>Keep bank, wallet and current-account balances visible beside the rest of the business.</p></div><Link href="/cheques" class="btn btn-ghost">Cheques <ArrowRight :size="14" /></Link></div>
    <div class="metric-strip"><div class="mini-metric"><span>Total balance</span><strong>{{ summary.balance }}</strong><em>Across active accounts</em></div><div class="mini-metric"><span>Accounts</span><strong>{{ summary.accounts }}</strong><em>{{ summary.active }} active</em></div><div class="mini-metric"><span>Pending cheques</span><strong>{{ summary.cheques }}</strong><em>Needs reconciliation</em></div></div>
    <div class="dashboard-grid"><section class="card"><div class="card-header"><div><h2>Bank accounts</h2><p>Operating accounts and mobile wallets</p></div></div><div class="table-scroll"><table class="data-table"><thead><tr><th>Account</th><th>Bank</th><th>Number</th><th>Balance</th><th>Cheques</th></tr></thead><tbody><tr v-for="account in accounts" :key="account.id"><td><div class="data-title">{{ account.name }}</div><div class="data-subtitle">{{ account.type }}</div></td><td>{{ account.bank }}</td><td>{{ account.accountNo }}</td><td><strong>{{ account.balance }}</strong></td><td>{{ account.cheques }}</td></tr></tbody></table></div></section><section class="card form-card"><div class="card-header" style="padding:0 0 18px"><div><h2>Add account</h2><p>Start tracking a new money rail.</p></div><div class="kpi-icon lime"><Landmark :size="17" /></div></div><form class="form-grid" @submit.prevent="submit"><div class="field full"><label>Account name</label><input v-model="form.name" placeholder="Redbook operating account" /></div><div class="field"><label>Bank / provider</label><input v-model="form.bank" placeholder="BRAC Bank" /></div><div class="field"><label>Account number</label><input v-model="form.account_no" placeholder="BRAC-001-8842" /></div><div class="field"><label>Type</label><select v-model="form.type"><option>Current</option><option>Savings</option><option>Mobile wallet</option></select></div><div class="field"><label>Opening balance</label><input v-model="form.opening_balance" type="number" min="0" step="0.01" placeholder="0" /></div><div class="form-actions full"><button class="btn btn-primary" type="submit" :disabled="form.processing"><Plus :size="15" /> Add account</button></div></form></section></div>
</template>
