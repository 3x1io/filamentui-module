<template>
    <div class="py-2 px-2" v-if="view === 'input'">
        <ViltLabel :row="row" v-if="label"/>
        <div>
            <flat-pickr
                v-model="value"
                :config="time"
                :placeholder="row.placeholder ? row.placeholder : row.name"
                class="block my-2 w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500 border-gray-300 dark:border-gray-600"
                :disabled="row.disabled"
                :class="{'border-danger-600 ring-danger-600' : message}"
            ></flat-pickr>
        </div>
        <ViltError :row="row" :message="message" />
    </div>
    <div class="flex justify-between my-4" v-if="view === 'view'">
        <div>
            <p class="font-bold capitalize">{{ row.label ? row.label : row.name }}</p>
        </div>
        <div>
            <p>
                <i class="mx-2 bx bxs-time"></i> {{ timefilter(modelValue) }}
            </p>
        </div>
    </div>
    <div v-if="view === 'table'">
        <i class="mx-2 bx bxs-time"></i>
        <span> {{ timefilter(modelValue) }}</span>
    </div>
</template>

<script>
import { defineComponent } from "vue";
import JetInputError from "@@/Jetstream/InputError.vue";
import flatPickr from "vue-flatpickr-component";
import moment from "moment";
import 'flatpickr/dist/flatpickr.css';
import ViltLabel from "$$/ViltLabel.vue";
import ViltError from "$$/ViltError.vue";

export default defineComponent({
    components: {
        JetInputError,
        flatPickr,
        ViltLabel,
        ViltError,
    },
    data() {
        return {
            value: "",
            time: {
                noCalendar: true,
                enableTime: true,
                enableSeconds: true,
                defaultHour: 12,
                locale: "ar", // locale for this instance only
                dateFormat: "H:i:S",
                altInput: true,
                altFormat: "h:i:S K",
            },
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
            if(this.view === 'input'){
                this.$emit("update:modelValue", val);
            }
        },
        modelValue: function (val) {
            if(this.view === 'input'&& this.modelValue) {
                this.value = val;
            }
        },
    },
    methods: {
        datetimefilter(value) {
            if (value) {
                return moment(String(value)).format("MM/DD/YYYY hh:mm");
            }
        },
        datefilter(value) {
            if (value) {
                return moment(String(value)).format("MM/DD/YYYY");
            }
        },
        timefilter(value) {
            if (value) {
                return moment(String(value), "hh:mm:ss").format("hh:mm:ss A");
            }
        },
    },
});
</script>
