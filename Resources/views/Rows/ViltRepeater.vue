<template>
    <div class="px-2 py-2" v-if="view === 'input'">
        <ViltLabel :row="row" v-if="label" />

        <div class="space-y-6 filament-forms-repeater-component rounded-xl">
            <VueDraggableNext class="space-y-6" :list="main">
                <div

                    class="relative bg-white border border-gray-300 shadow-sm rounded-xl dark:bg-gray-800 dark:border-gray-600"
                    v-for="(value, index) in main"
                    :key="index"
                >
                    <header class="flex items-center h-10 overflow-hidden border-b bg-gray-50 rounded-t-xl dark:bg-gray-800 dark:border-gray-700">
                        <button class="flex items-center justify-center flex-none w-10 h-10 text-gray-400 transition border-r hover:text-gray-300 dark:text-gray-400 dark:border-gray-700 dark:hover:text-gray-500">
                            <span class="sr-only">
                                Move down
                            </span>
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path d="M5 12a1 1 0 102 0V6.414l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L5 6.414V12zM15 8a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L15 13.586V8z"></path>
                            </svg>
                        </button>
                        <p class="flex-none px-4 text-xs font-medium text-gray-600 truncate dark:text-gray-400">

                        </p>
                        <div class="flex-1"></div>
                        <ul class="flex divide-x rtl:divide-x-reverse dark:divide-gray-700">
                            <li>
                                <button
                                    @click.prevent="removeItem(value)"
                                    class="flex items-center justify-center flex-none w-10 h-10 transition text-danger-600 hover:text-danger-500 dark:text-danger-500 dark:hover:text-danger-400"
                                >
                                    <span class="sr-only">
                                        Delete
                                    </span>
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </li>
                        </ul>

                    </header>
                    <div class="px-6 py-4">
                        <div v-for="(item, key) in row.options" :key="key">
                            <ViltText v-if="item.vue === 'ViltText.vue'" :row="item" view="input" v-model="main[index][item.name]"/>
                            <ViltEmail v-if="item.vue === 'ViltEmail.vue'" :row="item" view="input" v-model="main[index][item.name]"/>
                            <ViltTel v-if="item.vue === 'ViltTel.vue'" :row="item" view="input" v-model="main[index][item.name]"/>
                            <ViltTextArea v-if="item.vue === 'ViltTextArea.vue'" view="input" :row="item" v-model="main[index][item.name]"/>
                            <ViltNumber v-if="item.vue === 'ViltNumber.vue'" view="input" :row="item" v-model="main[index][item.name]"/>
                            <ViltSelect v-if="item.vue === 'ViltSelect.vue'" view="input" :row="item" v-model="main[index][item.name]"/>
                            <ViltSwitch v-if="item.vue === 'ViltSwitch.vue'" view="input" :row="item" v-model="main[index][item.name]"/>
                            <ViltColor v-if="item.vue === 'ViltColor.vue'" view="input" :row="item" v-model="main[index][item.name]"/>
                            <ViltDate v-if="item.vue === 'ViltDate.vue'" view="input" :row="item" v-model="main[index][item.name]"/>
                            <ViltDateTime v-if="item.vue === 'ViltDateTime.vue'" view="input" :row="item" v-model="main[index][item.name]"/>
                            <ViltTime v-if="item.vue === 'ViltTime.vue'" view="input" :row="item" v-model="main[index][item.name]"/>
                            <ViltMedia v-if="item.vue === 'ViltMedia.vue'" view="input" :row="item" v-model="main[index][item.name]"/>
                        </div>
                    </div>
                </div>
            </VueDraggableNext>
            <div class="relative flex justify-center">
                <button
                    @click.prevent="addItem()"
                    class="filament-button inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2rem] px-3 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700"
                >
                    Add New Item
                </button>
            </div>

        </div>
        <ViltError :row="row" :message="message" />
    </div>

    <div class="flex flex-col my-4 space-y-2" v-if="view === 'view' && main && main.length">
        <div>
            <p class="font-bold capitalize">{{ row.label ? row.label : row.name }}</p>
        </div>
        <div class="overflow-scroll">
            <table class="w-full table-normal">
                <thead>
                <tr>
                    <th class="w-full border"  v-for="(item, key) in row.options" :key="key">
                        {{item.label ? item.label : item.name}}
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr
                    v-for="(value, key) in main"
                    :key="key"
                    class="px-2 py-2"
                >
                    <td class="w-full border" v-for="(item, index) in row.options" :key="index">
                        <div v-if="item.vue === 'ViltMedia.vue'">
                            <img :src="value[item.name]" alt="" class="w-16" />
                        </div>
                        <div v-else>
                            <p v-if="value[item.name]" class="px-2 py-2">{{ value[item.name] }}</p>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

    </div>
    <div v-if="view === 'table' && main && main.length">
        <div class="mx-1 my-1 overflow-x-scroll">
            <table class="w-full table-normal">
                <thead>
                    <tr>
                        <th class="w-full border"  v-for="(item, key) in row.options" :key="key">
                            {{item.label ? item.label : item.name}}
                        </th>
                    </tr>
                </thead>
                <tbody>
                <tr
                    v-for="(value, key) in main"
                    :key="key"
                    class="px-2 py-2"
                >
                    <td class="w-full border" v-for="(item, index) in row.options" :key="index">
                        <div v-if="item.vue === 'ViltMedia.vue'">
                            <img :src="value[item.name]" alt="" class="w-16" />
                        </div>
                        <div v-else>
                            <p v-if="value[item.name]" class="px-2 py-2">{{ value[item.name] }}</p>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import { defineComponent } from "vue";
import JetInputError from "@@/Jetstream/InputError.vue";
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
import {VueDraggableNext} from 'vue-draggable-next'
import ViltLabel from "$$/ViltLabel.vue";
import ViltError from "$$/ViltError.vue";

export default defineComponent({
    components: {
        VueDraggableNext,
        JetInputError,
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
        ViltLabel,
        ViltError
    },
    data() {
        return {
            main: [],
        };
    },
    watch:{
        main:{
            handler: function (val, oldVal) {
                if(this.view === 'input'){
                    this.$emit('update:modelValue', val);
                }
            },
            deep: true
        },
        modelValue: function (val) {
            if(this.view === 'input' && this.modelValue !== null && this.modelValue !== undefined) {
                this.main = val;
            }
        },
    },
    beforeUpdate() {
        if(this.view === 'input'){
            if (this.modelValue !== null && this.modelValue !== undefined) {
                this.main = this.modelValue;
            }
            else if (this.row.default) {
                this.main.push(this.row.default);
            }
            else {
                this.main.push(this.optionRows);
            }
        }
        else {
            this.main = this.modelValue;
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
    computed:{
        optionRows(){
            let rows = {};
            for(let i=0; i<this.row.options.length; i++){
                if(this.row.options[i].default){
                    rows[this.row.options[i].name] = this.row.options[i].default;
                }
                else {
                    if(this.row.options[i].vue === 'ViltSwitch.vue'){
                        rows[this.row.options[i].name] = false;
                    }
                    else if(this.row.options[i].vue === 'ViltColor.vue'){
                        rows[this.row.options[i].name] = "#000000";
                    }
                    else {
                        rows[this.row.options[i].name] = "";
                    }
                }
            }
            return rows;
        }

    },
    methods: {
        addItem() {
            let lastItem = this.main[this.main.length - 1];
            for(let i=0; i<this.row.options.length; i++){
                if(this.row.options[i].required){
                    if(!lastItem[this.row.options[i].name]){
                        this.$toast.error(this.row.options[i].label ? this.row.options[i].label : this.row.options[i].name + " is required", {
                            position: "top",
                            style: {
                                background: "#d41717",
                                "border-radius": "0.25rem",
                            },
                        });
                        return;
                    }
                }
            }

            let rows = this.optionRows;
            let JSONRows = JSON.stringify(rows);
            this.main.push(JSON.parse(JSONRows));
            this.$emit("update:modelValue", this.main);
        },
        removeItem(value) {
            this.main.splice(this.main.indexOf(value), 1);
            this.$emit("update:modelValue", this.main);
        },
    },
});
</script>
