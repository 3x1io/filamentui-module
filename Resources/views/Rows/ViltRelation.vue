<template>
    <div class="py-2 px-2" v-if="view === 'input'">
        <ViltLabel :row="row" v-if="label" />
        <multiselect
            v-model="value"
            :options="records"
            :multiple="true"
            :track-by="row.trackById"
            :label="row.trackByName"
            :disabled="row.disabled"
            :loading="isLoading"
            :searchable="true"
            @search-change="asyncFind"
            class="mt-2"
            :class="{'border-danger-600 ring-danger-600' : message}"
        >
            <template #singleLabel="props">
                <div class="flex justify-start space-x-2">
                    <img class="option__image w-8" v-if="props.option.media" :src="props.option.media" alt="No Man’s Sky">
                    <span class="option__desc">
                        <span class="option__title">
                            {{ transItem(props.option, 'name') }}
                        </span>
                        <br>
                        <span class="option__small text-sm text-gray-400" v-if="props.option.description">
                            {{ transItem(props.option, 'description') }}
                        </span>
                    </span>
                </div>
            </template>
            <template #option="props">
                <div class="flex justify-start space-x-2">
                    <img class="option__image w-8" v-if="props.option.media" :src="props.option.media" :alt="props.option.name">
                    <div class="option__desc">
                        <span class="option__title">
                            {{ transItem(props.option, 'name') }}
                        </span>
                        <br>
                        <span class="option__small text-sm text-gray-400" v-if="props.option.description">
                            {{ transItem(props.option, 'description') }}
                        </span>
                    </div>
                </div>
            </template>
        </multiselect>
        <ViltError :row="row" :message="message" />
    </div>
    <div class="flex justify-between my-4" v-if="view === 'view' && modelValue">
        <div>
            <p class="font-bold capitalize">{{ row.label ? row.label: row.name }}</p>
        </div>
        <div class="flex justify-start space-x-2">
            <p
                v-for="(item, key) in modelValue"
                :key="key"
                class="inline-flex items-center justify-center ml-auto rtl:ml-0 rtl:mr-auto min-h-4 px-2 py-0.5 text-sm font-medium tracking-tight rounded-xl whitespace-normal bg-primary-200 dark:text-primary-500 text-primary-500 "
                :style="item.hasOwnProperty('color') ? 'background-color: ' + item.color : ''"
            >
                <i v-if="item.hasOwnProperty('icon')" :class="item.icon"></i>
                {{transItem(item, row.trackByName)}}
            </p>
        </div>
    </div>
    <div v-if="view === 'table' && modelValue" class="flex justify-start space-x-2">
        <p
           v-for="(item, key) in modelValue"
           :key="key"
           class="inline-flex items-center justify-center ml-auto rtl:ml-0 rtl:mr-auto min-h-4 px-2 py-0.5 text-sm font-medium tracking-tight rounded-xl whitespace-normal bg-primary-200 dark:text-primary-500 text-primary-500 "
           :style="item.hasOwnProperty('color') ? 'background-color: ' + item.color : ''"
        >
            <i v-if="item.hasOwnProperty('icon')" :class="item.icon"></i>
            {{ item ? transItem(item, row.trackByName): null }}
        </p>
    </div>
</template>

<script>
import { defineComponent } from "vue";
import JetInputError from "@@/Jetstream/InputError.vue";
import Multiselect from "@suadelabs/vue3-multiselect";
import "@suadelabs/vue3-multiselect/dist/vue3-multiselect.css";
import ViltLabel from "$$/ViltLabel.vue";
import ViltError from "$$/ViltError.vue";

export default defineComponent({
    components: {
        JetInputError,
        Multiselect,
        ViltLabel,
        ViltError
    },
    data() {
        return {
            value: "",
            records: [],
            isLoading: false,
        };
    },
    mounted() {
        let _this = this;
        if(this.modelValue && this.modelValue.length && this.view ==='input'){
            this.isLoading = true;
            let ids = [];
            for (let i = 0; i < this.modelValue.length; i++) {
                ids.push(this.modelValue[i].id);
            }
            axios
                .post(route("select"), {
                    model_type: this.row.model,
                    model_id: ids,
                })
                .then((response) => {
                    _this.value = response.data.data;
                    _this.isLoading = false;
                });
        }
        else {
            if (this.row.default) {
                this.value = this.row.default;
            }
        }


        if (this.row.model !== null && this.view ==='input') {
            this.isLoading = true;
            axios
                .post(route("select"), {
                    model_type: this.row.model,
                })
                .then((response) => {
                    this.records = response.data.data.data;
                    this.isLoading = false;
                });
        }

        if(this.view !== 'input'){
            this.value = this.modelValue;
        }
    },
    watch: {
        value: function (val) {
            if(this.view === 'input') {
                this.$emit("update:modelValue", val);
                this.$emit("change");
            }
        },
        modelValue: function (val) {
            if(this.view === 'input'&& this.modelValue) {
                this.value = val;
            }
        },
    },
    methods: {
        transItem(item, key) {
            if (
                item[key] &&
                item[key].hasOwnProperty("ar") &&
                item[key].hasOwnProperty("en")
            ) {
                if (localStorage.getItem("lang")) {
                    let lang = JSON.parse(localStorage.getItem("lang"));
                    if (lang.id === "ar") {
                        return item[key].en;
                    } else if (lang.id === "en") {
                        return item[key].ar;
                    }
                }
            } else {
                return item[key];
            }
        },
        asyncFind(query, key = null) {
            this.isLoading = true;
            let _this = this;
            if (!key) {
                key = this.row.trackByName;
            }
            axios
                .post(route("select"), {
                    model_type: this.row.model,
                    query: true,
                    key: key,
                    value: query,
                })
                .then((response) => {
                    _this.records = response.data.data.data;
                    _this.isLoading = false;
                });
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
