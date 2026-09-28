<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, Plus, Search } from 'lucide-vue-next';
import { ref } from 'vue';
import AppLayout from '../../Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
const props = defineProps({ variants: Array, filters: Object });
const search = ref(props.filters?.search || '');
const apply = () => router.get('/inventory/variants', { search: search.value || undefined }, { preserveState: true, replace: true });
</script>

<template>
    <Head title="Product variants" />
    <div class="page-heading"><div><p class="eyebrow"><span class="dot" /> Inventory setup</p><h1>Variants that stay recognizable.</h1><p>Track colour, size, pack and barcode differences without losing the parent product.</p></div><div class="heading-actions"><Link href="/inventory" class="btn btn-ghost"><ArrowLeft :size="15" /> Inventory</Link><Link href="/inventory/variants/create" class="btn btn-primary"><Plus :size="15" /> Add variant</Link></div></div>
    <section class="card"><div class="card-header"><div><h2>Variant catalogue</h2><p>{{ variants.length }} variant records in this view</p></div></div><div class="toolbar" style="padding:17px 21px 0"><form class="table-search" @submit.prevent="apply"><Search :size="15" /><input v-model="search" placeholder="Search variant, SKU or barcode" /></form></div><div class="table-scroll"><table class="data-table"><thead><tr><th>Parent product</th><th>Variant</th><th>SKU / barcode</th><th>Price</th><th>Stock</th><th>Status</th></tr></thead><tbody><tr v-for="variant in variants" :key="variant.id"><td>{{ variant.product }}</td><td><div class="data-title">{{ variant.name }}</div></td><td><div class="data-title">{{ variant.sku }}</div><div class="data-subtitle">{{ variant.barcode || 'No barcode' }}</div></td><td><strong>{{ variant.price }}</strong></td><td>{{ variant.stock }}</td><td><span class="status-pill" :class="{ warning: variant.status === 'Low stock' }">{{ variant.status }}</span></td></tr><tr v-if="!variants.length"><td colspan="6" class="table-empty">No variants match this search.</td></tr></tbody></table></div></section>
</template>
