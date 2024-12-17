<template>
  <div class="q-pa-md">
    <div class="row justify-between q-mb-md items-center">
      <h5 class="q-ma-none text-weight-regular">
        {{ t('users') }}
      </h5>

      <q-btn
        icon="add"
        class="float-right"
        :label="t('register')"
        color="primary"
        outline
        :to="{ name: 'users_create' }"
      />
    </div>

    <users-list/>
  </div>
</template>

<script setup>
import UsersList from 'src/components/users/UsersListComponent.vue';
import { t } from 'src/services/utils/i18n';
</script>
