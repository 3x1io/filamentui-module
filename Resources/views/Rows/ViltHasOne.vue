<template>
    <div class="px-2 py-2" v-if="view === 'input'">
        <ViltLabel :row="row" v-if="label"/>
        <div class="grid grid-cols-12 gap-2">
            <div :class="!row.add ? 'col-span-12' : 'col-span-11'">
                <multiselect v-model="value" :options="records" :multiple="false" :track-by="row.trackById"
                             :label="row.trackByName" :disabled="row.disabled" :loading="isLoading" :searchable="true"
                             :class="{'border-danger-600 ring-danger-600' : message}" @search-change="asyncFind"
                             class="mt-2">
                    <template #singleLabel="props">
                        <div class="flex justify-start space-x-2">
                            <img class="w-8 option__image" v-if="props.option.media" :src="props.option.media"
                                 alt="No Man’s Sky">
                            <span class="option__desc">
                                <span class="option__title">
                                    {{ transItem(props.option, 'name') }}
                                </span>
                                <br>
                                <span class="text-sm text-gray-400 option__small" v-if="props.option.description">
                                    {{ transItem(props.option, 'description') }}
                                </span>
                                </span>
                        </div>
                    </template>
                    <template #option="props">
                        <div class="flex justify-start space-x-2">
                            <img class="w-8 option__image" v-if="props.option.media" :src="props.option.media"
                                 :alt="props.option.name">
                            <div class="option__desc">
                                            <span class="option__title">
                                                {{ transItem(props.option, 'name') }}
                                            </span>
                                <br>
                                <span class="text-sm text-gray-400 option__small" v-if="props.option.description">
                                                {{ transItem(props.option, 'description') }}
                                            </span>
                            </div>
                        </div>
                    </template>
                </multiselect>
            </div>
            <div v-if="row.add && view === 'input'" class="my-auto">
                <button @click.prevent="openAdd" title="Create option" type="button"
                        class="relative flex items-center justify-center w-10 h-10 -my-2 rounded-full filament-icon-button hover:bg-gray-500/5 focus:outline-none text-primary-500 focus:bg-primary-500/10 dark:hover:bg-gray-300/5 filament-forms-icon-button-action">
                    <span class="sr-only">
                        Create option
                    </span>

                    <svg class="w-5 h-5 filament-icon-button-icon" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <svg v-if="isLoading" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                         class="w-5 h-5 animate-spin filament-icon-button-icon">
                        <path opacity="0.2" fill-rule="evenodd" clip-rule="evenodd"
                              d="M12 19C15.866 19 19 15.866 19 12C19 8.13401 15.866 5 12 5C8.13401 5 5 8.13401 5 12C5 15.866 8.13401 19 12 19ZM12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z"
                              fill="currentColor"></path>
                        <path d="M2 12C2 6.47715 6.47715 2 12 2V5C8.13401 5 5 8.13401 5 12H2Z"
                              fill="currentColor"></path>
                    </svg>

                </button>
            </div>
        </div>
        <ViltError :row="row" :message="message"/>
    </div>
    <div class="flex justify-between my-4" v-if="view === 'view' && modelValue">
        <div>
            <p class="font-bold capitalize">{{ row.label ? row.label : row.name }}</p>
        </div>
        <div>
            <p
                class="flex justify-start space-x-2 ml-auto rtl:ml-0 rtl:mr-auto min-h-4 px-2 py-0.5 text-sm font-medium tracking-tight rounded-xl bg-primary-200 dark:text-primary-500 text-primary-600 my-auto"
                :style="modelValue.hasOwnProperty('color') ? 'background-color: ' + modelValue.color : ''"
            >
                <i v-if="modelValue.hasOwnProperty('icon')" :class="modelValue.icon" class="text-md my-auto"></i>
                <span>{{ modelValue ? modelValue[row.trackByName]: null }}</span>
            </p>
        </div>
    </div>
    <div v-if="view === 'table' && modelValue">
        <p
            class="flex justify-center space-x-2 ml-auto rtl:ml-0 rtl:mr-auto min-h-4 px-2 py-0.5 text-sm font-medium tracking-tight rounded-xl bg-primary-200 dark:text-primary-500 text-primary-600 my-auto"
            :style="modelValue.hasOwnProperty('color') ? 'background-color: ' + modelValue.color : ''"
        >
            <i v-show="modelValue.hasOwnProperty('icon')" :class="modelValue.icon" class="text-md my-auto"></i>
            <span>{{ modelValue ? modelValue[row.trackByName]: null }}</span>
        </p>
    </div>
    <!-- Modals Generator -->
    <JetDialogModal v-if="view === 'input' && row.add" :show="showAdd" maxWidth="sm">
        <template #title>
            <div class="flex justify-between">
                <h2 class="text-xl font-bold tracking-tight filament-modal-heading">{{ row.label }}</h2>
            </div>
        </template>

        <template #content>
            <form action="" enctype="multipart/form-data">
                <div v-for="(item, key) in row.rows" :key="key">
                    <div v-if="item.reactive">
                        <Component
                            v-if="item.vue === 'ViltHasOne.vue' && addForm[item.reactiveRow] && addForm[item.reactiveRow][item.reactiveBy] === item.reactiveWhere"
                            :is="item.vue.replace('.vue', '')" :row="item" :view="view"
                            v-model="addForm[item.name]" :message="errors[item.name]"></Component>
                        <Component
                            v-else-if="item.vue === 'ViltSlug.vue' && addForm[item.reactiveRow] && addForm[item.reactiveRow][item.reactiveBy] === item.reactiveWhere"
                            :is="item.vue.replace('.vue', '')" :row="item" :view="view"
                            v-model="addForm[item.name]" :message="errors[item.name]"></Component>
                        <Component
                            v-else-if="addForm[item.reactiveRow] && addForm[item.reactiveRow][item.reactiveBy] === item.reactiveWhere"
                            :is="item.vue.replace('.vue', '')" :row="item" :view="view"
                            v-model="addForm[item.name]" :message="errors[item.name]"></Component>
                    </div>
                    <div v-if="!item.reactive">
                        <Component v-if="item.vue === 'ViltHasOne.vue'" :is="item.vue.replace('.vue', '')" :row="item"
                                   :view="view" v-model="addForm[item.name]"
                                   :message="errors[item.name]">
                        </Component>
                        <Component v-else-if="item.vue === 'ViltSlug.vue'" :is="item.vue.replace('.vue', '')"
                                   :row="item"
                                   :view="view" v-model="addForm[item.name]"
                                   :message="errors[item.name]">
                        </Component>
                        <Component v-else :is="item.vue.replace('.vue', '')" :row="item" :view="view"
                                   v-model="addForm[item.name]" :message="errors[item.name]">
                        </Component>
                    </div>
                </div>
            </form>
        </template>

        <template #footer>
            <div class="flex flex-wrap items-center gap-4 filament-modal-actions rtl:space-x-reverse">
                <button
                    @click.prevent="create"
                    class="filament-button inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700 filament-page-modal-button-action">
                    {{ trans('global.save') }}
                </button>
                <button
                    class="filament-button inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm text-gray-800 bg-white border-gray-300 hover:bg-gray-50 focus:ring-primary-600 focus:text-primary-600 focus:bg-primary-50 focus:border-primary-600 dark:bg-gray-800 dark:hover:bg-gray-700 dark:border-gray-600 dark:hover:border-gray-500 dark:text-gray-200 dark:focus:text-primary-400 dark:focus:border-primary-400 dark:focus:bg-gray-800 filament-page-modal-button-action"
                    @click="showAdd = !showAdd">
                    {{ trans('global.close') }}
                </button>
            </div>
        </template>
    </JetDialogModal>
</template>

<script>
import {defineComponent} from "vue";
import JetInputError from "@@/Jetstream/InputError.vue";
import Multiselect from "@suadelabs/vue3-multiselect";
import "@suadelabs/vue3-multiselect/dist/vue3-multiselect.css";
import ViltLabel from "$$/ViltLabel.vue";
import ViltError from "$$/ViltError.vue";
import JetSecondaryButton from '@@/Jetstream/SecondaryButton.vue';
import JetButton from '@@/Jetstream/Button.vue';
import JetDialogModal from '@@/Jetstream/DialogModal.vue';
import ViltText from '$$/ViltText.vue';
import ViltTel from '$$/ViltTel.vue';
import ViltTextArea from '$$/ViltTextArea.vue';
import ViltNumber from '$$/ViltNumber.vue';
import ViltSelect from '$$/ViltSelect.vue';
import ViltSwitch from '$$/ViltSwitch.vue';
import ViltColor from '$$/ViltColor.vue';
import ViltEmail from '$$/ViltEmail.vue';
import ViltDate from '$$/ViltDate.vue';
import ViltDateTime from '$$/ViltDateTime.vue';
import ViltTime from '$$/ViltTime.vue';
import ViltMedia from '$$/ViltMedia.vue';
import ViltRepeater from '$$/ViltRepeater.vue';
import ViltSchema from '$$/ViltSchema.vue';
import ViltRich from '$$/ViltRich.vue';
import ViltRelation from '$$/ViltRelation.vue';
import ViltSlug from '$$/ViltSlug.vue';
import ViltIcon from '$$/ViltIcon.vue';


export default defineComponent({
    components: {
        ViltIcon,
        ViltSlug,
        ViltRelation,
        ViltRich,
        ViltSchema,
        ViltRepeater,
        ViltMedia,
        ViltTime,
        ViltDateTime,
        ViltDate,
        ViltEmail,
        ViltColor,
        ViltSwitch,
        ViltText,
        ViltTel,
        ViltTextArea,
        ViltNumber,
        ViltSelect,
        JetInputError,
        Multiselect,
        ViltLabel,
        ViltError,
        JetSecondaryButton,
        JetButton,
        JetDialogModal,
    },
    computed: {
      lang(){
          return this.$page.props.data.trans;
      }
    },
    data() {
        return {
            value: "",
            records: [],
            isLoading: false,
            showAdd: false,
            addForm: {}
        };
    },
    beforeUpdate() {

    },
    mounted() {
        this.initModel();
    },
    watch: {
        value: function (val) {
            if (this.view === 'input') {
                this.$emit("update:modelValue", val);
                this.$emit("change");
            }
        },
        modelValue: function (val) {
            if (this.view === 'input' && this.modelValue) {
                this.value = val;
            }
        },
        row: {
            handler(val){
                this.initModel();
            },
            deep: true
        }
    },
    methods: {
        initModel(){
            if (this.view !== 'input') {
                if (this.modelValue) {
                    this.value = this.modelValue;
                } else {
                    if (this.row.default && this.view === 'input') {
                        this.value = this.row.default;
                    }
                }
            }

            let _this = this;
            if (this.modelValue && this.view === 'input') {
                this.isLoading = true;
                axios
                    .post(route("select"), {
                        model_type: this.row.model,
                        model_id: this.modelValue.id,
                    })
                    .then((response) => {
                        _this.value = response.data.data;
                        _this.isLoading = false;
                    });
            }


            if (this.row.model !== null && this.view === 'input') {
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

            if (this.row.add && this.view === 'input') {
                for(let i=0; i<this.row.rows.length; i++){
                    this.addForm[this.row.rows[i].name] = "";
                }
            }

            this.value = "";

        },
        create(){
            this.addForm.model = this.row.model;
            this.isLoading = true;
            let form = this.$inertia.form(this.addForm);
            form.post(route('create'), {
                onSuccess: ()=>{
                    this.initModel();
                    this.showAdd = false;
                    this.isLoading = false;
                }
            });
        },
        trans(key){
            return this.lang[key] ? this.lang[key] : key;
        },
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
        openAdd(){
            this.addForm = {};
            if (this.row.add && this.view === 'input') {
                for(let i=0; i<this.row.rows.length; i++){
                    this.addForm[this.row.rows[i].name] = "";
                }
            }
            this.showAdd = !this.showAdd
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
        },
        errors: {
            Object,
            default: {}
        }
    },
});
</script>
