<template>
    <div v-if="Object.keys(this.bulk).length"  class="filament-dropdown filament-tables-bulk-actions">
        <div @click="showBluk = !showBluk" class="filament-dropdown-trigger cursor-pointer">
            <button title="Open actions" type="button" class="filament-icon-button flex items-center justify-center rounded-full relative hover:bg-gray-500/5 focus:outline-none text-primary-500 focus:bg-primary-500/10 dark:hover:bg-gray-300/5 w-10 h-10 filament-tables-bulk-actions-trigger">
                                    <span class="sr-only">
                                        Open actions
                                    </span>

                <svg class="filament-icon-button-icon w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                </svg>
            </button>
        </div>

        <div v-show="showBluk" class="filament-dropdown-panel absolute z-10 w-full rounded-lg bg-white shadow-lg ring-1 ring-black/5 transition dark:bg-gray-800 dark:ring-white/10 max-w-[14rem]">
            <div class="filament-tables-bulk-action">
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<script>
import { defineComponent } from "vue";
import { Link } from "@inertiajs/inertia-vue3";
export default defineComponent({
    components: {
        Link,
    },
    props: {
        bulk: Object,
        show: Boolean,
        collection: Object
    },
    watch: {
        show(newValue, oldValue){
            this.showBluk = newValue;
        }
    },
    data() {
        return {
            showBluk: false,
        };
    },
    methods: {
        close() {
            this.$emit("close", this.showBluk);
        },
    },
});
</script>
