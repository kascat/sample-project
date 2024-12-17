<template>
  <div class="q-pa-md">
    <div class="row q-col-gutter-sm q-mb-md items-center">
      <div>
        <q-btn
          color="primary"
          icon="arrow_back"
          dens
          outline
          rounded
          :to="{ name: 'permissions' }"
        >
          <q-tooltip :offset="[5, 5]">
            {{ t('to_go_back') }}
          </q-tooltip>
        </q-btn>
      </div>
      <h5 class="q-mt-none q-mb-none text-weight-regular">
        {{ t('permission') }}
      </h5>
    </div>

    <permissions-form :form-item-id="route.params.id"/>
  </div>
</template>

<script setup>
import PermissionsForm from 'src/components/permissions/PermissionsFormComponent.vue';
import { t } from 'src/services/utils/i18n';
import { useRoute } from 'vue-router';

const route = useRoute();
</script>
