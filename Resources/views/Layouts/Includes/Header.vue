<script setup>
import { useTrans } from '@@/Composables/useTrans';
const props = defineProps({
    title: String,
    button: String,
    url: String,
    canCreate: Boolean
});
let { trans } = useTrans();
let emit = defineEmits(['createItem']);
</script>

<template>
    <header class="filament-header space-y-2 items-start justify-between sm:flex sm:space-y-0 sm:space-x-4  sm:rtl:space-x-reverse sm:py-4">
        <h1 class="text-2xl font-bold tracking-tight md:text-3xl filament-header-heading">
            {{ title }}
        </h1>

        <div class="filament-page-actions flex flex-wrap items-center gap-4 justify-start shrink-0">
            <slot></slot>
            <a
                class="
                filament-button
                inline-flex
                items-center
                justify-center
                py-1
                gap-1
                font-medium
                rounded-lg
                border
                transition-colors
                focus:outline-none
                focus:ring-offset-2
                focus:ring-2
                focus:ring-inset
                dark:focus:ring-offset-0
                min-h-[2.25rem]
                px-4
                text-sm
                text-white
                shadow
                focus:ring-white
                border-transparent
                bg-primary-600
                hover:bg-primary-500
                focus:bg-primary-700
                focus:ring-offset-primary-700
                filament-page-button-action"
                v-if="props.canCreate"
                @click.prevent="emit('createItem')"
                href="#"
            >
                <span class="">
                    {{ button }}
                </span>
            </a>
        </div>
    </header>
</template>
