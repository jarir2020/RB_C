<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Minus, Plus, ShoppingCart, Trash2 } from 'lucide-vue-next';
import AppLayout from '../../Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
const props = defineProps({ customers: Array, branches: Array, products: Array, paymentMethods: Array });
const form = useForm({ contact_id: props.customers?.[0]?.id || '', branch_id: props.branches?.[0]?.id || '', payment_method: 'Cash', items: [{ product_id: props.products?.[0]?.id || '', quantity: 1 }] });
const money = (value) => `৳ ${Number(value || 0).toLocaleString()}`;
const productFor = (id) => props.products?.find((product) => product.id === Number(id));
const stockFor = (product) => Number(product?.stocks?.[String(form.branch_id)] || 0);
const total = computed(() => form.items.reduce((sum, item) => { const product = productFor(item.product_id); return sum + (Number(product?.price || 0) * Number(item.quantity || 0)); }, 0));
const itemError = (index, field) => form.errors[`items.${index}.${field}`];
const addLine = () => form.items.push({ product_id: props.products?.[0]?.id || '', quantity: 1 });
const removeLine = (index) => { if (form.items.length > 1) form.items.splice(index, 1); };
const submit = () => form.post('/pos');
</script>

<template>
    <Head title="POS checkout" />
    <div class="page-heading"><div><p class="eyebrow"><span class="dot" /> Counter sales</p><h1>Keep checkout quick and human.</h1><p>Build a basket, take payment and let the stock record follow the sale automatically.</p></div><Link href="/sales" class="btn btn-ghost"><ArrowLeft :size="15" /> Back to sales</Link></div>
    <form class="pos-grid" @submit.prevent="submit">
        <section class="card pos-card"><div class="card-header" style="padding-bottom:18px"><div><h2>Basket</h2><p>Choose products and quantities for this checkout.</p></div><button class="btn btn-ghost" type="button" @click="addLine"><Plus :size="14" /> Add line</button></div><div class="pos-lines"><div v-for="(item, index) in form.items" :key="index" class="pos-line"><div class="pos-product"><label :for="`product-${index}`">Product</label><select :id="`product-${index}`" v-model="item.product_id"><option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }} · {{ product.sku }}</option></select><small v-if="itemError(index, 'product_id')" class="form-error">{{ itemError(index, 'product_id') }}</small></div><div class="pos-quantity"><label :for="`quantity-${index}`">Qty</label><div class="quantity-control"><button type="button" aria-label="Decrease quantity" @click="item.quantity = Math.max(1, item.quantity - 1)"><Minus :size="13" /></button><input :id="`quantity-${index}`" v-model.number="item.quantity" type="number" min="1" /><button type="button" aria-label="Increase quantity" @click="item.quantity++"><Plus :size="13" /></button></div><small v-if="itemError(index, 'quantity')" class="form-error">{{ itemError(index, 'quantity') }}</small></div><div class="pos-stock">{{ stockFor(productFor(item.product_id)) }} at branch</div><strong class="pos-line-total">{{ money((productFor(item.product_id)?.price || 0) * item.quantity) }}</strong><button v-if="form.items.length > 1" class="icon-button pos-remove" type="button" aria-label="Remove line" @click="removeLine(index)"><Trash2 :size="16" /></button></div></div><div v-if="form.errors['items.0.quantity']" class="alert-success pos-alert">{{ form.errors['items.0.quantity'] }}</div></section>
        <aside class="card pos-card pos-summary"><div class="card-header" style="padding-bottom:18px"><div><h2>Checkout details</h2><p>The server recalculates this total before saving.</p></div><div class="pos-cart-icon"><ShoppingCart :size="17" /></div></div><div class="pos-fields"><div class="field"><label for="customer">Customer</label><select id="customer" v-model="form.contact_id"><option v-for="customer in customers" :key="customer.id" :value="customer.id">{{ customer.name }}</option></select><small v-if="form.errors.contact_id" class="form-error">{{ form.errors.contact_id }}</small></div><div class="field"><label for="branch">Selling branch</label><select id="branch" v-model="form.branch_id"><option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}</option></select><small v-if="form.errors.branch_id" class="form-error">{{ form.errors.branch_id }}</small></div><div class="field"><label for="payment_method">Payment</label><select id="payment_method" v-model="form.payment_method"><option v-for="method in paymentMethods" :key="method">{{ method }}</option></select></div></div><div class="pos-total"><span>Checkout total</span><strong>{{ money(total) }}</strong></div><button class="btn btn-primary pos-submit" type="submit" :disabled="form.processing || !form.items.length"><ShoppingCart :size="16" /> {{ form.processing ? 'Completing…' : 'Complete checkout' }}</button><p class="pos-note">On account keeps the invoice open. Other methods create a completed payment automatically.</p></aside>
    </form>
</template>
