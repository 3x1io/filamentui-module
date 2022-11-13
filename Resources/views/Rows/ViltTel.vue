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
        <a v-if="row.badge" :href="'tel:'+modelValue" target="_blank" class="inline-flex items-center justify-center rounded-full bg-primary-100 px-2.5 py-0.5 text-primary-700">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="mx-1 h-3 w-3">
                <path fill-rule="evenodd" d="M1.5 4.5a3 3 0 013-3h1.372c.86 0 1.61.586 1.819 1.42l1.105 4.423a1.875 1.875 0 01-.694 1.955l-1.293.97c-.135.101-.164.249-.126.352a11.285 11.285 0 006.697 6.697c.103.038.25.009.352-.126l.97-1.293a1.875 1.875 0 011.955-.694l4.423 1.105c.834.209 1.42.959 1.42 1.82V19.5a3 3 0 01-3 3h-2.25C8.552 22.5 1.5 15.448 1.5 6.75V4.5z" clip-rule="evenodd" />
            </svg>

            <p class="whitespace-nowrap text-sm">{{ modelValue }}</p>
        </a>
        <p v-else>{{ modelValue }}</p>
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
        if (this.modelValue !== null && this.modelValue !== undefined) {
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
