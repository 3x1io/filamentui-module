<template>
    <JetDialogModal :show="showModal">
        <template #title>
            <h2 class="filament-modal-heading text-xl font-bold tracking-tight dark:text-gray-200">{{ title }}</h2>
        </template>

        <template #content>
            <div class="print_area">
                <div v-for="(row, key) in rows" :key="key">
                    <div v-if="row.view" class="dark:text-gray-200">
                        <ViltText v-if="row.vue === 'ViltText.vue' " :row="row" v-model="item[row.name]" view="view"/>
                        <ViltEmail v-if="row.vue === 'ViltEmail.vue' " :row="row" v-model="item[row.name]" view="view"/>
                        <ViltTel v-if="row.vue === 'ViltTel.vue' " :row="row" v-model="item[row.name]" view="view"/>
                        <ViltTextArea v-if="row.vue === 'ViltTextArea.vue' " :row="row" v-model="item[row.name]" view="view"/>
                        <ViltNumber v-if="row.vue === 'ViltNumber.vue' " :row="row" v-model="item[row.name]" view="view"/>
                        <ViltSelect v-if="row.vue === 'ViltSelect.vue' " :row="row" v-model="item[row.name]" view="view"/>
                        <ViltSwitch v-if="row.vue === 'ViltSwitch.vue' " :row="row" v-model="item[row.name]" view="view"/>
                        <ViltColor v-if="row.vue === 'ViltColor.vue' " :row="row" v-model="item[row.name]" view="view"/>
                        <ViltDate v-if="row.vue === 'ViltDate.vue'"  :row="row" v-model="item[row.name]" view="view"/>
                        <ViltDateTime v-if="row.vue === 'ViltDateTime.vue'" :row="row" v-model="item[row.name]" view="view"/>
                        <ViltTime v-if="row.vue === 'ViltTime.vue'" :row="row" v-model="item[row.name]" view="view"/>
                        <ViltMedia v-if="row.vue === 'ViltMedia.vue'" @popUp="popUp" :row="row" v-model="item[row.name]" view="view"/>
                        <ViltRepeater v-if="row.vue === 'ViltRepeater.vue'" :row="row" v-model="item[row.name]" view="view"/>
                        <ViltSchema v-if="row.vue === 'ViltSchema.vue'" :row="row" v-model="item[row.name]" view="view"/>
                        <ViltRich v-if="row.vue === 'ViltRich.vue'"  :row="row" v-model="item[row.name]" view="view"/>
                        <ViltRelation v-if="row.vue === 'ViltRelation.vue'"  :row="row" v-model="item[row.name]" view="view"/>
                        <ViltHasOne v-if="row.vue === 'ViltHasOne.vue'"  :row="row" v-model="item[row.name]" view="view"/>
                        <ViltIcon v-if="row.vue === 'ViltIcon.vue'"  :row="row" v-model="item[row.name]" view="view"/>
                    </div>
                </div>
            </div>
        </template>

        <template #footer>
            <slot></slot>
            <button
                class="filament-button inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm text-gray-800 bg-white border-gray-300 hover:bg-gray-50 focus:ring-primary-600 focus:text-primary-600 focus:bg-primary-50 focus:border-primary-600 dark:bg-gray-800 dark:hover:bg-gray-700 dark:border-gray-600 dark:hover:border-gray-500 dark:text-gray-200 dark:focus:text-primary-400 dark:focus:border-primary-400 dark:focus:bg-gray-800 filament-tables-modal-button-action"
                @click="close">
                {{ trans("global.close") }}
            </button>
        </template>
    </JetDialogModal>
</template>

<script>
import { defineComponent } from "vue";
import { Link } from "@inertiajs/inertia-vue3";
import JetDialogModal from "@@/Jetstream/DialogModal.vue";
import JetSecondaryButton from "@@/Jetstream/SecondaryButton.vue";
import JetButton from "@@/Jetstream/Button.vue";
import ViltText from '$$/ViltText.vue'
import ViltTel from '$$/ViltTel.vue'
import ViltTextArea from "$$/ViltTextArea.vue";
import ViltNumber from "$$/ViltNumber.vue";
import ViltSelect from "$$/ViltSelect.vue";
import ViltSwitch from "$$/ViltSwitch.vue";
import ViltColor from "$$/ViltColor.vue";
import ViltEmail from "$$/ViltEmail.vue";
import ViltDate from "$$/ViltDate.vue";
import ViltDateTime from "$$/ViltDateTime.vue";
import ViltTime from "$$/ViltTime.vue";
import ViltMedia from "$$/ViltMedia.vue";
import ViltRepeater from "$$/ViltRepeater.vue";
import ViltSchema from "$$/ViltSchema.vue";
import ViltRich from "$$/ViltRich.vue";
import ViltRelation from "$$/ViltRelation.vue";
import ViltHasOne from "$$/ViltHasOne.vue";
import ViltIcon from "$$/ViltIcon.vue";

export default defineComponent({
    components: {
        ViltHasOne,
        Link,
        JetDialogModal,
        JetSecondaryButton,
        JetButton,
        ViltTextArea,
        ViltText,
        ViltTel,
        ViltNumber,
        ViltSelect,
        ViltSwitch,
        ViltColor,
        ViltEmail,
        ViltDate,
        ViltDateTime,
        ViltTime,
        ViltMedia,
        ViltRepeater,
        ViltSchema,
        ViltRich,
        ViltRelation,
        ViltIcon,
    },
    props: {
        show: Boolean,
        item: Object,
        rows: Array,
        title: String,
    },
    watch: {
        show(newValue, oldValue) {
            this.showModal = newValue;
        },
    },
    computed: {
        lang() {
            return this.$page.props.data.trans;
        },
    },
    data() {
        return {
            showModal: false,
        };
    },
    methods: {
        trans(key) {
            return this.lang[key] ? this.lang[key] : key;
        },
        close() {
            this.$emit("close", this.showModal);
        },
        popUp(images){
            this.$emit('media', images)
        }
    },
});
</script>
