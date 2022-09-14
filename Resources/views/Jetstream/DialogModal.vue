<template>
    <modal :show="show" :max-width="maxWidth" :closeable="closeable" @close="close">
        <div class="px-2 py-2" v-if="$slots.title">
            <div class="filament-modal-header px-4 py-2">
                <slot name="title">
                </slot>
            </div>
        </div>
        <div aria-hidden="true" class="filament-hr border-t dark:border-gray-700" v-if="$slots.title"></div>
        <div class="filament-modal-content space-y-2 py-4 px-4">
            <slot name="content">
            </slot>
        </div>
        <div aria-hidden="true" class="filament-hr border-t dark:border-gray-700"></div>
        <div class="filament-modal-footer px-4 py-4">
            <slot name="footer">
            </slot>
        </div>
    </modal>
</template>

<script>
    import { defineComponent } from 'vue'
    import Modal from './Modal.vue'

    export default defineComponent({
        emits: ['close'],

        components: {
            Modal,
        },

        props: {
            show: {
                default: false
            },
            maxWidth: {
                default: '2xl'
            },
            closeable: {
                default: false
            },
        },

        methods: {
            close() {
                this.$emit('close')
            },
        }
    })
</script>
