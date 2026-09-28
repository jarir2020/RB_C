<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { Download, Filter, MoreHorizontal, Plus, Search, SlidersHorizontal } from 'lucide-vue-next';
import AppLayout from '../../Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
const props = defineProps({ products: Array, filters: Object, metrics: Object });
const search = props.filters?.search || '';
const submitSearch = (event) => router.get('/inventory', { search: event.target.search.value }, { preserveState: true, replace: true });
</script>

<template>
    <Head title="Inventory" />
    <div class="page-heading"><div><p class="eyebrow"><span class="dot" /> Stock control</p><h1>Inventory, without the guesswork.</h1><p>Keep every item, variant, warehouse and reorder signal in one calm view.</p></div><div class="heading-actions"><Link href="/inventory/labels" class="btn btn-ghost"><Download :size="15" /> Labels</Link><Link href="/inventory/variants" class="btn btn-ghost">Variants</Link><Link href="/inventory/create" class="btn btn-primary"><Plus :size="15" /> Add product</Link></div></div>
    <div class="metric-strip"><div class="mini-metric"><span>Total products</span><strong>{{ metrics.total }}</strong><em>Across all branches</em></div><div class="mini-metric"><span>Low stock</span><strong>{{ metrics.lowStock }}</strong><em style="color:#b57e31">Needs attention</em></div><div class="mini-metric"><span>Stock value</span><strong>{{ metrics.stockValue }}</strong><em>+7.4% this month</em></div><div class="mini-metric"><span>Categories</span><strong>{{ metrics.categories }}</strong><em>4 new this quarter</em></div><div class="mini-metric"><span>Variants</span><strong>{{ metrics.variants }}</strong><em>Sellable options</em></div></div>
    <div class="toolbar"><div class="filter-tabs"><button class="active">All products</button><button>Low stock</button><button>Archived</button></div><form class="table-search" @submit.prevent="submitSearch"><Search :size="15" /><input name="search" :value="search" placeholder="Search SKU or product" /><button type="submit" style="display:none" /></form><button class="btn btn-ghost"><SlidersHorizontal :size="14" /> Filters</button></div>
    <section class="card"><div class="card-header"><div><h2>Product catalogue</h2><p>{{ products.length }} items in this view</p></div><button class="icon-button" style="color:#81908e"><MoreHorizontal :size="18" /></button></div><div class="table-scroll"><table class="data-table"><thead><tr><th>Product</th><th>Category</th><th>Price</th><th>Stock</th><th>Variant</th><th>Status</th><th /></tr></thead><tbody><tr v-for="product in products" :key="product.sku"><td><div class="data-title">{{ product.name }}</div><div class="data-subtitle">{{ product.sku }}</div></td><td>{{ product.category }}<div class="data-subtitle">{{ product.brand || 'Unbranded' }} · {{ product.unit }}</div></td><td><strong>{{ product.price }}</strong><div class="data-subtitle">{{ product.barcode || 'No barcode' }}</div></td><td><strong>{{ product.stock }}</strong><div class="data-subtitle">Reorder at {{ product.reorder }}</div></td><td>{{ product.variants }}</td><td><span class="status-pill" :class="{ warning: product.status === 'Low stock' }">{{ product.status }}</span></td><td><Link :href="`/inventory/${product.id}`" class="icon-button" style="color:#8c9997"><MoreHorizontal :size="17" /></Link></td></tr><tr v-if="!products.length"><td colspan="7" style="text-align:center;padding:38px">No products match this search.</td></tr></tbody></table></div></section>
</template>
