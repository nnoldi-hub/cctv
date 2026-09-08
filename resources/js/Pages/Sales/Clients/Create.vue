<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import ClientForm from './Partials/ClientForm.vue';

defineProps({
    users: Array,
    portalUsers: Array,
});

const form = useForm({
    name: '',
    company_name: '',
    email: '',
    phone: '',
    address: '',
    city: '',
    county: '',
    source: 'manual',
    status: 'lead',
    pipeline_stage: 'new',
    lost_reason: '',
    assigned_to: null,
    notes: '',
    portal_user_id: null,
    portal_role: 'client',
});

function submit() {
    form.post(route('sales.clients.store'));
}
</script>

<template>
    <Head title="Client nou" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Client nou</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <form class="space-y-6 rounded-lg bg-white p-6 shadow-sm" @submit.prevent="submit">
                    <ClientForm :form="form" :users="users" :portal-users="portalUsers" />
                    <div class="flex justify-end gap-3">
                        <button type="submit" :disabled="form.processing" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500 disabled:opacity-50">
                            Salveaza client
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
