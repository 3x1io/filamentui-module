<template>
    <div class="py-2 px-2" v-if="view === 'input'">
        <label v-if="row.name" :for="row.name" class="text-sm font-normal capitalize dark:text-gray-100" :class="{'text-red-600': message}">{{
                row.label ? row.label : row.name
            }}
            <span v-if="row.required" class="text-red-600 text-bold">*</span>
        </label>
        <div class="">
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
        <small v-if="row.hint" class="text-gray-400 mx-2">{{row.hint}}</small>
        <JetInputError v-if="message" :message="message" class="mt-2" />
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
                <div v-if="modelValue && row.multi">
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
        <div v-if="value && row.multi">
            <img
                @click.prevent="popUp"
                :src="value[0]"
                class="object-cover w-20 h-20 mx-auto rounded-full"
            />
        </div>
        <div v-else-if="!row.multi">
            <img
                @click.prevent="popUp"
                :src="value"
                class="object-cover w-20 h-20 rounded-full border"
            />
        </div>
        <div v-else>
            <img
                :src="$page.props.data.appUrl + '/placeholder.webp'"
                class="object-cover w-20 h-20 mx-auto rounded-full border"
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

// // Create component
const FilePond = vueFilePond(
    FilePondPluginFileValidateType,
    FilePondPluginImagePreview,
);

export default defineComponent({
    components: {
        JetInputError,
        FilePond,
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
    },
    data() {
        return {
            value: [],
            images: []
        };
    },
    watch: {
        value: function (val) {
            if(this.view === 'input') {
                this.$emit("update:modelValue", val);
            }
        },
        modelValue: function (val) {
            if(this.view === 'input' && this.modelValue) {
                this.value = val;
            }
        },
    },
    mounted() {
        if(this.modelValue){
            if(this.view === 'input'){
                if(this.row.multi) {
                    this.value = this.modelValue;
                } else {
                    this.value.push(this.modelValue);
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
        }
    },
    methods: {
        handleFilePondInit: function () {
            this.$refs[this.row.name].getFiles();
        },
        addNewFile(error,file){
            this.images.push(file.file);
            this.$emit("update:modelValue", this.images);
        },
        removeFile(error,file){
            this.images.splice(this.images.indexOf(file.file), 1);
        },
        popUp(){
            if(this.row.multi){
                this.$emit('popUp', this.value);
            }
            else {
                this.$emit('popUp', [this.value]);
            }
        }
    },
});
</script>
