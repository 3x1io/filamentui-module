<template>
    <div>
<!--        <transition leave-active-class="transition ease-in duration-1000" leave-from-class="opacity-100" leave-to-class="opacity-0">-->

<!--        </transition>-->
        <div v-show="on" class="flex justify-start space-x-2 text-lg bg-success-500 text-white w-full border-success-400 py-2  px-6 rounded-lg shadow-sm my-auto">
            <i class="bx bx-check" style="font-size: 28px"></i>
            <h2><slot /></h2>
        </div>
    </div>
</template>

<script>
    import { defineComponent } from 'vue'

    export default defineComponent({
        props: ['on'],
    })
</script>
