<template>
    <teleport to="body">
        <transition leave-active-class="duration-200">
            <div v-show="show" class="fixed inset-0 z-40 flex min-h-screen overflow-y-auto transition p-4" :class="{'items-center':maxWidth==='sm', 'items-start':maxWidth==='2xl'}" scroll-region>
                <transition
                        enter-active-class="ease-in duration-150"
                        enter-from-class="opacity-0"
                        enter-to-class="opacity-100"
                        leave-active-class="ease-out duration-150"
                        leave-from-class="opacity-100"
                        leave-to-class="opacity-0">
                    <div v-show="show" class="filament-modal-close-overlay fixed inset-0 w-full h-full bg-black/50 cursor-pointer" @click="close">
                        <div class="relative w-full cursor-pointer pointer-events-none " :class="{'my-auto':maxWidth==='sm'}"></div>
                    </div>
                </transition>

                <transition
                        enter-active-class="ease-in duration-150"
                        enter-from-class="opacity-0"
                        enter-to-class="opacity-100"
                        leave-active-class="ease-out duration-150"
                        leave-from-class="opacity-0"
                        leave-to-class="opacity-100"
                >
                    <div v-show="show" class="
                    transition-all
                    transform
                    filament-modal-window
                    p-2
                    bg-white
                    cursor-default
                    pointer-events-auto
                    dark:bg-gray-800
                    relative
                    rounded-xl
                    mx-auto
                    " :class="maxWidthClass">
                        <slot></slot>
                    </div>
                </transition>
            </div>
        </transition>
    </teleport>
</template>

<script>
import { defineComponent, onMounted, onUnmounted } from "vue";

export default defineComponent({
        emits: ['close'],

        props: {
            show: {
                default: false
            },
            maxWidth: {
                default: '2xl'
            },
            closeable: {
                default: true
            },
        },

        watch: {
            show: {
                immediate: true,
                handler: (show) => {
                    if (show) {
                        document.body.style.overflow = 'hidden'
                    } else {
                        document.body.style.overflow = null
                    }
                }
            }
        },

        setup(props, {emit}) {
            const close = () => {
                if (props.closeable) {
                    emit('close')
                }
            }

            const closeOnEscape = (e) => {
                if (e.key === 'Escape' && props.show) {
                    close()
                }
            }

            onMounted(() => document.addEventListener('keydown', closeOnEscape))
            onUnmounted(() => {
                document.removeEventListener('keydown', closeOnEscape)
                document.body.style.overflow = null
            })

            return {
                close,
            }
        },

        computed: {
            maxWidthClass() {
                return {
                    'sm': 'sm:max-w-sm w-3/4',
                    'md': 'sm:max-w-md',
                    'lg': 'sm:max-w-lg' ,
                    'xl': 'sm:max-w-xl',
                    '2xl': 'sm:max-w-2xl  w-full',
                }[this.maxWidth]
            }
        }
    })
</script>
