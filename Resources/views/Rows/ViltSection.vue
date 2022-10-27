<template>
    <div class="py-2 px-2" v-if="view === 'input'">
        <div class="filament-forms-section-header-wrapper flex rtl:space-x-reverse overflow-hidden rounded-xl min-h-[56px] px-4 py-2 items-center bg-gray-100 dark:bg-gray-900">
            <div class="filament-forms-section-header flex-1 space-y-1">
                <div class="flex justify-start space-x-2 text-xl items-center"  :class="row.color ? 'text-'+row.color+'-500': null">
                    <i :class="row.icon" class="bx-sm my-auto"></i>
                    <h3 class="font-bold tracking-tight pointer-events-none  font-bold">
                        {{row.label}}
                    </h3>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { defineComponent } from "vue";

export default defineComponent({
    props: {
        modelValue: {},
        row: {
            Object,
            default: {},
        },
        view: {
            String,
            default: "input",
        },
        message: {
            String,
            default: null,
        },
        label: {
            Boolean,
            default: true,
        }
    },
});
</script>
