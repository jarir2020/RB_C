<script setup>
import { reactive } from 'vue';
import { Head } from '@inertiajs/vue3';
import { Building2, Check, Globe2 } from 'lucide-vue-next';
import AppLayout from '../../Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
const props = defineProps({ company: Object, preferences: Object });
const preferenceState = reactive({ ...props.preferences });
</script>

<template>
    <Head title="Settings" />
    <div class="page-heading"><div><p class="eyebrow"><span class="dot" /> Workspace settings</p><h1>Make the system feel like yours.</h1><p>Company context, operational preferences and the small details that make daily work calmer.</p></div><button class="btn btn-primary"><Check :size="15" /> Changes saved</button></div>
    <div class="settings-grid"><section class="card setting-card"><div class="kpi-icon lime"><Building2 :size="17" /></div><h2 style="margin-top:16px">Company profile</h2><p>The identity your teams see across invoices and reports.</p><div class="detail-row"><span>Company</span><strong>{{ company.name }}</strong></div><div class="detail-row"><span>Base currency</span><strong>{{ company.currency }}</strong></div><div class="detail-row"><span>Timezone</span><strong>{{ company.timezone }}</strong></div><div class="detail-row"><span>Branches</span><strong>{{ company.branches }} active locations</strong></div></section><section class="card setting-card"><div class="kpi-icon blue"><Globe2 :size="17" /></div><h2 style="margin-top:16px">Operating preferences</h2><p>These settings shape how your team records work.</p><div v-for="(value, label) in preferences" :key="label" class="toggle-row"><div class="toggle-copy"><strong>{{ label }}</strong><span>{{ label === 'Low-stock alerts' ? 'Notify branch managers before items run out.' : 'Apply this rule across the workspace.' }}</span></div><button class="toggle" :class="{ on: value }" @click="preferenceState[label] = !preferenceState[label]"><i /></button></div></section></div>
</template>
