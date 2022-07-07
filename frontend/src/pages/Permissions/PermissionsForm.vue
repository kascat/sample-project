<template>
  <div class="full-width q-pa-lg items-start">
    <q-card>
      <q-card-section>
        <permissions-form />
      </q-card-section>
    </q-card>
  </div>
</template>

<script setup>
import PermissionsForm from '../../components/permissions/PermissionsFormComponent.vue'
</script>

<style scoped>
  .full-width {
    width: 100%;
  }
</style>
