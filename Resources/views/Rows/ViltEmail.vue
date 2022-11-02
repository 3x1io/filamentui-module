<template>
    <div class="py-2 px-2" v-if="view === 'input'">
        <ViltLabel :row="row" v-if="label"/>
        <input
            type="email"
            :name="row.name"
            :id="row.name"
            :disabled="row.disabled"
            v-model="value"
            :placeholder="row.placeholder"
            class="block my-2 w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500 border-gray-300 dark:border-gray-600"
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
        <a v-if="row.badge" :href="'mailto:'+modelValue" target="_blank" class="inline-flex items-center justify-center rounded-full bg-primary-100 px-2.5 py-0.5 text-primary-700">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="mx-1 h-3 w-3">
                <path d="M1.5 8.67v8.58a3 3 0 003 3h15a3 3 0 003-3V8.67l-8.928 5.493a3 3 0 01-3.144 0L1.5 8.67z" />
                <path d="M22.5 6.908V6.75a3 3 0 00-3-3h-15a3 3 0 00-3 3v.158l9.714 5.978a1.5 1.5 0 001.572 0L22.5 6.908z" />
            </svg>


            <p class="whitespace-nowrap text-sm">{{ modelValue }}</p>
        </a>
        <p v-else>{{ modelValue }}</p>
    </div>
</template>

<script>
import { defineComponent } from "vue";
import JetInputError from "@@/Jetstream/InputError.vue";
import ViltLabel from "$$/ViltLabel.vue";
import ViltError from "$$/ViltError.vue";

export default defineComponent({
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
    components: {
        JetInputError,
        ViltLabel,
        ViltError
    },
    data(){
        return {
            value: ""
        }
    },
    mounted(){
        if(this.modelValue !== null){
            this.value = this.modelValue
        }
        else {
            if(this.row.default){
                this.value = this.row.default
            }
        }
    },
});
</script>
