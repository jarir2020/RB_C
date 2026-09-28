<script setup>
import { computed, ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import {
    BarChart3, Bell, ChevronDown, CircleHelp, GitBranch, LayoutDashboard,
    Landmark, Menu, Package, PanelLeftClose, PanelLeftOpen, ReceiptText,
    Search, Settings2, ShoppingCart, Sparkles, Users, X,
} from 'lucide-vue-next';

const page = usePage();
const mobileOpen = ref(false);
const collapsed = ref(false);

const iconMap = { LayoutDashboard, Package, ShoppingCart, ReceiptText, Landmark, BarChart3, Users, Settings2, GitBranch };
const groups = [
    { label: 'Workspace', items: [
        { label: 'Overview', href: '/dashboard', icon: 'LayoutDashboard' },
        { label: 'Inventory', href: '/inventory', icon: 'Package', badge: '6' },
        { label: 'Sales', href: '/sales', icon: 'ShoppingCart' },
        { label: 'POS checkout', href: '/pos', icon: 'ShoppingCart' },
        { label: 'Purchases', href: '/purchase', icon: 'ReceiptText' },
    ] },
    { label: 'Operations', items: [
        { label: 'Branches', href: '/branches', icon: 'GitBranch' },
        { label: 'Employees', href: '/employees', icon: 'Users' },
        { label: 'Warehouses', href: '/warehouses', icon: 'Package' },
        { label: 'Transfers', href: '/transfers', icon: 'GitBranch' },
        { label: 'Production', href: '/production', icon: 'Package' },
    ] },
    { label: 'Finance', items: [
        { label: 'Accounting', href: '/account', icon: 'Landmark' },
        { label: 'Ledger', href: '/account/ledger', icon: 'ReceiptText' },
        { label: 'Invoices', href: '/invoices', icon: 'ReceiptText' },
        { label: 'Payments', href: '/payments', icon: 'ShoppingCart' },
        { label: 'Returns', href: '/returns', icon: 'ReceiptText' },
        { label: 'Banking', href: '/banking', icon: 'Landmark' },
        { label: 'Cheques', href: '/cheques', icon: 'ReceiptText' },
        { label: 'Cost centres', href: '/cost-centres', icon: 'BarChart3' },
    ] },
    { label: 'Insights', items: [
        { label: 'Reports', href: '/reports', icon: 'BarChart3' },
        { label: 'Customers & vendors', href: '/contacts', icon: 'Users' },
    ] },
];

const currentPath = computed(() => page.url.split('?')[0]);
const isActive = (href) => currentPath.value === href.split('?')[0];
const userInitials = computed(() => (page.props.auth?.user?.name || 'AM').split(' ').map((part) => part[0]).slice(0, 2).join('').toUpperCase());
const logout = () => router.post('/admin/logout');
const pageTitle = computed(() => ({
    '/dashboard': 'Overview',
    '/inventory': 'Inventory',
    '/inventory/create': 'Add product',
    '/inventory/edit': 'Edit product',
    '/sales': 'Sales',
    '/pos': 'POS checkout',
    '/sales/create': 'New sale',
    '/purchase': 'Purchases',
    '/purchase/create': 'New purchase',
    '/account': 'Accounting',
    '/account/ledger': 'Ledger',
    '/account/ledger/create': 'New voucher',
    '/invoices': 'Invoices',
    '/payments': 'Payments',
    '/payments/create': 'Record payment',
    '/returns': 'Returns',
    '/returns/create': 'Record return',
    '/transfers': 'Stock transfers',
    '/transfers/create': 'Create transfer',
    '/branches': 'Branches',
    '/branches/create': 'Add branch',
    '/employees': 'Employees',
    '/employees/create': 'Add employee',
    '/warehouses': 'Warehouses',
    '/warehouses/create': 'Add warehouse',
    '/contacts': 'Customers & vendors',
    '/contacts/create': 'Add contact',
    '/reports': 'Reports',
    '/settings': 'Settings',
    '/settings/access': 'Access control',
    '/banking': 'Banking',
    '/cheques': 'Cheques',
    '/cost-centres': 'Cost centres',
    '/production': 'Production',
    '/production/create': 'Plan production',
    '/inventory/variants': 'Product variants',
    '/inventory/variants/create': 'Add variant',
    '/inventory/labels': 'Price labels',
}[currentPath.value] || 'Workspace'));
</script>

<template>
    <div class="app-frame" :class="{ 'sidebar-collapsed': collapsed }">
        <div v-if="mobileOpen" class="mobile-scrim" @click="mobileOpen = false" />
        <aside class="app-sidebar" :class="{ 'is-open': mobileOpen }">
            <div class="brand-lockup">
                <div class="brand-mark"><Sparkles :size="18" stroke-width="2.5" /></div>
                <div v-if="!collapsed" class="brand-copy">
                    <strong>redbook<span>one</span></strong>
                    <small>business command centre</small>
                </div>
                <button class="icon-button sidebar-toggle" aria-label="Toggle sidebar" @click="collapsed = !collapsed">
                    <PanelLeftClose v-if="!collapsed" :size="17" />
                    <PanelLeftOpen v-else :size="17" />
                </button>
            </div>

            <div class="workspace-switcher">
                <div class="workspace-avatar">RH</div>
                <div v-if="!collapsed" class="workspace-copy"><strong>Redbook Holdings</strong><span>Dhaka · 8 branches</span></div>
                <ChevronDown v-if="!collapsed" :size="15" class="muted-icon" />
            </div>

            <nav class="sidebar-nav">
                <div v-for="group in groups" :key="group.label" class="nav-group">
                    <p v-if="!collapsed" class="nav-label">{{ group.label }}</p>
                    <Link v-for="item in group.items" :key="item.label" :href="item.href" class="nav-item" :class="{ active: isActive(item.href) }" @click="mobileOpen = false">
                        <component :is="iconMap[item.icon]" :size="18" stroke-width="1.8" />
                        <span v-if="!collapsed">{{ item.label }}</span>
                        <em v-if="item.badge && !collapsed">{{ item.label === 'Inventory' ? (page.props.navigation?.products ?? item.badge) : item.badge }}</em>
                    </Link>
                </div>
            </nav>

            <div class="sidebar-footer">
                <Link href="/settings" class="nav-item" :class="{ active: isActive('/settings') }" @click="mobileOpen = false"><Settings2 :size="18" /><span v-if="!collapsed">Settings</span></Link><Link href="/settings/access" class="nav-item" :class="{ active: isActive('/settings/access') }" @click="mobileOpen = false"><Users :size="18" /><span v-if="!collapsed">Access control</span></Link>
                <a href="#" class="nav-item"><CircleHelp :size="18" /><span v-if="!collapsed">Help centre</span></a>
                <div v-if="!collapsed" class="sidebar-promo">
                    <div class="promo-icon"><Sparkles :size="16" /></div>
                    <strong>Make every branch count.</strong>
                    <span>Unlock smarter decisions with Redbook intelligence.</span>
                    <a href="#">Explore insights <span>↗</span></a>
                </div>
            </div>
        </aside>

        <main class="app-main">
            <header class="topbar">
                <button class="mobile-menu icon-button" aria-label="Open navigation" @click="mobileOpen = true"><Menu :size="21" /></button>
                <div class="breadcrumbs"><span>Redbook Holdings</span><b>/</b><strong>{{ pageTitle }}</strong></div>
                <div class="topbar-actions">
                    <label class="global-search"><Search :size="17" /><input placeholder="Search anything" /><kbd>⌘ K</kbd></label>
                    <button class="icon-button notification-button" aria-label="Notifications"><Bell :size="18" /><i /></button>
                    <button class="profile-chip profile-button" type="button" title="Sign out" @click="logout"><div class="profile-avatar">{{ userInitials }}</div><span class="profile-name">{{ page.props.auth?.user?.name || 'Admin workspace' }}</span><ChevronDown :size="15" /></button>
                </div>
            </header>
            <div class="page-canvas">
                <div v-if="page.props.flash?.success" class="flash-success" role="status">{{ page.props.flash.success }}</div>
                <slot />
            </div>
        </main>
    </div>
</template>
