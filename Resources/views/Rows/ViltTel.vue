<template>
    <div class="py-2 px-2" v-if="view === 'input'">
        <ViltLabel :row="row" v-if="label"/>
        <VueTelInput
            :disabled="row.disabled"
            v-model="value"
            @input="updateData"
            class="mt-2"
            :inputOptions="{
                    placeholder: row.placeholder,
                    name: row.name,
                    id: row.name,
                    styleClasses: 'block my-2 w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500 border-gray-300 dark:border-gray-600',
                }"
            :value="value"
            :class="{'border-danger-600 ring-danger-600' : message}"
        />
        <ViltError :row="row" :message="message" />
    </div>
    <div  v-if="view === 'view'" class="flex justify-between my-4" >
        <div>
            <p class="font-bold capitalize">{{ row.label ? row.label: row.name }}</p>
        </div>
        <div>
            <p>{{ modelValue }}</p>
        </div>
    </div>
    <div v-if="view === 'table'">
        <p class="bg-main-500 text-main-200 py-2 px-2">{{ modelValue }}</p>
    </div>
</template>

<script>
import { defineComponent } from "vue";
import JetInputError from "@@/Jetstream/InputError.vue";
import { VueTelInput } from "vue3-tel-input";
import "vue3-tel-input/dist/vue3-tel-input.css";
import ViltLabel from "$$/ViltLabel.vue";
import ViltError from "$$/ViltError.vue";

export default defineComponent({
    components: {
        JetInputError,
        VueTelInput,
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
                if (val) {
                    this.$emit("update:modelValue", val);
                } else {
                    this.$emit("update:modelValue", "");
                }
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
            this.value = this.modelValue;
        }
        else {
            if(this.row.default){
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
    methods: {
        updateData(number, phoneObject) {
            if (phoneObject) {
                this.value = phoneObject.number;
                this.$emit("update:modelValue", phoneObject.number);
            }
        },
    },
});
</script>
<style>
.vue-tel-input{
    border-color: #C8C8C8;
}
.dark .vti__dropdown:hover {
    background-color: #111010;
}
.dark .vti__dropdown-list{
    background-color: #1f2937;
    border: 1px solid #1f2937;
}
.dark .vti__dropdown-item.highlighted {
    background-color: #000;
}
</style>
