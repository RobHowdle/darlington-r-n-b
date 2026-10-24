<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

type Contact = { name: string; phone: string; email: string };
type ClubSettings = {
    booking_title: string; booking_text: string; contacts: Contact[];
    location_title: string; address: string; phone: string;
    contact_url: string; facebook_url: string; instagram_url: string;
};
const props = defineProps<{ settings: ClubSettings }>();
const form = useForm({ ...props.settings, contacts: props.settings.contacts.map(c => ({ ...c })) });
const keys = ref(form.contacts.map((_, index) => index));
let nextKey = keys.value.length;
const saved = ref(false);
const venueFields = [
    { key: 'location_title', label: 'Location heading', type: 'text' },
    { key: 'phone', label: 'Venue phone', type: 'tel' },
    { key: 'contact_url', label: 'Venue contact page URL', type: 'url' },
    { key: 'facebook_url', label: 'Facebook URL', type: 'url' },
    { key: 'instagram_url', label: 'Instagram URL', type: 'url' },
] as const;
function addContact() {
    form.contacts.push({ name: '', phone: '', email: '' });
    keys.value.push(nextKey++);
}
function removeContact(index: number) {
    form.contacts.splice(index, 1);
    keys.value.splice(index, 1);
}
function move(index: number, offset: number) {
    const next = index + offset;
    if (next < 0 || next >= form.contacts.length) return;
    const [contact] = form.contacts.splice(index, 1);
    const [key] = keys.value.splice(index, 1);
    form.contacts.splice(next, 0, contact);
    keys.value.splice(next, 0, key);
}
function save() {
    saved.value = false;
    form.put('/admin/club-settings', { onSuccess: () => { form.defaults(); saved.value = true; } });
}
</script>

<template>
    <Head title="Club details" />
    <div class="max-w-3xl space-y-6 p-6">
        <div class="flex items-center justify-between gap-4">
            <h1 class="text-2xl font-semibold">Club details</h1>
            <Link href="/admin" class="underline">Dashboard</Link>
        </div>
        <form class="space-y-6" @submit.prevent="save">
            <div v-if="Object.keys(form.errors).length" role="alert" class="rounded border border-red-700 p-4 text-red-700">
                <p v-for="(error, name) in form.errors" :key="name">{{ error }}</p>
            </div>
            <section class="space-y-4 rounded border p-4">
                <h2 class="text-xl font-semibold">Booking enquiries</h2>
                <label class="block">Heading<input v-model="form.booking_title" required class="block w-full rounded border p-2" /></label>
                <label class="block">Introduction<textarea v-model="form.booking_text" rows="3" class="block w-full rounded border p-2" /></label>
                <div v-for="(contact, index) in form.contacts" :key="keys[index]" class="space-y-3 rounded border p-4">
                    <label class="block">Name<input v-model="contact.name" required class="block w-full rounded border p-2" /></label>
                    <label class="block">Phone<input v-model="contact.phone" type="tel" class="block w-full rounded border p-2" /></label>
                    <label class="block">Email<input v-model="contact.email" type="email" class="block w-full rounded border p-2" /></label>
                    <div class="flex gap-4">
                        <button type="button" :disabled="index === 0" class="underline disabled:opacity-40" @click="move(index, -1)">Up</button>
                        <button type="button" :disabled="index === form.contacts.length - 1" class="underline disabled:opacity-40" @click="move(index, 1)">Down</button>
                        <button type="button" class="text-red-700 underline" @click="removeContact(index)">Remove contact</button>
                    </div>
                </div>
                <button type="button" class="rounded border px-3 py-2" @click="addContact">Add contact</button>
            </section>
            <section class="space-y-4 rounded border p-4">
                <h2 class="text-xl font-semibold">Venue</h2>
                <label v-for="field in venueFields" :key="field.key" class="block">
                    {{ field.label }}
                    <input v-model="form[field.key]" :type="field.type" :required="field.key === 'location_title'" class="block w-full rounded border p-2" />
                </label>
                <label class="block">Address<textarea v-model="form.address" required rows="4" class="block w-full rounded border p-2" /></label>
                <p class="text-sm text-neutral-500">The homepage map uses this address.</p>
            </section>
            <div class="flex items-center gap-4">
                <button type="submit" :disabled="form.processing" class="rounded bg-neutral-900 px-4 py-2 text-white disabled:opacity-50">Save club details</button>
                <span v-if="saved && !form.isDirty" role="status">Saved.</span>
                <a href="/" target="_blank" rel="noopener noreferrer" class="underline">View homepage</a>
            </div>
        </form>
    </div>
</template>
