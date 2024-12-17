<template>
  <div class="q-pa-md">
    <div class="row justify-between q-mb-md items-center">
      <h5 class="q-ma-none text-weight-regular">
        {{ t('permissions_label') }}
      </h5>

      <q-btn
        icon="add"
        class="float-right"
        :label="t('register')"
        color="primary"
        outline
        :to="{ name: 'permissions_create' }"
      />
    </div>

    <permissions-list/>
  </div>
</template>

<script setup>
import PermissionsList from 'src/components/permissions/PermissionsListComponent.vue';
import { t } from 'src/services/utils/i18n';
</script>
