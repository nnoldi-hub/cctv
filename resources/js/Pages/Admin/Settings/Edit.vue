<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
    settings: Object,
});

const page = usePage();

const form = useForm({
    company_name: props.settings.company_name,
    company_email: props.settings.company_email,
    company_phone: props.settings.company_phone,
    company_address: props.settings.company_address,
    company_hours: props.settings.company_hours,
    social_facebook: props.settings.social_facebook,
    social_instagram: props.settings.social_instagram,
    social_linkedin: props.settings.social_linkedin,
    invoice_series: props.settings.invoice_series,
    vat_percentage: Number(props.settings.vat_percentage),
    minimum_profit_margin: Number(props.settings.minimum_profit_margin),
});

function submit() {
    form.put(route('admin.settings.update'));
}
</script>

<template>
    <Head title="Setari generale" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Setari generale</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div v-if="page.props.flash.success" class="mb-4 rounded-md bg-green-50 p-4 text-sm font-medium text-green-800">
                    {{ page.props.flash.success }}
                </div>

                <form class="space-y-6 rounded-lg bg-white p-6 shadow-sm" @submit.prevent="submit">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-slate-700">Nume firma *</label>
                            <input v-model="form.company_name" type="text" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                            <p v-if="form.errors.company_name" class="mt-1 text-sm text-red-600">{{ form.errors.company_name }}</p>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-slate-700">Program *</label>
                            <input v-model="form.company_hours" type="text" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                        </div>
                        <div class="sm:col-span-2 border-t border-slate-200 pt-5">
                            <h3 class="text-sm font-semibold text-slate-900">Retele sociale</h3>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Facebook</label>
                            <input v-model="form.social_facebook" type="url" placeholder="https://facebook.com/..." class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Instagram</label>
                            <input v-model="form.social_instagram" type="url" placeholder="https://instagram.com/..." class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">LinkedIn</label>
                            <input v-model="form.social_linkedin" type="url" placeholder="https://linkedin.com/..." class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Email firma *</label>
                            <input v-model="form.company_email" type="email" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Telefon firma *</label>
                            <input v-model="form.company_phone" type="text" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-slate-700">Adresa firma</label>
                            <input v-model="form.company_address" type="text" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Serie facturi *</label>
                            <input v-model="form.invoice_series" type="text" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">TVA (%) *</label>
                            <input v-model.number="form.vat_percentage" type="number" min="0" max="100" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Marja minima profit (%)</label>
                            <input v-model.number="form.minimum_profit_margin" type="number" min="0" max="100" step="0.01" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm" />
                            <p class="mt-1 text-xs text-slate-500">Pragul pentru avertizarea ofertelor cu profit redus.</p>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" :disabled="form.processing" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500 disabled:opacity-50">
                            Salveaza setarile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
