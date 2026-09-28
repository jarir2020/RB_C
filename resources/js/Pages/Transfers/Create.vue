<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, GitBranch } from 'lucide-vue-next';
import AppLayout from '../../Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
const props = defineProps({ products: Array, branches: Array });
const form = useForm({ product_id: props.products?.[0]?.id || '', from_branch_id: props.branches?.[0]?.id || '', to_branch_id: props.branches?.[1]?.id || props.branches?.[0]?.id || '', quantity: 1, transferred_on: new Date().toISOString().slice(0, 10), note: '' });
const selectedProduct = computed(() => props.products?.find((product) => product.id === Number(form.product_id)));
const sourceStock = computed(() => Number(selectedProduct.value?.stocks?.[String(form.from_branch_id)] || 0));
const submit = () => form.post('/transfers');
</script>

<template>
    <Head title="Create transfer" />
    <div class="page-heading"><div><p class="eyebrow"><span class="dot" /> Branch movement</p><h1>Send stock where it matters.</h1><p>Create a movement record with a clear origin, destination and handoff note.</p></div><Link href="/transfers" class="btn btn-ghost"><ArrowLeft :size="15" /> Back to transfers</Link></div>
    <section class="card form-card"><div class="card-header" style="padding:0 0 20px"><div><h2>Transfer details</h2><p>Receiving confirmation can be added as the movement lifecycle grows.</p></div></div><form class="form-grid" @submit.prevent="submit"><div class="field full"><label for="product_id">Product</label><select id="product_id" v-model="form.product_id"><option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }} · {{ product.stock }} units available</option></select><small v-if="form.errors.product_id" class="form-error">{{ form.errors.product_id }}</small></div><div class="field"><label for="from_branch_id">From branch</label><select id="from_branch_id" v-model="form.from_branch_id"><option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}</option></select><small v-if="form.errors.from_branch_id" class="form-error">{{ form.errors.from_branch_id }}</small></div><div class="field"><label for="to_branch_id">To branch</label><select id="to_branch_id" v-model="form.to_branch_id"><option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}</option></select><small v-if="form.errors.to_branch_id" class="form-error">{{ form.errors.to_branch_id }}</small></div><div class="field"><label for="quantity">Quantity</label><input id="quantity" v-model.number="form.quantity" type="number" min="1" /><small class="field-hint">{{ sourceStock }} units allocated at source</small><small v-if="form.errors.quantity" class="form-error">{{ form.errors.quantity }}</small></div><div class="field"><label for="transferred_on">Transfer date</label><input id="transferred_on" v-model="form.transferred_on" type="date" /><small v-if="form.errors.transferred_on" class="form-error">{{ form.errors.transferred_on }}</small></div><div class="field full"><label for="note">Handoff note (optional)</label><input id="note" v-model="form.note" placeholder="Fast-move replenishment" /><small v-if="form.errors.note" class="form-error">{{ form.errors.note }}</small></div><div class="form-actions field full"><Link href="/transfers" class="btn btn-ghost">Cancel</Link><button class="btn btn-primary" type="submit" :disabled="form.processing"><GitBranch :size="15" /> {{ form.processing ? 'Saving…' : 'Create transfer' }}</button></div></form></section>
</template>
