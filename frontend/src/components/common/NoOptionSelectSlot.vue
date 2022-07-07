<template>
  <q-item>
    <q-item-section class="text-italic text-grey">
      {{ label }}
    </q-item-section>
  </q-item>
</template>

<script setup>
defineProps({
  label: {
    default: 'Nenhuma opção'
  }
})
</script>
