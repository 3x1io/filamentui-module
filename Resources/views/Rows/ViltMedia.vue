<template>
    <div class="py-2 px-2" v-if="view === 'input'">
        <ViltLabel :row="row" v-if="label" />
        <div v-if="!showURLMedia">
            <file-pond
                :allowReorder="true"
                :name="row.name"
                :max-files="row.max"
                :ref="row.name"
                :required="row.required"
                :disabled="row.disabled"
                styleImageEditButtonEditItemPosition="center"
                :class="{'border-danger-600 ring-danger-600' : message}"
                :allow-drop="true"
                :label-idle="`
                <div class='filepond-placeholder'>
                    <div>
                        <i class='bx bx-upload bx-sm'></i>
                    </div>
                    <div>
                        <span>`+(row.placeholder ? row.placeholder : row.name)+`</span>
                    </div>
                </div>
            `"
                v-bind:allow-multiple="row.multi"
                :accepted-file-types="row.type === 'image' ? 'image/jpeg, image/png' : null"
                v-bind:files="value"
                v-on:init="handleFilePondInit"
                @addfile="addNewFile"
                @removefile="removeFile"
            />
        </div>
        <div v-if="showURLMedia">
            <input
                class="block my-2 w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500 border-gray-300 dark:border-gray-600"
                :class="{'border-danger-600 ring-danger-600' : message}"
                v-model="mediaURL"
                @input="changeImage"
                type="text"
                :placeholder="row.placeholder"
                :id="row.name"
                :name="row.name"
                :required="row.required"
                :disabled="row.disabled"
            />
        </div>
        <div v-if="row.type === 'lib'" class="flex justify-start w-full space-x-2">
            <button
                @click.prevent="openMediaLib"
                class="w-full filament-button inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700 filament-page-modal-button-action">
                <i class="bx bxs-image"></i>
            </button>
            <button
                @click.prevent="showURLMedia = !showURLMedia"
                class="w-full filament-button inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700 filament-page-modal-button-action">
                <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <i v-else class="bx bx-link"></i>
            </button>
        </div>

        <ViltError :row="row" :message="message" />

        <!-- Modals Generator -->
        <JetDialogModal v-if="view === 'input'" :show="showMediaLibModal">
            <template #content>
                <div v-cloak>
                    <div class="bg-gray-200 py-4 px-4 rounded-t-lg">
                        <div class="flex justify-between">
                            <div class="flex justify-start space-x-2">
                                <button class="inline-flex items-center justify-center px-4 my-2 font-medium tracking-tight text-white transition-colors border border-transparent rounded-lg shadow focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset filament-button dark:focus:ring-offset-0 h-9 focus:ring-white bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700 filament-page-button-action" @click.prevent="refresh()"><i class="bx bx-refresh"></i></button>
                            </div>
                            <div class="flex justify-start space-x-2">
                                <button v-if="Object.keys(folder).length && data.hasOwnProperty('size')" @click.prevent="deleteMedia()" class="inline-flex items-center justify-center px-4 my-2 font-medium tracking-tight text-white  transition-colors border border-transparent rounded-lg shadow focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset filament-button dark:focus:ring-offset-0 h-9 focus:ring-white bg-danger-500 hover:bg-danger-600 focus:bg-danger-700 focus:ring-offset-danger-700 filament-page-button-action">Delete</button>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-100 py-2 px-2 flex justify-start space-x-2">
                        <h1 @click.prevent="refresh" :class="{'text-primary-500 font-bold cursor-pointer':Object.keys(folder).length}">Media Library</h1>
                        <span v-if="Object.keys(folder).length" class="text-gray-600"> <span class="text-gray-900">></span> {{data.file_name}}</span>
                    </div>
                    <div class="grid grid-cols-12 gap-2" v-if="Object.keys(media).length">
                        <div :class="{'col-span-12' : !data, 'col-span-9': data}">
                            <div class="grid grid-cols-3 gap-4 py-4 px-4">
                                <div
                                    v-if="!folder.length"
                                    v-for="(item, key) in media"
                                    @click.prevent="showData(key)"
                                    @dblclick.prevent="setFolder(item)"
                                    :class="{'bg-primary-500 text-white': data.file_name === key}"
                                    class="overflow-hidden cursor-pointer bg-gray-100 hover:bg-primary-500 hover:text-white border py-4 px-4 flex flex-col justify-center space-y-2 rounded-lg text-center">
                                    <i class="bx bxs-folder bx-lg"></i>
                                    <div>{{key}}</div>
                                </div>
                                <div v-else
                                     v-for="(item, key) in folder"
                                     @click.prevent="showData(item)"
                                     :class="{'bg-primary-500 text-white': data.id === item.id}"
                                     class="overflow-hidden cursor-pointer bg-gray-100 hover:bg-primary-500 hover:text-white border py-4 px-4 flex flex-col justify-center space-y-2 rounded-lg text-center">
                                    <div class="w-16 h-16 bg-cover mx-auto" :style="'background-image: url('+item.original_url+');'"></div>
                                    <div class="my-auto">
                                        <span class="text-sm">{{item.file_name}}</span>
                                        <br>
                                        <span class="text-sm text-gray-200">{{item.size/1000}}kb</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col w-full col-span-3" v-if="Object.keys(data).length">
                            <div class="bg-gray-100/50 border-l border-b py-8 px-4 flex justify-center space-x-4 ">
                                <i v-if="data.mime_type === 'folder'" class="bx bxs-folder bx-lg text-center"></i>
                                <img v-else :src="data.original_url" class="w-full"/>
                            </div>
                            <div class="border-l py-2 px-2  h-full text-sm overflow-scroll">
                                <div>
                                    <span class="text-gray-300">Title: </span>
                                    {{ data.file_name }}
                                </div>
                                <div>
                                    <span class="text-gray-300">Type: </span>
                                    {{ data.mime_type }}
                                </div>
                                <div v-if="data.size">
                                    <span class="text-gray-300">Size: </span>
                                    {{ data.size/1000 }}kp
                                </div>
                                <div v-if="data.original_url">
                                    <span class="text-gray-300">Public URL:</span>
                                    <a :href="data.original_url" class="text-primary-500" target="_blank">Click Here</a>
                                </div>
                                <div v-if="data.updated_at">
                                    <span class="text-gray-300">Last Modified: </span>
                                    <br>
                                    {{data.updated_at}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="relative p-8 text-center col-span-12" v-else>
                        <div class="flex items-center justify-center p-4">
                            <div
                                class="flex flex-col items-center justify-center flex-1 p-6 mx-auto space-y-6 text-center bg-white filament-tables-empty-state dark:bg-gray-800">
                                <div
                                    class="flex items-center justify-center w-16 h-16 rounded-full text-primary-500 bg-primary-50 dark:bg-gray-700">
                                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                         stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>

                                </div>

                                <div class="max-w-md space-y-1">
                                    <h2 class="text-xl font-bold tracking-tight filament-tables-empty-state-heading dark:text-white">
                                        {{ trans('global.empty') }}
                                    </h2>

                                    <p
                                        class="text-sm font-medium text-gray-500 whitespace-normal filament-tables-empty-state-description dark:text-gray-400">

                                    </p>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- Confirm Generator -->
                <JetDialogModal
                    maxWidth="sm"
                    :show="showDelete"
                >
                    <template #content>
                        <div class="p-4 space-y-2 text-center dark:text-white">
                            <h2 class="text-xl font-bold tracking-tight filament-modal-heading" id="ozo9fHMwMlC1FjErUz3l-table-bulk-action.heading">
                                Delete
                            </h2>
                            <h3 class="text-gray-500 filament-modal-subheading">
                                Do You Went to Delete This Media Item?
                            </h3>
                        </div>
                    </template>

                    <template #footer>
                        <div class="filament-modal-actions grid gap-2 grid-cols-[repeat(auto-fit,minmax(0,1fr))]">
                            <button
                                class="filament-button inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-danger-600 hover:bg-danger-500 focus:bg-danger-700 focus:ring-offset-danger-700 filament-tables-modal-button-action"
                                @click.prevent="deleteMediaItem()"
                            >
                                Delete
                            </button>
                            <button
                                class="filament-button inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm text-gray-800 bg-white border-gray-300 hover:bg-gray-50 focus:ring-primary-600 focus:text-primary-600 focus:bg-primary-50 focus:border-primary-600 dark:bg-gray-800 dark:hover:bg-gray-700 dark:border-gray-600 dark:hover:border-gray-500 dark:text-gray-200 dark:focus:text-primary-400 dark:focus:border-primary-400 dark:focus:bg-gray-800 filament-tables-modal-button-action"
                                @click="showDelete = !showDelete"
                            >
                                {{ trans('global.close') }}
                            </button>
                        </div>
                    </template>
                </JetDialogModal>
            </template>

            <template #footer>
                <div class="flex flex-wrap items-center gap-4 filament-modal-actions rtl:space-x-reverse">
                    <button
                        @click.prevent="setMedia"
                        class="filament-button inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700 filament-page-modal-button-action">
                        <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ trans('media.select') }}
                    </button>
                    <button
                        class="filament-button inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm text-gray-800 bg-white border-gray-300 hover:bg-gray-50 focus:ring-primary-600 focus:text-primary-600 focus:bg-primary-50 focus:border-primary-600 dark:bg-gray-800 dark:hover:bg-gray-700 dark:border-gray-600 dark:hover:border-gray-500 dark:text-gray-200 dark:focus:text-primary-400 dark:focus:border-primary-400 dark:focus:bg-gray-800 filament-page-modal-button-action"
                        @click="showMediaLibModal = !showMediaLibModal">
                        {{ trans('global.close') }}
                    </button>
                </div>
            </template>
        </JetDialogModal>
    </div>
    <div
        class="flex justify-between py-2 my-4"
        v-if="view === 'view'"
    >
        <div>
            <p class="font-bold capitalize">{{ row.label ? row.label : row.name }}</p>
        </div>
        <div>
            <div v-if="modelValue">
                <div v-if="row.multi">
                    <div v-for="(item, key) in modelValue" :key="key">
                        <img
                            :src="item"
                            class="object-cover w-20 h-20 mx-auto rounded-full"
                        />
                    </div>
                </div>
                <div v-else>
                    <img
                        :src="modelValue"
                        class="object-cover w-20 h-20 rounded-full border"
                    />
                </div>
            </div>
            <div  v-else>
                <img
                    :src="$page.props.data.appUrl + '/placeholder.webp'"
                    class="object-cover w-20 h-20 rounded-full border"
                />
            </div>
        </div>
    </div>
    <div v-if="view === 'table'" class="p-4">
        <div v-if="modelValue">
            <div v-if="row.multi">
                <div v-for="(item, key) in modelValue" :key="key">
                    <img
                        :src="item"
                        class="mx-auto"
                    />
                </div>
            </div>
            <div v-else>
                <img
                    :src="modelValue"
                    class="mx-auto"
                />
            </div>
        </div>
        <div  v-else>
            <img
                :src="$page.props.data.appUrl + '/placeholder.webp'"
                class="mx-auto"
            />
        </div>


    </div>
</template>

<script>
import { defineComponent } from "vue";
// Import Vue FilePond
import vueFilePond from "vue-filepond";

// Import FilePond styles
import "filepond/dist/filepond.min.css";

// Import FilePond plugins
// Please note that you need to install these plugins separately



// Import image preview and file type validation plugins
import FilePondPluginFileValidateType from "filepond-plugin-file-validate-type";
import FilePondPluginImagePreview from "filepond-plugin-image-preview";

// Import image preview plugin styles
import "filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css";

import JetInputError from "@@/Jetstream/InputError.vue";

import ViltLabel from "$$/ViltLabel.vue";
import ViltError from "$$/ViltError.vue";
import JetDialogModal from '@@/Jetstream/DialogModal.vue';


// // Create component
const FilePond = vueFilePond(
    FilePondPluginFileValidateType,
    FilePondPluginImagePreview,
);

export default defineComponent({
    components: {
        JetInputError,
        FilePond,
        ViltLabel,
        ViltError,
        JetDialogModal
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
    data() {
        return {
            value: [],
            images: [],
            showMediaOnSelect:false,
            showMediaLibModal:false,
            folder: [],
            data: {},
            showDelete: false,
            media: [],
            loading: false,
            showURLMedia:false,
            mediaURL: ""
        };
    },
    watch: {
        value: function (val) {
            if(this.view === 'input') {
                this.$emit("update:modelValue", val);
            }
        },
        modelValue: function (val) {
            this.images = [];
            this.value = [];
            if(this.view === 'input' && this.modelValue) {
                if(this.row.multi){
                    this.images = val;
                    this.value = val;
                }
                else {
                    this.images = val;
                    this.value = val;
                }
            }
            else {
                this.images = [];
                this.value = [];
            }

        },
    },
    mounted() {
        if(this.modelValue){
            if(this.view === 'input'){
                if(this.row.multi) {
                    this.value = this.modelValue;
                } else {
                    this.value = this.modelValue;
                    this.images = this.modelValue;
                }
            }
            else {
                if(this.row.multi){
                    this.value = this.modelValue[0];
                }
                else {
                    this.value = this.modelValue;
                }
            }

        }
        else {
            if (this.row.default) {
                if(this.row.multi) {
                    this.value = this.row.default;
                } else {
                    this.value.push(this.row.default);
                }
            }
            else {
                this.value = [];
                this.images = [];
            }
        }
    },
    computed: {
        lang(){
            return this.$page.props.data.trans;
        }
    },
    methods: {
        loadMedia(){
            axios.get(route('media.api')).then(data => {
                this.media = data.data.data;
            });
        },
        setFolder(item){
            this.folder = item;
        },
        showData(item){
            if(item.hasOwnProperty('collection_name')){
                this.data = item;
            }
            else {
                this.data.file_name = item;
                this.data.mime_type = "folder";
            }
        },
        refresh(){
            this.folder= [];
            this.data= {};
            this.showDelete=false;
            this.loadMedia();
        },
        deleteMedia(){
            this.showDelete = true;
        },
        deleteMediaItem(){
            this.$inertia.post(route('media.destroy', this.data.id),{}, {
                preserveScroll: true,
                onSuccess: () => {
                    this.showDelete = false;
                    this.$toast.success("Media Item Deleted Successfully");
                    this.refresh();
                }
            });
        },
        cropMedia(){
            this.$inertia.visit(route('media.crop', this.data.file_name));
        },
        trans(key){
            return this.lang[key] ? this.lang[key]: key;
        },
        handleFilePondInit: function () {
            this.$refs[this.row.name].getFiles();
        },
        addNewFile(error,file){
            try {
                this.images.push(file.file);
            }catch (e){}

            this.$emit("update:modelValue", this.images);
        },
        removeFile(error,file){
            if(this.row.multi){
                this.value.splice(this.value.indexOf(file.file), 1);
            }
            else {
                this.images = [];
                this.value= [];
            }

        },
        popUp(){
            if(this.row.multi){
                this.$emit('popUp', this.value);
            }
            else {
                this.$emit('popUp', [this.value]);
            }
        },
        openMediaLib(){
            this.loadMedia();
            this.folder= [];
            this.data= {};
            this.showDelete=false;
            this.showMediaLibModal = true;
        },
        setMedia(){
            this.images = [];
            this.value= [];
            this.loading = true;
            setTimeout(fn => {
                this.images = this.data.original_url;
                this.value = this.data.original_url;

                this.showMediaLibModal = false;
                this.loading = false;
            }, 500);
        },
        changeImage(event){
            let value = event.target.value;
            this.images = [];
            this.value= [];
            this.loading = true;
            setTimeout(fn => {
                this.showURLMedia = false;
                this.loading = false;
                this.images = value;
                this.value = value;
            }, 500);
            this.mediaURL = "";
        }
    },
});
</script>
