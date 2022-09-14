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
                <form @submit.prevent="$emit('submitted')">
                    <div class="filament-forms-section-content p-6"
                         :class="hasActions ? 'sm:rounded-tl-md sm:rounded-tr-md' : 'sm:rounded-md'">
                        <div class="grid grid-cols-1 lg:grid-cols-2 filament-forms-component-container gap-6">
                            <slot name="form"></slot>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-4 justify-start mb-4" v-if="hasActions">
                        <slot name="actions"></slot>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    import { defineComponent } from 'vue'

    export default defineComponent({
        emits: ['submitted'],

        computed: {
            hasActions() {
                return !! this.$slots.actions
            }
        }
    })
</script>
