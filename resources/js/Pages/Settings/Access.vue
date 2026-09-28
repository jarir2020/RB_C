<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { Check, ShieldCheck, UserPlus } from 'lucide-vue-next';
import AppLayout from '../../Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
const props = defineProps({ users: Array, roles: Array, permissions: Object });
const form = useForm({ name: '', email: '', role: 'Operator', password: '' });
const submit = () => form.post('/settings/access', { onSuccess: () => form.reset() });
</script>
<template>
    <Head title="Access control" />
    <div class="page-heading"><div><p class="eyebrow"><span class="dot" /> Workspace security</p><h1>Give people the right view.</h1><p>Keep access understandable with a small set of roles built around real work.</p></div></div>
    <div class="dashboard-grid"><section class="card"><div class="card-header"><div><h2>Workspace users</h2><p>{{ users.length }} people with current access</p></div></div><div class="table-scroll"><table class="data-table"><thead><tr><th>Person</th><th>Email</th><th>Role</th><th>Joined</th></tr></thead><tbody><tr v-for="user in users" :key="user.email"><td><div class="data-title">{{ user.name }}</div></td><td>{{ user.email }}</td><td><span class="status-pill neutral">{{ user.role }}</span></td><td>{{ user.joined }}</td></tr></tbody></table></div></section><section class="card form-card"><div class="card-header" style="padding:0 0 18px"><div><h2>Add teammate</h2><p>New users receive their role immediately.</p></div><div class="kpi-icon lime"><UserPlus :size="17" /></div></div><form class="form-grid" @submit.prevent="submit"><div class="field full"><label>Name</label><input v-model="form.name" placeholder="Team member" /></div><div class="field full"><label>Email</label><input v-model="form.email" type="email" placeholder="team@company.com" /></div><div class="field"><label>Role</label><select v-model="form.role"><option v-for="role in roles" :key="role">{{ role }}</option></select></div><div class="field"><label>Temporary password</label><input v-model="form.password" type="password" placeholder="At least 8 characters" /></div><div class="form-actions full"><button class="btn btn-primary" type="submit" :disabled="form.processing"><Check :size="15" /> Create user</button></div></form></section></div>
    <section class="card" style="margin-top:17px"><div class="card-header"><div><h2>Role map</h2><p>What each role can see and do</p></div><ShieldCheck :size="18" style="color:#7b941d" /></div><div class="role-grid"><article v-for="(items, role) in permissions" :key="role"><strong>{{ role }}</strong><span v-for="item in items" :key="item">{{ item }}</span></article></div></section>
</template>
