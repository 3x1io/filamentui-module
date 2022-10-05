<script setup>
import {useForm, Head, Link, usePage} from '@inertiajs/inertia-vue3'
import {mdiAccount, mdiAsterisk} from '@mdi/js'
import LayoutGuest from '@@/Layouts/LayoutGuest.vue'
import FilamentText from '@@/Components/Rows/FilamentText.vue';
import FilamentCheck from '@@/Components/Rows/FilamentCheck.vue';
import {ref} from "vue";
import FilamentButton from "@@/Components/Actions/FilamentButton.vue";
import {useStyleStore} from '@@/Stores/style.js';

const props = defineProps({
    canResetPassword: Boolean,
    status: {
        type: String,
        default: null
    }
})


const styleStorge = useStyleStore();

let loading = ref(false);
const form = useForm({
    email: '',
    password: '',
    remember: []
})
const submit = () => {
    loading.value = true;
    form
        .transform(data => ({
            ...data,
            remember: form.remember && form.remember.length ? 'on' : ''
        }))
        .post(route('login'), {
            onFinish: () => {
                loading.value = false;
                form.reset('password')
            },
        })
}
</script>

<template>
    <LayoutGuest>
        <div
            class="filament-login-page flex items-center justify-center min-h-screen bg-gray-100 text-gray-900 py-12 dark:bg-gray-900 dark:text-white">
            <div class="w-screen px-6 -mt-16 space-y-8 md:mt-0 md:px-2 max-w-md">
                <div
                    class="p-8 space-y-4 bg-white/50 backdrop-blur-xl border border-gray-200 shadow-2xl rounded-2xl relative dark:bg-gray-900/50 dark:border-gray-700">
                    <div class="flex justify-center w-full">
                        <img
                            v-if="styleStorge.darkMode"
                            :src="usePage().props.value.data.appUrl +'/'+usePage().props.value.data.theme.dark_logo"
                            class="h-8"
                            alt="logo"
                        />
                        <img
                            v-else
                            :src="usePage().props.value.data.appUrl +'/'+usePage().props.value.data.theme.logo"
                            class="h-8"
                            alt="logo"
                        />
                    </div>

                    <h2 class="text-2xl font-bold tracking-tight text-center">
                        Login
                    </h2>

                    <div>
                        <form class="space-y-8" @submit.prevent="submit">
                            <div class="grid grid-cols-1 filament-forms-component-container gap-6">
                                <FilamentText v-model="form.email" :row="{
                                    label: 'Email address',
                                    name: 'email',
                                    type: 'email',
                                    required: true,
                                }" :col="1" :errors="form.errors.email" />

                                <FilamentText v-model="form.password" :row="{
                                    label: 'Password',
                                    name: 'password',
                                    type: 'password',
                                    required: true,
                                }" :col="1" :errors="form.errors.password" />

                                <FilamentCheck v-model="form.remember" :row="{
                                    label: 'Remember me',
                                    name: 'remember',
                                    type: 'remember',
                                    required: false,
                                }" :col="1" :errors="form.errors.remember" />
                            </div>


                            <FilamentButton label="Login" type="submit" :loading="loading" />
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </LayoutGuest>
</template>
