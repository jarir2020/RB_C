<script setup>
import { computed, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, CheckCircle2 } from 'lucide-vue-next';
import AppLayout from '../../Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
const props = defineProps({ sales: Array, methods: Array });
const form = useForm({ sale_id: props.sales?.[0]?.id || '', amount: props.sales?.[0]?.outstanding || '', method: props.methods?.[0] || 'Bank transfer', paid_on: new Date().toISOString().slice(0, 10), reference: '' });
const selectedSale = computed(() => props.sales?.find((sale) => sale.id === Number(form.sale_id)));
watch(() => form.sale_id, () => { form.amount = selectedSale.value?.outstanding || ''; });
const submit = () => form.post('/payments');
</script>

<template>
    <Head title="Record payment" />
    <div class="page-heading"><div><p class="eyebrow"><span class="dot" /> Cash movement</p><h1>Move one invoice forward.</h1><p>Record the payment once; the invoice status will close automatically when the full amount is collected.</p></div><Link href="/payments" class="btn btn-ghost"><ArrowLeft :size="15" /> Back to payments</Link></div>
    <section class="card form-card"><div class="card-header" style="padding:0 0 20px"><div><h2>Payment details</h2><p>References make future reconciliation calm and quick.</p></div></div><form class="form-grid" @submit.prevent="submit"><div class="field full"><label for="sale_id">Invoice</label><select id="sale_id" v-model="form.sale_id"><option v-for="sale in sales" :key="sale.id" :value="sale.id">{{ sale.invoice }} · {{ sale.customer }} · ৳ {{ Number(sale.outstanding).toLocaleString() }} outstanding</option></select><small v-if="form.errors.sale_id" class="form-error">{{ form.errors.sale_id }}</small></div><div class="field"><label for="amount">Amount (BDT)</label><input id="amount" v-model="form.amount" type="number" min="1" step="0.01" /><small v-if="form.errors.amount" class="form-error">{{ form.errors.amount }}</small></div><div class="field"><label for="method">Payment method</label><select id="method" v-model="form.method"><option v-for="method in methods" :key="method">{{ method }}</option></select><small v-if="form.errors.method" class="form-error">{{ form.errors.method }}</small></div><div class="field"><label for="paid_on">Paid on</label><input id="paid_on" v-model="form.paid_on" type="date" /><small v-if="form.errors.paid_on" class="form-error">{{ form.errors.paid_on }}</small></div><div class="field"><label for="reference">Reference (optional)</label><input id="reference" v-model="form.reference" placeholder="TRX-2034" /><small v-if="form.errors.reference" class="form-error">{{ form.errors.reference }}</small></div><div class="form-actions field full"><Link href="/payments" class="btn btn-ghost">Cancel</Link><button class="btn btn-primary" type="submit" :disabled="form.processing"><CheckCircle2 :size="15" /> {{ form.processing ? 'Recording…' : 'Record payment' }}</button></div></form></section>
</template>
