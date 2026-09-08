<script setup>
defineProps({
    form: Object,
    users: Array,
    portalUsers: Array,
});
</script>

<template>
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-slate-700">Nume *</label>
            <input v-model="form.name" type="text" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Firma</label>
            <input v-model="form.company_name" type="text" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Telefon</label>
            <input v-model="form.phone" type="text" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Email</label>
            <input v-model="form.email" type="email" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
            <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Adresa</label>
            <input v-model="form.address" type="text" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700">Oras</label>
                <input v-model="form.city" type="text" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Judet</label>
                <input v-model="form.county" type="text" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Sursa</label>
            <select v-model="form.source" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="manual">Manual</option>
                <option value="web">Web</option>
                <option value="phone">Telefon</option>
                <option value="referral">Recomandare</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Status</label>
            <select v-model="form.status" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="lead">Lead</option>
                <option value="client">Client</option>
                <option value="inactive">Inactiv</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Etapa pipeline</label>
            <select v-model="form.pipeline_stage" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm">
                <option value="new">Lead nou</option>
                <option value="contacted">Contactat</option>
                <option value="visit">Vizita programata</option>
                <option value="proposal">Oferta trimisa</option>
                <option value="won">Castigat</option>
                <option value="lost">Pierdut</option>
            </select>
        </div>
        <div v-if="form.pipeline_stage === 'lost'">
            <label class="block text-sm font-medium text-slate-700">Motiv pierdere</label>
            <input v-model="form.lost_reason" type="text" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm" />
        </div>
        <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-slate-700">Asignat catre</label>
            <select v-model="form.assigned_to" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option :value="null">Neasignat</option>
                <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
            </select>
        </div>
        <div v-if="portalUsers?.length" class="sm:col-span-2 rounded-md border border-blue-100 bg-blue-50 p-4">
            <h3 class="text-sm font-semibold text-blue-900">Acces Portal Client</h3>
            <p class="mt-1 text-xs text-blue-700">Asociaza un cont existent. Contul va vedea doar datele acestui client.</p>
            <div class="mt-3 grid gap-4 sm:grid-cols-2">
                <label class="text-sm font-medium text-slate-700">Cont portal
                    <select v-model="form.portal_user_id" class="mt-1 block w-full rounded-md border-slate-300">
                        <option :value="null">Fara acces portal</option>
                        <option v-for="user in portalUsers" :key="user.id" :value="user.id">{{ user.name }} ({{ user.email }})</option>
                    </select>
                </label>
                <label class="text-sm font-medium text-slate-700">Tip acces
                    <select v-model="form.portal_role" class="mt-1 block w-full rounded-md border-slate-300">
                        <option value="client">Client</option>
                        <option value="client-manager">Client Manager</option>
                    </select>
                </label>
            </div>
            <p v-if="form.errors.portal_user_id || form.errors.portal_role" class="mt-2 text-sm text-red-600">{{ form.errors.portal_user_id || form.errors.portal_role }}</p>
        </div>
        <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-slate-700">Notite</label>
            <textarea v-model="form.notes" rows="4" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
        </div>
    </div>
</template>
