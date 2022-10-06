<template>
    <div class="py-2 px-2" v-if="view === 'input'">
        <ViltLabel :row="row" v-if="label" />
        <multiselect
            v-if="hasMediaOrDescription"
            v-model="value"
            :options="row.options"
            :multiple="row.multi"
            :track-by="row.trackById"
            :label="row.trackByName"
            :disabled="row.disabled"
            class="mt-2"
            :class="{'border-danger-600 ring-danger-600' : message}"
        >
            <template #singleLabel="props">
                <div class="flex justify-start space-x-2">
                    <img class="option__image w-8" v-if="props.option.media" :src="props.option.media" alt="No Man’s Sky">
                    <span class="option__desc">
                        <span class="option__title">
                            {{ props.option.name }}
                        </span>
                        <br>
                        <span class="option__small text-sm text-gray-400" v-if="props.option.description">
                            {{ props.option.description }}
                        </span>
                    </span>
                </div>
            </template>
            <template #option="props">
                <div class="flex justify-start space-x-2">
                    <img class="option__image w-8" v-if="props.option.media" :src="props.option.media" :alt="props.option.name">
                    <div class="option__desc">
                        <span class="option__title">
                            {{ props.option.name }}
                        </span>
                        <br>
                        <span class="option__small text-sm text-gray-400" v-if="props.option.description">
                            {{ props.option.description }}
                        </span>
                    </div>
                </div>
            </template>
        </multiselect>
        <multiselect
            v-else
            v-model="value"
            :options="row.options"
            :multiple="row.multi"
            :track-by="row.trackById"
            :label="row.trackByName"
            :disabled="row.disabled"
            :class="row.className"
            class="mt-2"
        >
        </multiselect>
        <ViltError :row="row" :message="message" />
    </div>
    <div class="flex justify-between my-4" v-if="view === 'view' && modelValue">
        <div>
            <p class="font-bold capitalize">{{ row.label ? row.label: row.name }}</p>
        </div>
        <div v-if="row.multi">
            <div class="flex justify-start space-x-2">
                <p
                    v-for="(item, key) in modelValue"
                    :key="key"
                    class="inline-flex items-center justify-center space-x-2 ml-auto rtl:ml-0 rtl:mr-auto min-h-4 px-2 py-0.5 text-sm font-medium tracking-tight rounded-xl whitespace-normal bg-primary-200 dark:text-primary-500 text-primary-500 "
                    :style="item.hasOwnProperty('color') ? 'background-color: ' + item.color : ''"
                >
                    <i v-if="item.hasOwnProperty('icon')" :class="item.icon"></i>
                    <span>{{ item[row.trackByName] }}</span>
                </p>
            </div>
        </div>
        <div v-else>
            <div class="flex justify-start space-x-2">
                <p
                    class="inline-flex items-center justify-center space-x-2 ml-auto rtl:ml-0 rtl:mr-auto min-h-4 px-2 py-0.5 text-sm font-medium tracking-tight rounded-xl whitespace-normal bg-primary-200 dark:text-primary-500 text-primary-500 "
                    :style="modelValue.hasOwnProperty('color') ? 'background-color: ' + modelValue.color : ''"
                >
                    <i v-if="modelValue.hasOwnProperty('icon')" :class="modelValue.icon"></i>
                    <span>{{ modelValue[row.trackByName] }}</span>
                </p>
            </div>
        </div>
    </div>
    <div v-if="view === 'table' && modelValue" class="flex justify-start space-x-2">
        <div v-if="row.multi">
            <p
                v-for="(item, key) in modelValue"
                :key="key"
                class="inline-flex items-center justify-center ml-auto rtl:ml-0 rtl:mr-auto min-h-4 px-2 py-0.5 text-sm font-medium tracking-tight rounded-xl whitespace-normal bg-primary-200 dark:text-primary-500 text-primary-500 "
                :style="item.hasOwnProperty('color') ? 'background-color: ' + item.color : ''"
            >
                <i v-if="item.hasOwnProperty('icon')" :class="item.icon"></i>
                {{ item ? item[row.trackByName]: null }}
            </p>
        </div>
        <div v-else>
            <p
                class="inline-flex items-center justify-center ml-auto rtl:ml-0 rtl:mr-auto min-h-4 px-2 py-0.5 text-sm font-medium tracking-tight rounded-xl whitespace-normal bg-primary-200 dark:text-primary-500 text-primary-500 "
                :style="modelValue.hasOwnProperty('color') ? 'background-color: ' + modelValue.color : ''"
            >
                <i v-if="modelValue.hasOwnProperty('icon')" :class="modelValue.icon"></i>
                {{ modelValue ? modelValue[row.trackByName]: null }}
            </p>
        </div>
    </div>
</template>

<script>
import { defineComponent } from "vue";
import JetInputError from "@@/Jetstream/InputError.vue";
import Multiselect from "@suadelabs/vue3-multiselect";
import "@suadelabs/vue3-multiselect/dist/vue3-multiselect.css";
import ViltLabel from "$$/ViltLabel.vue";
import ViltError from "$$/ViltError.vue";

export default defineComponent({
    components: {
        JetInputError,
        Multiselect,
        ViltLabel,
        ViltError
    },
    data() {
        return {
            value: "",
        };
    },
    computed:{
        hasMediaOrDescription(){
            return this.row.options.some(option => option.media || option.description);
        },
    },
    created() {
        if (this.modelValue !== null) {
            this.value = this.modelValue;
        }
        else {
            if (this.row.default) {
                this.value = this.row.default;
            }
        }
    },
    watch: {
        value: function (val) {
            if(this.view === 'input') {
                this.$emit("update:modelValue", val);
                this.$emit("change");
            }
        },
        modelValue: function (val) {
            if(this.view === 'input'&& this.modelValue) {
                this.value = val;
            }
        },
    },
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

<style type="text/scss">
.multiselect__tag{
    background: theme('colors.primary.200') !important;
    color: theme('colors.primary.600') !important;
}
.multiselect__tag-icon {
    background: theme('colors.primary.200') !important;
    color: theme('colors.primary.600') !important;
}
.multiselect__option--highlight {
    background: theme('colors.primary.600') !important;
}
</style>
