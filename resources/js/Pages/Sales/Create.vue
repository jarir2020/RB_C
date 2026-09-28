<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, CheckCircle2 } from 'lucide-vue-next';
import AppLayout from '../../Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
const props = defineProps({ customers: Array, branches: Array });
const form = useForm({ contact_id: props.customers?.[0]?.id || '', branch_id: props.branches?.[0]?.id || '', total: '', channel: 'Retail' });
const submit = () => form.post('/sales');
</script>
<template>
    <Head title="New sale" />
    <div class="page-heading"><div><p class="eyebrow"><span class="dot" /> Sales workspace</p><h1>Start a new sale.</h1><p>Create the commercial record first; approval, delivery and payment can follow as the order moves.</p></div><Link href="/sales" class="btn btn-ghost"><ArrowLeft :size="15" /> Back to sales</Link></div>
    <section class="card form-card"><div class="card-header" style="padding:0 0 20px"><div><h2>Sale details</h2><p>This creates a pending invoice in the current workspace.</p></div></div><form class="form-grid" @submit.prevent="submit"><div class="field"><label for="customer">Customer</label><select id="customer" v-model="form.contact_id"><option v-for="customer in customers" :key="customer.id" :value="customer.id">{{ customer.name }}</option></select><small v-if="form.errors.contact_id" class="form-error">{{ form.errors.contact_id }}</small></div><div class="field"><label for="branch">Branch</label><select id="branch" v-model="form.branch_id"><option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}</option></select><small v-if="form.errors.branch_id" class="form-error">{{ form.errors.branch_id }}</small></div><div class="field"><label for="total">Invoice total (BDT)</label><input id="total" v-model="form.total" type="number" min="1" step="0.01" placeholder="48500" /><small v-if="form.errors.total" class="form-error">{{ form.errors.total }}</small></div><div class="field"><label for="channel">Sales channel</label><select id="channel" v-model="form.channel"><option>Retail</option><option>Wholesale</option><option>Institutional</option><option>POS</option></select></div><div class="form-actions field full"><Link href="/sales" class="btn btn-ghost">Cancel</Link><button class="btn btn-primary" type="submit" :disabled="form.processing"><CheckCircle2 :size="15" /> {{ form.processing ? 'Creating…' : 'Create pending sale' }}</button></div></form></section>
</template>
