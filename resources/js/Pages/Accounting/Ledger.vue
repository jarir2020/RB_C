<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, Plus, Search } from 'lucide-vue-next';
import { ref } from 'vue';
import AppLayout from '../../Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const props = defineProps({
    entries: Array,
    accounts: Array,
    filters: Object,
    summary: Object,
});

const search = ref(props.filters?.search || '');
const accountId = ref(props.filters?.account_id || '');

const applyFilters = () => router.get('/account/ledger', {
    search: search.value || undefined,
    account_id: accountId.value || undefined,
}, { preserveState: true, replace: true });
</script>

<template>
    <Head title="Ledger" />
    <div class="page-heading">
        <div>
            <p class="eyebrow"><span class="dot" /> Financial control</p>
            <h1>The journal behind every decision.</h1>
            <p>Review posted vouchers by account, branch, reference, and movement.</p>
        </div>
        <div class="heading-actions">
            <Link href="/account" class="btn btn-ghost"><ArrowLeft :size="15" /> Accounting</Link>
            <Link href="/account/ledger/create" class="btn btn-primary"><Plus :size="15" /> New voucher</Link>
        </div>
    </div>

    <div class="metric-strip">
        <div class="mini-metric"><span>Posted entries</span><strong>{{ summary.entries }}</strong><em>Filtered journal view</em></div>
        <div class="mini-metric"><span>Total debit</span><strong>{{ summary.debit }}</strong><em>Increase in debit-side accounts</em></div>
        <div class="mini-metric"><span>Total credit</span><strong>{{ summary.credit }}</strong><em>Increase in credit-side accounts</em></div>
        <div class="mini-metric"><span>Accounts used</span><strong>{{ summary.accounts }}</strong><em>Across this selection</em></div>
    </div>

    <section class="card">
        <div class="card-header">
            <div><h2>Posted journal</h2><p>Search references or descriptions, then narrow to one control account.</p></div>
        </div>
        <div class="toolbar" style="padding:17px 21px 0">
            <label class="table-search"><Search :size="15" /><input v-model="search" placeholder="Search journal" @keyup.enter="applyFilters" /></label>
            <select v-model="accountId" class="field-select" @change="applyFilters">
                <option value="">All accounts</option>
                <option v-for="account in accounts" :key="account.id" :value="account.id">{{ account.code }} · {{ account.name }}</option>
            </select>
            <button class="btn btn-ghost btn-small" type="button" @click="applyFilters">Apply filters</button>
        </div>
        <div class="table-scroll">
            <table class="data-table">
                <thead><tr><th>Date</th><th>Reference</th><th>Description</th><th>Account</th><th>Branch</th><th>Debit</th><th>Credit</th></tr></thead>
                <tbody>
                    <tr v-for="entry in entries" :key="entry.reference + entry.description + entry.date">
                        <td>{{ entry.date }}</td>
                        <td><span class="status-pill neutral">{{ entry.reference }}</span></td>
                        <td><div class="data-title">{{ entry.description }}</div></td>
                        <td>{{ entry.account }}</td>
                        <td>{{ entry.branch }}</td>
                        <td><strong>{{ entry.debit }}</strong></td>
                        <td><strong>{{ entry.credit }}</strong></td>
                    </tr>
                    <tr v-if="!entries.length"><td colspan="7" class="table-empty">No journal entries match this view.</td></tr>
                </tbody>
            </table>
        </div>
    </section>
</template>
