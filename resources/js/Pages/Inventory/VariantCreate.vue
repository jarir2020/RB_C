<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save } from 'lucide-vue-next';
import AppLayout from '../../Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
const props = defineProps({ products: Array });
const form = useForm({ product_id: props.products?.[0]?.id || '', name: '', sku: '', barcode: '', attributes: '', price: '', stock: 0, reorder_level: 5 });
const submit = () => form.post('/inventory/variants');
</script>

<template>
    <Head title="Add variant" />
    <div class="page-heading"><div><p class="eyebrow"><span class="dot" /> Inventory setup</p><h1>Add a product variant.</h1><p>Keep the parent product intact while giving your team a precise sellable unit.</p></div><Link href="/inventory/variants" class="btn btn-ghost"><ArrowLeft :size="15" /> Back to variants</Link></div>
    <section class="card form-card"><div class="card-header" style="padding:0 0 20px"><div><h2>Variant details</h2><p>Attributes use a simple comma-separated format such as size:L, colour:Navy.</p></div></div><form class="form-grid" @submit.prevent="submit"><div class="field full"><label for="product_id">Parent product</label><select id="product_id" v-model="form.product_id"><option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option></select><small v-if="form.errors.product_id" class="form-error">{{ form.errors.product_id }}</small></div><div class="field"><label for="name">Variant name</label><input id="name" v-model="form.name" placeholder="Navy · Large" /><small v-if="form.errors.name" class="form-error">{{ form.errors.name }}</small></div><div class="field"><label for="sku">Variant SKU</label><input id="sku" v-model="form.sku" placeholder="FSH-1180-NV-L" /><small v-if="form.errors.sku" class="form-error">{{ form.errors.sku }}</small></div><div class="field"><label for="barcode">Barcode</label><input id="barcode" v-model="form.barcode" placeholder="890118011181" /><small v-if="form.errors.barcode" class="form-error">{{ form.errors.barcode }}</small></div><div class="field"><label for="attributes">Attributes</label><input id="attributes" v-model="form.attributes" placeholder="size:L, colour:Navy" /></div><div class="field"><label for="price">Selling price</label><input id="price" v-model="form.price" type="number" min="0" step="0.01" placeholder="2950" /><small v-if="form.errors.price" class="form-error">{{ form.errors.price }}</small></div><div class="field"><label for="stock">Opening stock</label><input id="stock" v-model="form.stock" type="number" min="0" /><small v-if="form.errors.stock" class="form-error">{{ form.errors.stock }}</small></div><div class="field"><label for="reorder_level">Reorder at</label><input id="reorder_level" v-model="form.reorder_level" type="number" min="0" /></div><div class="form-actions full"><Link href="/inventory/variants" class="btn btn-ghost">Cancel</Link><button class="btn btn-primary" type="submit" :disabled="form.processing"><Save :size="15" /> {{ form.processing ? 'Saving…' : 'Save variant' }}</button></div></form></section>
</template>
