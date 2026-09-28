<script setup>
import { computed, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, CornerUpLeft } from 'lucide-vue-next';
import AppLayout from '../../Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
const props = defineProps({ sales: Array, reasons: Array });
const form = useForm({ sale_id: props.sales?.[0]?.id || '', product_id: props.sales?.[0]?.items?.[0]?.id || '', quantity: 1, returned_on: new Date().toISOString().slice(0, 10), reason: props.reasons?.[0] || '' });
const selectedSale = computed(() => props.sales?.find((sale) => sale.id === Number(form.sale_id)));
const saleItems = computed(() => selectedSale.value?.items || []);
const selectedItem = computed(() => saleItems.value.find((item) => item.id === Number(form.product_id)));
watch(() => form.sale_id, () => { form.product_id = saleItems.value[0]?.id || ''; form.quantity = 1; });
const submit = () => form.post('/returns');
</script>

<template>
    <Head title="Record return" />
    <div class="page-heading"><div><p class="eyebrow"><span class="dot" /> After-sales care</p><h1>Turn a return into a clear next step.</h1><p>Select the original invoice and item; the available quantity is checked before the stock is restocked.</p></div><Link href="/returns" class="btn btn-ghost"><ArrowLeft :size="15" /> Back to returns</Link></div>
    <section class="card form-card"><div class="card-header" style="padding:0 0 20px"><div><h2>Return details</h2><p>{{ selectedItem ? `${selectedItem.quantity} unit(s) were sold on this invoice.` : 'Choose an invoice to begin.' }}</p></div></div><form class="form-grid" @submit.prevent="submit"><div class="field full"><label for="sale_id">Original invoice</label><select id="sale_id" v-model="form.sale_id"><option v-for="sale in sales" :key="sale.id" :value="sale.id">{{ sale.invoice }} · {{ sale.customer }}</option></select><small v-if="form.errors.sale_id" class="form-error">{{ form.errors.sale_id }}</small></div><div class="field"><label for="product_id">Product</label><select id="product_id" v-model="form.product_id"><option v-for="item in saleItems" :key="item.id" :value="item.id">{{ item.name }} · {{ item.sku }}</option></select><small v-if="form.errors.product_id" class="form-error">{{ form.errors.product_id }}</small></div><div class="field"><label for="quantity">Quantity</label><input id="quantity" v-model.number="form.quantity" type="number" min="1" :max="selectedItem?.quantity || 1" /><small v-if="form.errors.quantity" class="form-error">{{ form.errors.quantity }}</small></div><div class="field"><label for="reason">Reason</label><select id="reason" v-model="form.reason"><option v-for="reason in reasons" :key="reason">{{ reason }}</option></select><small v-if="form.errors.reason" class="form-error">{{ form.errors.reason }}</small></div><div class="field"><label for="returned_on">Returned on</label><input id="returned_on" v-model="form.returned_on" type="date" /><small v-if="form.errors.returned_on" class="form-error">{{ form.errors.returned_on }}</small></div><div class="form-actions field full"><Link href="/returns" class="btn btn-ghost">Cancel</Link><button class="btn btn-primary" type="submit" :disabled="form.processing"><CornerUpLeft :size="15" /> {{ form.processing ? 'Recording…' : 'Record return' }}</button></div></form></section>
</template>
