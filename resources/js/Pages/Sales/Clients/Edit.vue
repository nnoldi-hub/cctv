<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import ClientForm from './Partials/ClientForm.vue';

const props = defineProps({
    client: Object,
    users: Array,
});

const form = useForm({
    name: props.client.name,
    company_name: props.client.company_name,
    email: props.client.email,
    phone: props.client.phone,
    address: props.client.address,
    city: props.client.city,
    county: props.client.county,
    source: props.client.source,
    status: props.client.status,
    assigned_to: props.client.assigned_to,
    notes: props.client.notes,
});

function submit() {
    form.put(route('sales.clients.update', props.client.id));
}
</script>

<template>
    <Head title="Editeaza client" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Editeaza client</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <form class="space-y-6 rounded-lg bg-white p-6 shadow-sm" @submit.prevent="submit">
                    <ClientForm :form="form" :users="users" />
                    <div class="flex justify-end gap-3">
                        <button type="submit" :disabled="form.processing" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500 disabled:opacity-50">
                            Salveaza modificarile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
