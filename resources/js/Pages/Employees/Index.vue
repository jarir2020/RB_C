<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { Mail, Phone, Plus, Search, Users } from 'lucide-vue-next';
import AppLayout from '../../Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
const props = defineProps({ employees: Array, filters: Object, summary: Object });
const search = props.filters?.search || '';
const submitSearch = (event) => router.get('/employees', { search: event.target.search.value }, { preserveState: true, replace: true });
</script>

<template>
    <Head title="Employees" />
    <div class="page-heading"><div><p class="eyebrow"><span class="dot" /> People operations</p><h1>Keep good work close to the context.</h1><p>See who is moving each branch forward, with the role and location details that make the directory useful.</p></div><Link href="/employees/create" class="btn btn-primary"><Plus :size="15" /> Add employee</Link></div>
    <div class="metric-strip"><div class="mini-metric"><span>People in workspace</span><strong>{{ summary.total }}</strong><em>Across all teams</em></div><div class="mini-metric"><span>Active today</span><strong>{{ summary.active }}</strong><em>Ready for the day</em></div><div class="mini-metric"><span>Branches covered</span><strong>{{ summary.branches }}</strong><em>Local context attached</em></div><div class="mini-metric"><span>Distinct roles</span><strong>{{ summary.roles }}</strong><em>Clear ownership</em></div></div>
    <div class="toolbar"><div><span class="toolbar-caption">People directory</span></div><form class="table-search" @submit.prevent="submitSearch"><Search :size="15" /><input name="search" :value="search" placeholder="Find a person or role" /></form></div>
    <section class="card"><div class="table-scroll"><table class="data-table"><thead><tr><th>Person</th><th>Role</th><th>Branch</th><th>Contact</th><th>Joined</th><th>Status</th></tr></thead><tbody><tr v-for="employee in employees" :key="employee.email"><td><div class="person-cell"><span class="person-avatar">{{ employee.name.split(' ').map(part => part[0]).slice(0, 2).join('') }}</span><div><div class="data-title">{{ employee.name }}</div><div class="data-subtitle">Workspace member</div></div></div></td><td>{{ employee.role }}</td><td>{{ employee.branch }}</td><td><div class="contact-stack"><span><Mail :size="12" /> {{ employee.email }}</span><span><Phone :size="12" /> {{ employee.phone }}</span></div></td><td>{{ employee.joined }}</td><td><span class="status-pill">{{ employee.status }}</span></td></tr><tr v-if="!employees.length"><td colspan="6" class="table-empty">No people match this search.</td></tr></tbody></table></div></section>
</template>
