<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive, watch } from 'vue';

const props = defineProps({
    logs: Object,
    filters: Object,
    driver: String,
});

const form = reactive({ status: props.filters.status ?? '' });
watch(form, () => {
    router.get(route('admin.sms-logs'), form, { preserveState: true, replace: true });
});

const statusClasses = {
    sent: 'bg-green-100 text-green-800',
    failed: 'bg-red-100 text-red-700',
};
</script>

<template>
    <Head title="Log SMS" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Log notificari SMS</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
                <div class="mb-4 rounded-md border border-blue-200 bg-blue-50 p-3 text-sm text-blue-800">
                    Driver activ: <strong>{{ driver }}</strong>.
                    <span v-if="driver === 'log'">
                        Niciun provider SMS real configurat - mesajele sunt inregistrate aici, dar nu sunt trimise efectiv.
                        Seteaza <code>SMS_DRIVER=http</code> si <code>SMS_API_URL</code>/<code>SMS_API_KEY</code> in .env pentru trimitere reala.
                    </span>
                </div>

                <div class="mb-4">
                    <select v-model="form.status" class="rounded-md border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Toate statusurile</option>
                        <option value="sent">Trimis</option>
                        <option value="failed">Esuat</option>
                    </select>
                </div>

                <div class="rounded-lg bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-500">Telefon</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-500">Mesaj</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-500">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-500">Data</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="log in logs.data" :key="log.id" class="hover:bg-slate-50">
                                <td class="px-4 py-3 text-sm text-slate-900">{{ log.phone }}</td>
                                <td class="px-4 py-3 text-sm text-slate-600">{{ log.message }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full px-2 py-1 text-xs font-medium" :class="statusClasses[log.status]">{{ log.status }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-500">{{ new Date(log.created_at).toLocaleString('ro-RO') }}</td>
                            </tr>
                            <tr v-if="!logs.data.length">
                                <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-400">Niciun SMS trimis inca.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                </div>

                <div v-if="logs.links.length > 3" class="mt-4 flex flex-wrap gap-2">
                    <Link
                        v-for="link in logs.links"
                        :key="link.label"
                        :href="link.url ?? '#'"
                        :class="[
                            'rounded-md px-3 py-1.5 text-sm',
                            link.active ? 'bg-blue-600 text-white' : 'bg-white text-slate-500 hover:bg-slate-100',
                            !link.url ? 'pointer-events-none opacity-40' : '',
                        ]"
                        v-html="link.label"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
