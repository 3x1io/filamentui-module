<template>
    <div class="col-span-full">
        <div class="filament-forms-section-component bg-white rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-800">
            <div class="filament-forms-section-header-wrapper flex items-center px-4 py-2 bg-gray-100 rtl:space-x-reverse overflow-hidden rounded-t-xl min-h-[40px] min-h-[56px] dark:bg-gray-900">
                <div class="filament-forms-section-header flex-1 my-4 mx-2">
                    <h3 class="font-bold tracking-tight pointer-events-none text-xl font-bold"><slot name="title"></slot></h3>
                    <p class="text-sm"><slot name="description"></slot></p>
                </div>
            </div>

            <div class="filament-forms-section-content-wrapper">
                <div class="filament-forms-section-content p-6">
                    <slot name="content"></slot>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { defineComponent } from 'vue'
    import JetSectionTitle from './SectionTitle.vue'

    export default defineComponent({
        components: {
            JetSectionTitle,
        }
    })
</script>
