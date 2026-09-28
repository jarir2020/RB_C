<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Printer } from 'lucide-vue-next';
import AppLayout from '../../Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
defineProps({ products: Array });
const printLabels = () => window.print();
</script>

<template>
    <Head title="Price labels" />
    <div class="page-heading"><div><p class="eyebrow"><span class="dot" /> Inventory tools</p><h1>Barcode-ready price labels.</h1><p>Turn the catalogue into a print-friendly shelf-label sheet for branch teams.</p></div><div class="heading-actions"><Link href="/inventory" class="btn btn-ghost"><ArrowLeft :size="15" /> Inventory</Link><button class="btn btn-primary" type="button" @click="printLabels"><Printer :size="15" /> Print labels</button></div></div>
    <section class="label-grid"><article v-for="product in products" :key="product.sku" class="card price-label"><div class="label-brand">REDBOOK<span>ONE</span></div><strong>{{ product.name }}</strong><small>{{ product.sku }} · {{ product.label }}</small><div class="barcode"><i v-for="index in 34" :key="index" :style="{ width: `${index % 4 === 0 ? 3 : 1}px` }" /></div><span class="barcode-number">{{ product.barcode }}</span><b>{{ product.price }}</b></article><div v-if="!products.length" class="empty-note">Add a barcode or price label to a product to make it printable.</div></section>
</template>
