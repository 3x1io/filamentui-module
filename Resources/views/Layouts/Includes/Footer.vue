<script setup>
import { usePage, Link } from '@inertiajs/inertia-vue3'
</script>

<template>
    <div class="filament-main-footer py-4 shrink-0">
        <div class="flex items-center justify-center filament-footer">
            <Link :href="route('dashboard')" rel="noopener noreferrer" class="text-center text-gray-300 hover:text-primary-500 transition">
                <img
                    :src="usePage().props.value.data.appUrl +'/'+usePage().props.value.data.theme.logo"
                    class="w-10 h-10"
                    alt="logo"
                />
            </Link>
        </div>
    </div>
</template>
