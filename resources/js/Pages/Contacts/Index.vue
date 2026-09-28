<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { Search, UserRoundPlus } from 'lucide-vue-next';
import AppLayout from '../../Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
const props = defineProps({ contacts: Array, filters: Object, counts: Object });
const filter = (kind, search = props.filters.search) => router.get('/contacts', { kind, search }, { preserveState: true, replace: true });
const submitSearch = (event) => filter(props.filters.kind, event.target.search.value);
</script>
<template>
    <Head title="Contacts" />
    <div class="page-heading"><div><p class="eyebrow"><span class="dot" /> Relationship centre</p><h1>People behind the numbers.</h1><p>Customers and vendors are more useful when their commercial context stays attached.</p></div><Link href="/contacts/create" class="btn btn-primary"><UserRoundPlus :size="15" /> Add contact</Link></div>
    <div class="metric-strip"><div class="mini-metric"><span>Customers</span><strong>{{ counts.customers }}</strong><em>Active relationships</em></div><div class="mini-metric"><span>Vendors</span><strong>{{ counts.vendors }}</strong><em>Supply network</em></div><div class="mini-metric"><span>Outstanding</span><strong>৳ 682K</strong><em style="color:#b57e31">Collection watch</em></div><div class="mini-metric"><span>Data quality</span><strong>98%</strong><em>Profiles complete</em></div></div>
    <div class="toolbar"><div class="filter-tabs"><button :class="{ active: !filters.kind }" @click="filter('')">All</button><button :class="{ active: filters.kind === 'Customer' }" @click="filter('Customer')">Customers</button><button :class="{ active: filters.kind === 'Vendor' }" @click="filter('Vendor')">Vendors</button></div><form class="table-search" @submit.prevent="submitSearch"><Search :size="15" /><input name="search" :value="filters.search" placeholder="Find a person or company" /></form></div>
    <section class="card"><div class="table-scroll"><table class="data-table"><thead><tr><th>Name</th><th>Type</th><th>Email</th><th>Phone</th><th>Balance</th></tr></thead><tbody><tr v-for="contact in contacts" :key="contact.name"><td><div class="data-title">{{ contact.name }}</div><div class="data-subtitle">Relationship profile</div></td><td><span class="status-pill" :class="{ neutral: contact.kind === 'Vendor' }">{{ contact.kind }}</span></td><td>{{ contact.email }}</td><td>{{ contact.phone }}</td><td><strong>{{ contact.balance }}</strong></td></tr></tbody></table></div></section>
</template>
