<script setup>
import SeoHead from '@/Components/SeoHead.vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { useForm, usePage } from '@inertiajs/vue3';

const page = usePage();
const params = new URLSearchParams(window.location.search);

const form = useForm({
    name: '',
    phone: '',
    email: '',
    city: '',
    notes: params.get('notes') ?? (params.get('package') ? `Interesat de pachetul: ${params.get('package')}` : ''),
    privacy_consent: false,
});

function submit() {
    form.post(route('public.lead.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset('name', 'phone', 'email', 'city', 'notes'),
    });
}
</script>

<template>
    <SeoHead
        title="Contact - cere o oferta"
        description="Contacteaza-ne pentru o oferta personalizata de instalare sistem de supraveghere video. Raspundem in cel mai scurt timp."
    />

    <PublicLayout>
        <section class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="font-display text-3xl font-bold text-slate-900">Cere o oferta</h1>
                <p class="mt-3 text-slate-500">
                    Completeaza formularul si te contactam in cel mai scurt timp pentru a stabili detaliile.
                </p>
            </div>

            <div
                v-if="page.props.flash.success"
                class="mt-8 rounded-md bg-green-50 p-4 text-sm font-medium text-green-800"
            >
                {{ page.props.flash.success }}
            </div>

            <form class="mt-10 space-y-6" @submit.prevent="submit">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Nume *</label>
                        <input v-model="form.name" type="text" required class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                    </div>
                    <label class="flex items-start gap-2 text-sm text-slate-600">
                        <input v-model="form.privacy_consent" type="checkbox" required class="mt-1 rounded border-slate-300 text-orange-500 focus:ring-orange-500" />
                        <span>Sunt de acord cu prelucrarea datelor conform <a :href="route('public.privacy')" class="text-blue-600 underline">Politicii de confidentialitate</a> si <a :href="route('public.terms')" class="text-blue-600 underline">Termenilor</a>.</span>
                    </label>
                    <p v-if="form.errors.privacy_consent" class="mt-1 text-sm text-red-600">{{ form.errors.privacy_consent }}</p>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Telefon *</label>
                        <input v-model="form.phone" type="text" required class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                        <p v-if="form.errors.phone" class="mt-1 text-sm text-red-600">{{ form.errors.phone }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Email</label>
                        <input v-model="form.email" type="email" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                        <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Oras</label>
                        <input v-model="form.city" type="text" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                        <p v-if="form.errors.city" class="mt-1 text-sm text-red-600">{{ form.errors.city }}</p>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Detalii (numar camere, tip proprietate, etc.)</label>
                    <textarea v-model="form.notes" rows="4" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    <p v-if="form.errors.notes" class="mt-1 text-sm text-red-600">{{ form.errors.notes }}</p>
                </div>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full rounded-md bg-orange-500 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-orange-400 disabled:opacity-50"
                >
                    Trimite cererea
                </button>
            </form>

            <div class="mt-12 grid grid-cols-1 gap-6 border-t border-slate-200 pt-8 text-sm text-slate-500 sm:grid-cols-4">
                <div>
                    <div class="font-semibold text-slate-900">Telefon</div>
                    {{ page.props.siteSettings?.company_phone || '0700 000 000' }}
                </div>
                <div>
                    <div class="font-semibold text-slate-900">Email</div>
                    {{ page.props.siteSettings?.company_email || 'contact@cctv-security.test' }}
                </div>
                <div>
                    <div class="font-semibold text-slate-900">Program</div>
                    {{ page.props.siteSettings?.company_hours || 'Luni - Vineri, 09:00 - 18:00' }}
                </div>
                <div>
                    <div class="font-semibold text-slate-900">Adresa</div>
                    {{ page.props.siteSettings?.company_address || 'Str. Petre Ionel nr. 205, Branesti, Ilfov, 077030' }}
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
