<template>
    <div class="py-2 px-2" v-if="view === 'input'">
        <ViltLabel :row="row" v-if="label" />
        <input
            type="number"
            :max="row.max"
            :min="row.min"
            :step="row.step"
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
            <p v-if="row.money">{{ money(modelValue) }}</p>
            <p v-else>{{ modelValue }}</p>
        </div>
    </div>
    <div class="flex justify-between my-4" v-if="view === 'table'">
        <p v-if="row.money">{{ money(modelValue) }}</p>
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
    data(){
        return {
            value: ""
        }
    },
    methods: {
        money(item) {
            if(item){
                if (localStorage.getItem('lang')) {
                    let lang = JSON.parse(localStorage.getItem('lang'));
                    if (lang.id === 'ar') {
                        return item.toLocaleString('en-US', {
                            style: 'currency',
                            currency: 'EGP',
                        });
                    } else if (lang.id === 'en') {
                        return item.toLocaleString('ar-EG', {
                            style: 'currency',
                            currency: 'EGP',
                        });
                    }
                }
            }
            else {
                item = 0;
                if (localStorage.getItem('lang')) {
                    let lang = JSON.parse(localStorage.getItem('lang'));
                    if (lang.id === 'ar') {
                        return item.toLocaleString('en-US', {
                            style: 'currency',
                            currency: 'EGP',
                        });
                    } else if (lang.id === 'en') {
                        return item.toLocaleString('ar-EG', {
                            style: 'currency',
                            currency: 'EGP',
                        });
                    }
                }
            }

        }
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
