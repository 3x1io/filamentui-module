<template>
    <div class="py-2 px-2" v-if="view === 'input'">
        <ViltLabel :row="row" v-if="label"/>
        <input
            autocomplete="off"
            :type="row.type? row.type: 'text'"
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
    <div class="flex justify-between my-4" v-if="view === 'view'">
        <div>
            <p class="font-bold capitalize">{{ row.label ? row.label: row.name }}</p>
        </div>
        <div>
            <p>{{ modelValue }}</p>
        </div>
    </div>
    <div v-if="view === 'table'">
        <p v-if="row.badge" class="inline-flex items-center justify-center ml-auto rtl:ml-0 rtl:mr-auto min-h-4 px-2 py-0.5 text-sm font-medium tracking-tight rounded-xl whitespace-normal " :class="row.color?'text-'+row.color+'-600 dark:text-'+row.color+'-600 bg-'+row.color+'-200': 'bg-primary-200 dark:text-primary-500 text-primary-600' ">
            <span v-if="row.max">
                {{modelValue.split(' ').slice(0, row.max).join('  ') +'...'}}
            </span>
            <span v-else>
                {{modelValue}}
            </span>
        </p>
        <a v-else-if="row.url" :href="modelValue" target="_blank" class="inline-flex items-center justify-center ml-auto rtl:ml-0 rtl:mr-auto min-h-4 px-2 py-0.5 text-sm font-medium tracking-tight rounded-xl whitespace-normal bg-primary-200 dark:text-primary-500 text-primary-600" >
            <i class="bx bx-link"></i>
            Open URL
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
    mounted(){
        if(this.modelValue !== null && this.modelValue !== undefined){
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
