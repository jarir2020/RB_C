<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Boxes } from 'lucide-vue-next';
import AppLayout from '../../Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
const props = defineProps({ branches: Array });
const form = useForm({ name: '', code: '', branch_id: props.branches?.[0]?.id || '', capacity: '' });
const submit = () => form.post('/warehouses');
</script>

<template>
    <Head title="Add warehouse" />
    <div class="page-heading"><div><p class="eyebrow"><span class="dot" /> Stock network</p><h1>Give inventory room to breathe.</h1><p>Register a storage point and attach it to the branch that owns the stock movement.</p></div><Link href="/warehouses" class="btn btn-ghost"><ArrowLeft :size="15" /> Back to warehouses</Link></div>
    <section class="card form-card"><div class="card-header" style="padding:0 0 20px"><div><h2>Warehouse details</h2><p>Utilization starts at zero and grows from future stock movements.</p></div></div><form class="form-grid" @submit.prevent="submit"><div class="field full"><label for="name">Warehouse name</label><input id="name" v-model="form.name" placeholder="Dhanmondi central store" /><small v-if="form.errors.name" class="form-error">{{ form.errors.name }}</small></div><div class="field"><label for="code">Warehouse code</label><input id="code" v-model="form.code" placeholder="WH-DHK-09" /><small v-if="form.errors.code" class="form-error">{{ form.errors.code }}</small></div><div class="field"><label for="branch_id">Owning branch</label><select id="branch_id" v-model="form.branch_id"><option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}</option></select><small v-if="form.errors.branch_id" class="form-error">{{ form.errors.branch_id }}</small></div><div class="field"><label for="capacity">Capacity (pallets)</label><input id="capacity" v-model="form.capacity" type="number" min="1" placeholder="800" /><small v-if="form.errors.capacity" class="form-error">{{ form.errors.capacity }}</small></div><div class="form-actions field full"><Link href="/warehouses" class="btn btn-ghost">Cancel</Link><button class="btn btn-primary" type="submit" :disabled="form.processing"><Boxes :size="15" /> {{ form.processing ? 'Saving…' : 'Add warehouse' }}</button></div></form></section>
</template>
