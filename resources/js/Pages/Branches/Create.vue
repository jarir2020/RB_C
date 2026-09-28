<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, GitBranch } from 'lucide-vue-next';
import AppLayout from '../../Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
const form = useForm({ name: '', code: '', city: '', sales_target: '' });
const submit = () => form.post('/branches');
</script>

<template>
    <Head title="Add branch" />
    <div class="page-heading"><div><p class="eyebrow"><span class="dot" /> Network setup</p><h1>Give a new location a clear start.</h1><p>Set the operating context now; sales and reporting will follow the branch as soon as its first records arrive.</p></div><Link href="/branches" class="btn btn-ghost"><ArrowLeft :size="15" /> Back to branches</Link></div>
    <section class="card form-card"><div class="card-header" style="padding:0 0 20px"><div><h2>Branch details</h2><p>Use a short code your team will recognise in reports.</p></div></div><form class="form-grid" @submit.prevent="submit"><div class="field full"><label for="name">Branch name</label><input id="name" v-model="form.name" placeholder="Dhanmondi flagship" /><small v-if="form.errors.name" class="form-error">{{ form.errors.name }}</small></div><div class="field"><label for="code">Branch code</label><input id="code" v-model="form.code" placeholder="DHK-09" /><small v-if="form.errors.code" class="form-error">{{ form.errors.code }}</small></div><div class="field"><label for="city">City</label><input id="city" v-model="form.city" placeholder="Dhaka" /><small v-if="form.errors.city" class="form-error">{{ form.errors.city }}</small></div><div class="field"><label for="sales_target">Monthly sales target (BDT)</label><input id="sales_target" v-model="form.sales_target" type="number" min="0" step="0.01" placeholder="950000" /><small v-if="form.errors.sales_target" class="form-error">{{ form.errors.sales_target }}</small></div><div class="form-actions field full"><Link href="/branches" class="btn btn-ghost">Cancel</Link><button class="btn btn-primary" type="submit" :disabled="form.processing"><GitBranch :size="15" /> {{ form.processing ? 'Saving…' : 'Add branch' }}</button></div></form></section>
</template>
