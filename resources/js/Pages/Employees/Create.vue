<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, UserPlus } from 'lucide-vue-next';
import AppLayout from '../../Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
const props = defineProps({ branches: Array });
const form = useForm({ name: '', role: '', email: '', phone: '', branch_id: props.branches?.[0]?.id || '', joined_on: '' });
const submit = () => form.post('/employees');
</script>

<template>
    <Head title="Add employee" />
    <div class="page-heading"><div><p class="eyebrow"><span class="dot" /> People operations</p><h1>Add someone to the flow.</h1><p>Give the team a useful starting point with role, branch and contact context attached.</p></div><Link href="/employees" class="btn btn-ghost"><ArrowLeft :size="15" /> Back to employees</Link></div>
    <section class="card form-card"><div class="card-header" style="padding:0 0 20px"><div><h2>Employee details</h2><p>Access roles and permissions can be added as the workspace grows.</p></div></div><form class="form-grid" @submit.prevent="submit"><div class="field full"><label for="name">Full name</label><input id="name" v-model="form.name" placeholder="Nusrat Jahan" /><small v-if="form.errors.name" class="form-error">{{ form.errors.name }}</small></div><div class="field"><label for="role">Role</label><input id="role" v-model="form.role" placeholder="Branch manager" /><small v-if="form.errors.role" class="form-error">{{ form.errors.role }}</small></div><div class="field"><label for="branch_id">Primary branch</label><select id="branch_id" v-model="form.branch_id"><option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}</option></select><small v-if="form.errors.branch_id" class="form-error">{{ form.errors.branch_id }}</small></div><div class="field"><label for="email">Work email</label><input id="email" v-model="form.email" type="email" placeholder="name@company.com" /><small v-if="form.errors.email" class="form-error">{{ form.errors.email }}</small></div><div class="field"><label for="phone">Phone</label><input id="phone" v-model="form.phone" placeholder="01700000000" /><small v-if="form.errors.phone" class="form-error">{{ form.errors.phone }}</small></div><div class="field"><label for="joined_on">Joined on</label><input id="joined_on" v-model="form.joined_on" type="date" /><small v-if="form.errors.joined_on" class="form-error">{{ form.errors.joined_on }}</small></div><div class="form-actions field full"><Link href="/employees" class="btn btn-ghost">Cancel</Link><button class="btn btn-primary" type="submit" :disabled="form.processing"><UserPlus :size="15" /> {{ form.processing ? 'Saving…' : 'Add employee' }}</button></div></form></section>
</template>
