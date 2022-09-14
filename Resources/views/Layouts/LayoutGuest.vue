<script setup>
import { useStyleStore } from "@@/Stores/style.js";

const styleStore = useStyleStore();
</script>

<template>
  <div>
      <slot />
  </div>
</template>
