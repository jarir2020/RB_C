<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save } from 'lucide-vue-next';
import AppLayout from '../../Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
const props = defineProps({ product: Object, units: Array });
const form = useForm({ ...props.product });
const submit = () => form.patch(`/inventory/${props.product.id}`);
</script>
<template>
    <Head title="Edit product" />
    <div class="page-heading"><div><p class="eyebrow"><span class="dot" /> Product detail</p><h1>Refine {{ product.name }}.</h1><p>Keep commercial, barcode and reorder context current for every branch.</p></div><Link :href="`/inventory/${product.id}`" class="btn btn-ghost"><ArrowLeft :size="15" /> Product detail</Link></div>
    <section class="card form-card"><form class="form-grid" @submit.prevent="submit"><div class="field full"><label>Product name</label><input v-model="form.name" /><small v-if="form.errors.name" class="form-error">{{ form.errors.name }}</small></div><div class="field"><label>SKU</label><input v-model="form.sku" /><small v-if="form.errors.sku" class="form-error">{{ form.errors.sku }}</small></div><div class="field"><label>Barcode</label><input v-model="form.barcode" /></div><div class="field"><label>Category</label><input v-model="form.category" /></div><div class="field"><label>Brand</label><input v-model="form.brand" /></div><div class="field"><label>Unit</label><select v-model="form.unit"><option v-for="unit in units" :key="unit">{{ unit }}</option></select></div><div class="field"><label>Retail price</label><input v-model="form.price" type="number" min="0" step="0.01" /></div><div class="field"><label>Cost price</label><input v-model="form.cost_price" type="number" min="0" step="0.01" /></div><div class="field"><label>Wholesale price</label><input v-model="form.wholesale_price" type="number" min="0" step="0.01" /></div><div class="field"><label>Price label</label><input v-model="form.price_label" /></div><div class="field"><label>Stock</label><input v-model="form.stock" type="number" min="0" /></div><div class="field"><label>Reorder level</label><input v-model="form.reorder_level" type="number" min="0" /></div><div class="field full"><label>Description</label><input v-model="form.description" /></div><div class="form-actions full"><Link :href="`/inventory/${product.id}`" class="btn btn-ghost">Cancel</Link><button class="btn btn-primary" type="submit" :disabled="form.processing"><Save :size="15" /> Save changes</button></div></form></section>
</template>
