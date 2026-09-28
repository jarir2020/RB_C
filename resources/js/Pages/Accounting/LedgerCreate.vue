<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Check, FileText } from 'lucide-vue-next';
import AppLayout from '../../Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const props = defineProps({ accounts: Array, branches: Array });
const form = useForm({
    ledger_account_id: props.accounts?.[0]?.id || '',
    branch_id: '',
    entry_date: new Date().toISOString().slice(0, 10),
    reference: '',
    description: '',
    debit: '',
    credit: '',
});

const submit = () => form.post('/account/ledger');
</script>

<template>
    <Head title="New voucher" />
    <div class="page-heading">
        <div>
            <p class="eyebrow"><span class="dot" /> Financial control</p>
            <h1>Post a new voucher.</h1>
            <p>Capture one journal movement with a clear reference and accountable owner.</p>
        </div>
        <Link href="/account/ledger" class="btn btn-ghost"><ArrowLeft :size="15" /> Back to ledger</Link>
    </div>

    <section class="card form-card">
        <div class="card-header" style="padding:0 0 20px"><div><h2>Voucher details</h2><p>Use either debit or credit for each posted line.</p></div><div class="pos-cart-icon"><FileText :size="16" /></div></div>
        <form class="form-grid" @submit.prevent="submit">
            <div class="field full"><label for="ledger_account_id">Ledger account</label><select id="ledger_account_id" v-model="form.ledger_account_id"><option v-for="account in accounts" :key="account.id" :value="account.id">{{ account.code }} · {{ account.name }} ({{ account.type }})</option></select><span v-if="form.errors.ledger_account_id" class="form-error">{{ form.errors.ledger_account_id }}</span></div>
            <div class="field"><label for="branch_id">Branch <span class="field-hint">optional</span></label><select id="branch_id" v-model="form.branch_id"><option value="">All branches</option><option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}</option></select><span v-if="form.errors.branch_id" class="form-error">{{ form.errors.branch_id }}</span></div>
            <div class="field"><label for="entry_date">Entry date</label><input id="entry_date" v-model="form.entry_date" type="date" /><span v-if="form.errors.entry_date" class="form-error">{{ form.errors.entry_date }}</span></div>
            <div class="field"><label for="reference">Reference</label><input id="reference" v-model="form.reference" placeholder="e.g. EXP-2026-001" /><span v-if="form.errors.reference" class="form-error">{{ form.errors.reference }}</span></div>
            <div class="field"><label for="description">Description</label><input id="description" v-model="form.description" placeholder="What was this movement for?" /><span v-if="form.errors.description" class="form-error">{{ form.errors.description }}</span></div>
            <div class="field"><label for="debit">Debit amount</label><input id="debit" v-model="form.debit" type="number" min="0" step="0.01" placeholder="0.00" /><span v-if="form.errors.debit" class="form-error">{{ form.errors.debit }}</span></div>
            <div class="field"><label for="credit">Credit amount</label><input id="credit" v-model="form.credit" type="number" min="0" step="0.01" placeholder="0.00" /><span v-if="form.errors.credit" class="form-error">{{ form.errors.credit }}</span></div>
            <div class="form-actions full"><Link href="/account/ledger" class="btn btn-ghost">Cancel</Link><button class="btn btn-primary" type="submit" :disabled="form.processing"><Check :size="15" />{{ form.processing ? 'Posting…' : 'Post voucher' }}</button></div>
        </form>
    </section>
</template>
