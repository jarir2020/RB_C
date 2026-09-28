<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, UserRoundPlus } from 'lucide-vue-next';
import AppLayout from '../../Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
const form = useForm({ name: '', kind: 'Customer', email: '', phone: '' });
const submit = () => form.post('/contacts');
</script>
<template>
    <Head title="Add contact" />
    <div class="page-heading"><div><p class="eyebrow"><span class="dot" /> Relationship centre</p><h1>Add a relationship.</h1><p>Start with the information your sales and supply teams actually use.</p></div><Link href="/contacts" class="btn btn-ghost"><ArrowLeft :size="15" /> Back to contacts</Link></div>
    <section class="card form-card"><div class="card-header" style="padding:0 0 20px"><div><h2>Contact profile</h2><p>The balance will be created by future transactions, not manual guesses.</p></div></div><form class="form-grid" @submit.prevent="submit"><div class="field full"><label for="name">Name or company</label><input id="name" v-model="form.name" placeholder="Akhter Enterprise" /><small v-if="form.errors.name" class="form-error">{{ form.errors.name }}</small></div><div class="field"><label for="kind">Relationship type</label><select id="kind" v-model="form.kind"><option>Customer</option><option>Vendor</option></select><small v-if="form.errors.kind" class="form-error">{{ form.errors.kind }}</small></div><div class="field"><label for="phone">Phone</label><input id="phone" v-model="form.phone" placeholder="01700000000" /><small v-if="form.errors.phone" class="form-error">{{ form.errors.phone }}</small></div><div class="field full"><label for="email">Email</label><input id="email" v-model="form.email" type="email" placeholder="accounts@company.example" /><small v-if="form.errors.email" class="form-error">{{ form.errors.email }}</small></div><div class="form-actions field full"><Link href="/contacts" class="btn btn-ghost">Cancel</Link><button class="btn btn-primary" type="submit" :disabled="form.processing"><UserRoundPlus :size="15" /> {{ form.processing ? 'Saving…' : 'Save contact' }}</button></div></form></section>
</template>
