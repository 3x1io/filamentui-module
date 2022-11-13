<template>
    <div class="py-2 px-2" v-if="view === 'input'">
        <ViltLabel :row="row" v-if="label" />
        <br v-if="label">
        <VSwatches v-model="value" show-fallback fallback-input-type="color" popover-x="left" :disabled="row.disabled"></VSwatches>
        <ViltError :row="row" :message="message" />
    </div>
    <div class="flex justify-between my-4" v-if="view === 'view'">
        <div>
            <p class="font-bold">{{ row.label ? row.label: row.name }}</p>
        </div>
        <div>
            <div class="py-2 px-2 rounded-full w-8 h-8 mx-auto capitalize" :style="'background-color: '+modelValue"></div>
        </div>
    </div>
    <div v-if="view === 'table'">
        <div>
            <div class="py-2 px-2 rounded-full w-8 h-8 mx-auto" :style="'background-color: '+modelValue"></div>
        </div>
    </div>
</template>

<script>
import { defineComponent } from "vue";
import JetInputError from "@@/Jetstream/InputError.vue";
import VSwatches from "vue3-swatches";
import ViltLabel from "$$/ViltLabel.vue";
import ViltError from "$$/ViltError.vue";

export default defineComponent({
    components: {
        JetInputError,
        VSwatches,
        ViltLabel,
        ViltError
    },
    data() {
        return {
            value: "",
        };
    },
    mounted() {
        if (this.modelValue !== null && this.modelValue !== undefined) {
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
