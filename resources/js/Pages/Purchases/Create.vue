<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, ClipboardPlus } from 'lucide-vue-next';
import AppLayout from '../../Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
const props = defineProps({ vendors: Array, branches: Array });
const form = useForm({ contact_id: props.vendors?.[0]?.id || '', branch_id: props.branches?.[0]?.id || '', total: '', items_count: 1 });
const submit = () => form.post('/purchase');
</script>
<template>
    <Head title="New purchase" />
    <div class="page-heading"><div><p class="eyebrow"><span class="dot" /> Supply workspace</p><h1>Start a purchase order.</h1><p>Capture the commitment now so receiving teams know what is coming.</p></div><Link href="/purchase" class="btn btn-ghost"><ArrowLeft :size="15" /> Back to purchases</Link></div>
    <section class="card form-card"><div class="card-header" style="padding:0 0 20px"><div><h2>Purchase order details</h2><p>New orders begin as drafts until the team approves them.</p></div></div><form class="form-grid" @submit.prevent="submit"><div class="field"><label for="vendor">Vendor</label><select id="vendor" v-model="form.contact_id"><option v-for="vendor in vendors" :key="vendor.id" :value="vendor.id">{{ vendor.name }}</option></select><small v-if="form.errors.contact_id" class="form-error">{{ form.errors.contact_id }}</small></div><div class="field"><label for="branch">Receiving branch</label><select id="branch" v-model="form.branch_id"><option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}</option></select><small v-if="form.errors.branch_id" class="form-error">{{ form.errors.branch_id }}</small></div><div class="field"><label for="total">Order total (BDT)</label><input id="total" v-model="form.total" type="number" min="1" step="0.01" placeholder="126400" /><small v-if="form.errors.total" class="form-error">{{ form.errors.total }}</small></div><div class="field"><label for="items_count">Number of items</label><input id="items_count" v-model="form.items_count" type="number" min="1" placeholder="18" /><small v-if="form.errors.items_count" class="form-error">{{ form.errors.items_count }}</small></div><div class="form-actions field full"><Link href="/purchase" class="btn btn-ghost">Cancel</Link><button class="btn btn-primary" type="submit" :disabled="form.processing"><ClipboardPlus :size="15" /> {{ form.processing ? 'Saving…' : 'Save draft order' }}</button></div></form></section>
</template>
