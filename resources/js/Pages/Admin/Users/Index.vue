<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';

defineProps({
    users: Object,
    roles: Array,
});

const page = usePage();

const roleClasses = {
    admin: 'bg-purple-100 text-purple-800',
    vanzari: 'bg-blue-100 text-blue-700',
    tehnic: 'bg-amber-100 text-amber-800',
    suport: 'bg-slate-100 text-slate-600',
};

function destroy(user) {
    if (confirm(`Stergi utilizatorul "${user.name}"?`)) {
        router.delete(route('admin.users.destroy', user.id));
    }
}
</script>

<template>
    <Head title="Utilizatori" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Utilizatori</h2>
                <Link :href="route('admin.users.create')" class="rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-500">
                    Utilizator nou
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
                <div class="rounded-lg bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-500">Nume</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-500">Email</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-500">Rol</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="user in users.data" :key="user.id" class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-medium text-slate-900">{{ user.name }}</td>
                                <td class="px-4 py-3 text-sm text-slate-600">{{ user.email }}</td>
                                <td class="px-4 py-3">
                                    <span
                                        v-for="role in user.roles"
                                        :key="role.id"
                                        class="rounded-full px-2 py-1 text-xs font-medium"
                                        :class="roleClasses[role.name]"
                                    >
                                        {{ role.name }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right text-sm">
                                    <Link :href="route('admin.users.edit', user.id)" class="text-slate-500 hover:text-slate-700">Editeaza</Link>
                                    <button
                                        v-if="user.id !== page.props.auth.user.id"
                                        class="ml-3 text-red-500 hover:text-red-700"
                                        @click="destroy(user)"
                                    >
                                        Sterge
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                </div>

                <div v-if="users.links.length > 3" class="mt-4 flex flex-wrap gap-2">
                    <Link
                        v-for="link in users.links"
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
