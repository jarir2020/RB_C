<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ArrowRight, BarChart3, Boxes, Check, GitBranch, Sparkles } from 'lucide-vue-next';

defineProps({ stats: Array });
</script>

<template>
    <Head title="Redbook one" />
    <main class="public-shell">
        <nav class="public-nav"><Link href="/" class="public-brand"><span class="brand-mark"><Sparkles :size="18" /></span><strong>redbook<span>one</span></strong></Link><div class="public-nav-links"><a href="#platform">Platform</a><a href="#signals">Signals</a><Link href="/admin/login">Sign in</Link><Link href="/register" class="btn btn-primary">Start a workspace <ArrowRight :size="14" /></Link></div></nav>
        <section class="public-hero"><div class="public-hero-copy"><p class="eyebrow"><span class="dot" /> Business command centre</p><h1>Make every branch count.</h1><p>Redbook one brings stock, sales, finance and people into one clear operating picture—so your next decision feels lighter.</p><div class="public-hero-actions"><Link href="/register" class="btn btn-primary">Build your workspace <ArrowRight :size="15" /></Link><Link href="/admin/login" class="btn btn-ghost">Explore the demo</Link></div><div class="public-proof"><Check :size="15" /> Built for growing teams with more than one place to be.</div></div><div class="public-orbit"><div class="public-orbit-ring ring-a" /><div class="public-orbit-ring ring-b" /><div class="public-dashboard"><div class="dashboard-top"><span>Redbook pulse</span><b>↗ 18.6%</b></div><strong>৳ 4.82M</strong><small>tracked revenue</small><div class="dashboard-bars"><i /><i /><i /><i /><i /><i /><i /></div><div class="dashboard-bottom"><span><Boxes :size="14" /> 128 units in motion</span><span><GitBranch :size="14" /> 08 branches</span></div></div></div></section>
        <section id="signals" class="public-stats"><div v-for="stat in stats" :key="stat.label"><strong>{{ stat.value }}</strong><span>{{ stat.label }}</span></div></section>
        <section id="platform" class="public-section"><div class="section-heading"><p class="eyebrow"><span class="dot" /> One view, many moving parts</p><h2>Clarity that travels with the work.</h2><p>From the first purchase order to the final branch report, every movement leaves a useful trail.</p></div><div class="public-feature-grid"><article><div class="feature-icon lime"><Boxes :size="18" /></div><h3>Inventory that knows context</h3><p>Variants, branches, stock movements and reorder signals stay connected.</p></article><article><div class="feature-icon blue"><BarChart3 :size="18" /></div><h3>Finance with a pulse</h3><p>Ledgers, payments, cheques and reports use the same living numbers.</p></article><article><div class="feature-icon violet"><GitBranch :size="18" /></div><h3>Operations that feel human</h3><p>Give every team a focused workspace without losing the bigger picture.</p></article></div></section>
        <section class="public-cta"><div><p class="eyebrow"><span class="dot" /> Ready when you are</p><h2>Make the next branch easier.</h2></div><Link href="/register" class="btn btn-primary">Start your workspace <ArrowRight :size="15" /></Link></section>
        <footer class="public-footer"><span>© 2026 Redbook one</span><span>Calm systems for growing businesses.</span></footer>
    </main>
</template>
