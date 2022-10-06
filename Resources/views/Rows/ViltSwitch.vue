<template>
    <div class="py-2 px-2" v-if="view === 'input'">
        <ViltLabel :row="row" v-if="label"/>
        <br v-if="label">
        <Toggle
            :name="row.name"
            :id="row.name"
            v-model="value"
            :classes="{
              container: row.color? 'inline-block rounded-full outline-none focus:ring focus:ring-'+row.color+'-500 focus:ring-opacity-30' : 'inline-block rounded-full outline-none focus:ring focus:ring-primary-500 focus:ring-opacity-30',
              toggle: 'flex w-12 h-5 rounded-full relative cursor-pointer transition items-center box-content border-2 text-xs leading-none',
              toggleOn: row.color ? 'bg-'+row.color+'-500 border-'+row.color+'-500 justify-start text-white' : 'bg-primary-500 border-primary-500 justify-start text-white',
              toggleOff: 'bg-gray-200 border-gray-200 justify-end text-gray-700',
              toggleOnDisabled: 'bg-gray-300 border-gray-300 justify-start text-gray-400 cursor-not-allowed',
              toggleOffDisabled: 'bg-gray-200 border-gray-200 justify-end text-gray-400 cursor-not-allowed',
              handle: 'inline-block bg-white w-5 h-5 top-0 rounded-full absolute transition-all',
              handleOn: 'left-full transform -translate-x-full',
              handleOff: 'left-0',
              handleOnDisabled: 'bg-gray-100 left-full transform -translate-x-full',
              handleOffDisabled: 'bg-gray-100 left-0',
              label: 'text-center w-8 border-box whitespace-nowrap select-none',
            }"
            :disabled="row.disabled"
        />
        <ViltError :row="row" :message="message" />
    </div>
    <div v-if="view === 'view'" class="flex justify-between my-4">
        <div>
            <p class="font-bold capitalize">{{ row.label ? row.label : row.name }}</p>
        </div>
        <div>
            <div v-if="modelValue == 1" class="filament-tables-boolean-column px-4 py-3">
                <svg class="w-6 h-6 text-success-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>

            <div v-else class="filament-tables-boolean-column px-4 py-3">
                <svg class="w-6 h-6 text-danger-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>
    <div v-if="view === 'table'" class="flex justify-between my-4">
        <div v-if="modelValue == 1" class="filament-tables-boolean-column px-4 py-3">
            <svg class="w-6 h-6 text-success-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>

        <div v-else class="filament-tables-boolean-column px-4 py-3">
            <svg class="w-6 h-6 text-danger-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
    </div>
</template>
<script>
import { defineComponent } from "vue";
import Toggle from "@vueform/toggle";
import JetInputError from "@@/Jetstream/InputError.vue";
import JetLabel from "@@/Jetstream/Label.vue";
import '@vueform/toggle/themes/default.css';
import ViltLabel from "$$/ViltLabel.vue";
import ViltError from "$$/ViltError.vue";

export default defineComponent({
    components: {
        Toggle,
        JetInputError,
        JetLabel,
        ViltLabel,
        ViltError
    },
    data() {
        return {
            value: false
        }
    },
    watch: {
        value: function (val) {
            if(this.view === 'input') {
                if (val === "1" || val === 1 || val===true) {
                    this.$emit("update:modelValue", 1);
                } else {
                    this.$emit("update:modelValue", 0);
                }
            }
        },
        modelValue: function (val) {
            if(this.view === 'input') {
                if(val === "1" || val === 1 || val===true){
                    this.value = true;
                }
                else {
                    this.value = false;
                }
            }
        },
    },
    mounted(){
        if(this.row.default !== null){
            if((this.row.default === "1" || this.row.default === 1 || this.row.default===true) && this.row.default !== null){
                this.value = true
            }
            else {
                this.value = false
            }
        }
        else {
            if(this.modelValue === "1" || this.modelValue === 1 || this.modelValue===true){
                this.value = true
            }
            else {
                this.value = false
            }
        }
    },
    props: {
        modelValue: false,
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
