<script setup>
import { usePage, Link } from '@inertiajs/inertia-vue3'
import {useStyleStore} from '@@/Stores/style.js';

const styleStorge = useStyleStore();
</script>

<template>
    <div class="filament-main-footer py-4 shrink-0">
        <div class="flex items-center justify-center filament-footer">
            <Link :href="route('dashboard')" rel="noopener noreferrer" class="text-center text-gray-300 hover:text-primary-500 transition">
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
            </Link>
        </div>
    </div>
</template>
