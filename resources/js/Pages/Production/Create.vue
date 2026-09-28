<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Factory, Save } from 'lucide-vue-next';
import AppLayout from '../../Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
const props = defineProps({ products: Array, branches: Array });
const form = useForm({ product_id: props.products?.[0]?.id || '', component_product_id: props.products?.[1]?.id || '', component_quantity: 1, branch_id: props.branches?.[0]?.id || '', planned_on: new Date().toISOString().slice(0, 10), output_quantity: 1, notes: '' });
const submit = () => form.post('/production');
</script>
<template>
    <Head title="Plan production" />
    <div class="page-heading"><div><p class="eyebrow"><span class="dot" /> Production control</p><h1>Plan a production run.</h1><p>Create a recipe and its output target; completion will consume component stock.</p></div><Link href="/production" class="btn btn-ghost"><ArrowLeft :size="15" /> Production</Link></div>
    <section class="card form-card"><div class="card-header" style="padding:0 0 20px"><div><h2>Production recipe</h2><p>This focused flow supports a finished product and one component per run.</p></div><div class="kpi-icon lime"><Factory :size="17" /></div></div><form class="form-grid" @submit.prevent="submit"><div class="field"><label>Finished product</label><select v-model="form.product_id"><option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }} · {{ product.sku }}</option></select></div><div class="field"><label>Component product</label><select v-model="form.component_product_id"><option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }} · {{ product.sku }}</option></select><small v-if="form.errors.component_product_id" class="form-error">{{ form.errors.component_product_id }}</small></div><div class="field"><label>Component units per output</label><input v-model="form.component_quantity" type="number" min="0.001" step="0.001" /></div><div class="field"><label>Output quantity</label><input v-model="form.output_quantity" type="number" min="1" /></div><div class="field"><label>Branch</label><select v-model="form.branch_id"><option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}</option></select></div><div class="field"><label>Planned date</label><input v-model="form.planned_on" type="date" /></div><div class="field full"><label>Notes</label><input v-model="form.notes" placeholder="Finishing run for branch replenishment" /></div><div class="form-actions full"><Link href="/production" class="btn btn-ghost">Cancel</Link><button class="btn btn-primary" type="submit" :disabled="form.processing"><Save :size="15" /> Plan order</button></div></form></section>
</template>
