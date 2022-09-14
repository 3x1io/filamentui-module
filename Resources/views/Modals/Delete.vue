<template>
        <JetDialogModal :show="showModal" maxWidth="sm">
            <template #content>
                <div class="p-4 space-y-2 text-center dark:text-white">
                    <h2 class="filament-modal-heading text-xl font-bold tracking-tight" id="ozo9fHMwMlC1FjErUz3l-table-bulk-action.heading">
                        {{ trans('global.delete') }}
                    </h2>

                    <h3 class="filament-modal-subheading text-gray-500">
                        {{ trans('global.delete.message') }}
                    </h3>
                </div>
            </template>

            <template #footer>
                <div class="filament-modal-actions grid gap-2 grid-cols-[repeat(auto-fit,minmax(0,1fr))]">
                    <button
                        class="filament-button inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-danger-600 hover:bg-danger-500 focus:bg-danger-700 focus:ring-offset-danger-700 filament-tables-modal-button-action"
                        @click.prevent="destroyRecord(form.id)"
                    >
                        {{trans('global.delete')}}
                    </button>
                    <button
                        @click="close()"
                        class="filament-button inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm text-gray-800 bg-white border-gray-300 hover:bg-gray-50 focus:ring-primary-600 focus:text-primary-600 focus:bg-primary-50 focus:border-primary-600 dark:bg-gray-800 dark:hover:bg-gray-700 dark:border-gray-600 dark:hover:border-gray-500 dark:text-gray-200 dark:focus:text-primary-400 dark:focus:border-primary-400 dark:focus:bg-gray-800 filament-tables-modal-button-action"
                    >
                        {{trans('global.close')}}
                    </button>
                </div>

            </template>
        </JetDialogModal>
</template>

<script>
import { defineComponent } from "vue";
import { Link } from "@inertiajs/inertia-vue3";
import JetDialogModal from "@@/Jetstream/DialogModal.vue";
import JetButton from "@@/Jetstream/Button.vue";
import JetInput from "@@/Jetstream/Input.vue";
import JetInputError from "@@/Jetstream/InputError.vue";
import JetLabel from "@@/Jetstream/Label.vue";

export default defineComponent({
    components: {
        Link,
        JetDialogModal,
        JetButton,
        JetInput,
        JetInputError,
        JetLabel,
    },
    props: {
        show: Boolean,
        url: String,
        item: Object
    },
    data() {
        return {
            showModal: false,
            form: {},
        };
    },
    watch: {
        show(newValue, oldValue) {
            this.showModal = newValue;
        },
        item(newValue, oldValue) {
            this.form = newValue;
        },
    },
    computed:{
        lang(){
            return this.$page.props.data.trans;
        }
    },
    methods: {
        trans(key){
            return this.lang[key] ? this.lang[key] : key;
        },
        close() {
            this.$emit("close", this.showModal);
        },
        destroyRecord(id) {
            this.form.submit("delete", this.route(this.url +".destory", id), {
                preserveScroll: true,
                onSuccess: () => {
                    this.form.reset();
                    this.$emit('success');
                },
            });
        },
    },
});
</script>
