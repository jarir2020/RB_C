<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowRight, LockKeyhole, Sparkles } from 'lucide-vue-next';

const form = useForm({ loginEmail: 'admin@redbook.test', loginPassword: '', remember: true });
const submit = () => form.post('/admin/login');
</script>

<template>
    <Head title="Admin login" />
    <main class="auth-shell">
        <section class="auth-visual">
            <div class="auth-brand"><span class="brand-mark"><Sparkles :size="18" stroke-width="2.5" /></span><strong>redbook<span>one</span></strong></div>
            <div class="auth-story"><p class="eyebrow"><span class="dot" /> Business command centre</p><h1>Make every branch count.</h1><p>One calm workspace for the decisions, relationships and daily momentum behind your business.</p></div>
            <div class="auth-orbit"><span class="orbit-ring ring-one" /><span class="orbit-ring ring-two" /><span class="orbit-core"><Sparkles :size="26" /></span><span class="orbit-note note-one">Revenue</span><span class="orbit-note note-two">Stock</span><span class="orbit-note note-three">People</span></div>
            <small class="auth-footnote">A clearer view of the work that keeps everything moving.</small>
        </section>
        <section class="auth-panel">
            <div class="auth-card">
                <div class="auth-card-heading"><div class="auth-lock"><LockKeyhole :size="17" /></div><p class="eyebrow"><span class="dot" /> Secure workspace</p><h2>Welcome back.</h2><p>Sign in to continue to your Redbook workspace.</p></div>
                <form @submit.prevent="submit">
                    <div class="field"><label for="loginEmail">Work email</label><input id="loginEmail" v-model="form.loginEmail" type="email" autocomplete="email" placeholder="you@company.com" /><small v-if="form.errors.loginEmail" class="form-error">{{ form.errors.loginEmail }}</small></div>
                    <div class="field"><label for="loginPassword">Password</label><input id="loginPassword" v-model="form.loginPassword" type="password" autocomplete="current-password" placeholder="Enter your password" /><small v-if="form.errors.loginPassword" class="form-error">{{ form.errors.loginPassword }}</small></div>
                    <label class="remember-row"><input v-model="form.remember" type="checkbox" /> <span>Keep me signed in on this device</span></label>
                    <button class="btn btn-primary auth-submit" type="submit" :disabled="form.processing"><span>{{ form.processing ? 'Signing in…' : 'Enter workspace' }}</span><ArrowRight :size="16" /></button>
                </form>
                <p class="auth-help">Need access? <Link href="/register" class="card-link">Request a workspace</Link></p>
            </div>
        </section>
    </main>
</template>
