<template>
    <div class="py-2 px-2" v-if="view === 'input'">
        <ViltLabel :row="row" v-if="label" />
        <VueEditor
            v-model="value"
            :id="row.name"
            :disabled="row.disabled"
            :placeholder="row.placeholder"
            :class="{'border-danger-600 ring-danger-600' : message}"
        />
        <ViltError :row="row" :message="message" />
    </div>
    <div class="flex justify-between my-4" v-if="view === 'view'">
        <div>
            <p class="font-bold capitalize">{{ row.label ? row.label: row.name }}</p>
        </div>
        <div>
            <div v-html="modelValue"></div>
        </div>
    </div>
    <div v-if="view === 'table'">
        <div v-html="modelValue"></div>
    </div>
</template>

<script>
import JetInputError from "@@/Jetstream/InputError.vue";
// Basic Use - Covers most scenarios
import { VueEditor } from "vue3-editor";

import ViltLabel from "$$/ViltLabel.vue";
import ViltError from "$$/ViltError.vue";

export default {
    components: {
        JetInputError,
        VueEditor,
        ViltLabel,
        ViltError
    },
    data() {
        return {
            value: "",
        };
    },
    watch: {
        value: function (val) {
            if(this.view === 'input') {
                this.$emit("update:modelValue", val);
            }
        },
        modelValue: function (val) {
            if(this.view === 'input'&& this.modelValue) {
                this.value = val;
            }
        },
    },
    mounted() {
        if (this.modelValue !== null) {
            this.value = this.modelValue
        }
        else {
            if (this.row.default) {
                this.value = this.row.default
            }
        }
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
};
</script>
