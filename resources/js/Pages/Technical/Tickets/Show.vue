<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';

const props = defineProps({
    ticket: Object,
});

const statusOptions = [
    { value: 'open', label: 'Deschis' },
    { value: 'in_progress', label: 'In lucru' },
    { value: 'resolved', label: 'Rezolvat' },
    { value: 'closed', label: 'Inchis' },
];

const statusClasses = {
    open: 'bg-blue-100 text-blue-700',
    in_progress: 'bg-amber-100 text-amber-800',
    resolved: 'bg-green-100 text-green-800',
    closed: 'bg-slate-100 text-slate-500',
};

const priorityClasses = {
    low: 'bg-slate-100 text-slate-600',
    medium: 'bg-amber-100 text-amber-800',
    high: 'bg-red-100 text-red-700',
};

function setStatus(status) {
    router.patch(route('technical.tickets.status', props.ticket.id), { status }, { preserveScroll: true });
}

const commentForm = useForm({ body: '' });

function submitComment() {
    commentForm.post(route('technical.tickets.comments', props.ticket.id), {
        preserveScroll: true,
        onSuccess: () => commentForm.reset(),
    });
}
</script>

<template>
    <Head :title="ticket.subject" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Tichet #{{ ticket.id }} - {{ ticket.subject }}</h2>
                <Link :href="route('technical.tickets.edit', ticket.id)" class="rounded-md border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Editeaza
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto grid max-w-5xl grid-cols-1 gap-6 sm:px-6 lg:grid-cols-3 lg:px-8">
                <div class="rounded-lg bg-white p-6 shadow-sm lg:col-span-1">
                    <h3 class="text-sm font-semibold text-slate-500">Client</h3>
                    <Link :href="route('sales.clients.show', ticket.client.id)" class="mt-2 block font-medium text-blue-600 hover:text-blue-500">
                        {{ ticket.client.name }}
                    </Link>

                    <h3 class="mt-6 text-sm font-semibold text-slate-500">Prioritate</h3>
                    <span class="mt-2 inline-block rounded-full px-2 py-1 text-xs font-medium" :class="priorityClasses[ticket.priority]">
                        {{ ticket.priority }}
                    </span>

                    <h3 class="mt-6 text-sm font-semibold text-slate-500">Status</h3>
                    <span class="mt-2 inline-block rounded-full px-2 py-1 text-xs font-medium" :class="statusClasses[ticket.status]">
                        {{ statusOptions.find((s) => s.value === ticket.status)?.label }}
                    </span>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <button
                            v-for="option in statusOptions"
                            :key="option.value"
                            :disabled="ticket.status === option.value"
                            class="rounded-md border border-slate-300 px-2.5 py-1 text-xs font-medium text-slate-600 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
                            @click="setStatus(option.value)"
                        >
                            {{ option.label }}
                        </button>
                    </div>

                    <h3 class="mt-6 text-sm font-semibold text-slate-500">Asignat</h3>
                    <p class="mt-2 text-sm text-slate-900">{{ ticket.assigned_to?.name ?? 'Neasignat' }}</p>

                    <div v-if="ticket.installation" class="mt-6">
                        <h3 class="text-sm font-semibold text-slate-500">Instalare asociata</h3>
                        <Link :href="route('technical.installations.show', ticket.installation.id)" class="mt-2 block text-sm text-blue-600 hover:text-blue-500">
                            Vezi instalarea #{{ ticket.installation.id }}
                        </Link>
                    </div>
                </div>

                <div class="space-y-6 lg:col-span-2">
                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <h3 class="text-sm font-semibold text-slate-500">Descriere</h3>
                        <p class="mt-2 whitespace-pre-line text-sm text-slate-700">{{ ticket.description }}</p>
                    </div>

                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <h3 class="text-sm font-semibold text-slate-500">Comentarii ({{ ticket.comments.length }})</h3>
                        <div class="mt-4 space-y-4">
                            <div v-for="comment in ticket.comments" :key="comment.id" class="rounded-md bg-slate-50 p-3">
                                <div class="flex items-center justify-between text-xs text-slate-400">
                                    <span class="font-medium text-slate-700">{{ comment.user.name }}</span>
                                    <span>{{ new Date(comment.created_at).toLocaleString('ro-RO') }}</span>
                                </div>
                                <p class="mt-1 whitespace-pre-line text-sm text-slate-700">{{ comment.body }}</p>
                            </div>
                            <p v-if="!ticket.comments.length" class="text-sm text-slate-400">Niciun comentariu inca.</p>
                        </div>

                        <form class="mt-4 space-y-2" @submit.prevent="submitComment">
                            <textarea v-model="commentForm.body" rows="3" placeholder="Adauga un comentariu..." class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                            <div class="flex justify-end">
                                <button type="submit" :disabled="commentForm.processing" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500 disabled:opacity-50">
                                    Trimite comentariu
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
