<template>
  <div class="full-width q-pa-lg items-start">
    <q-card>
      <q-card-section>
        <users-form />
      </q-card-section>
    </q-card>
  </div>
</template>

<script setup>
import UsersForm from '../../components/users/UsersFormComponent.vue'
</script>

<style scoped>
  .full-width {
    width: 100%;
  }
</style>
