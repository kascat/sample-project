<template>
  <q-layout view="lHh Lpr lFf">
    <q-header :class="menuColorClass">
      <q-toolbar>
        <div class="full-width q-pt-sm">
          <q-btn
            flat
            dense
            round
            icon="menu"
            aria-label="Menu"
            @click="appDrawer.toggleDrawer()"
            color="grey-6"
            size="20px"
          />
          <q-btn-dropdown
            flat
            round
            icon="account_circle"
            color="grey-6"
            class="absolute-right"
            size="20px"
          >
            <q-list>
              <q-item
                clickable
                v-close-popup
                @click="logoutUser"
              >
                <q-item-section>
                  <q-item-label>{{ t('logout') }}</q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </q-btn-dropdown>
        </div>
      </q-toolbar>
    </q-header>

    <app-drawer ref="appDrawer"/>

    <q-page-container class="bg-grey-1">
      <router-view/>
    </q-page-container>
  </q-layout>
</template>

<script setup>
import AppDrawer from 'src/components/drawer/AppDrawer.vue';
import { postLogoutUser } from 'src/services/login/login-api';
import { resetLoggedUser, resetUserInLocalStorage } from 'boot/user';
import { useRouter } from 'vue-router';
import { Notify } from 'quasar';
import { computed, ref } from 'vue';
import { t } from 'src/services/utils/i18n';

const router = useRouter();
const appDrawer = ref(null);

const menuColorClass = computed(() => {
  return {
    development: 'bg-grey-4',
    homolog: 'bg-orange-4',
    production: 'bg-purple-3',
  }[process.env.ENVIRONMENT] || 'bg-red-5';
});

const logoutUser = async () => {
  try {
    await postLogoutUser();

    resetLoggedUser();
    resetUserInLocalStorage();
    router.push({ name: 'login' });
  } catch {
    Notify.create({
      message: t('unexpected_error'),
      type: 'negative',
    });
  }
};
</script>

<style scoped>

</style>
