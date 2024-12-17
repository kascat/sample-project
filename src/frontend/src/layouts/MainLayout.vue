<template>
  <q-layout view="lHr LpR lFr" class="bg-grey-1">
    <q-header :class="menuColorClass">
      <q-toolbar class="q-pa-none">
        <div class="full-width row justify-between">
          <q-btn
            flat
            dense
            icon="menu"
            aria-label="Menu"
            @click="toggleLeftDrawer()"
            color="grey-4"
            size="lg"
          />

          <q-btn-dropdown flat icon="account_circle" color="grey-4" size="lg">
            <user-menu/>
          </q-btn-dropdown>
        </div>
      </q-toolbar>
    </q-header>

    <q-drawer
      v-model="showLeftDrawer"
      show-if-above
      :mini="leftDrawerMini && showLeftDrawerAsMini"
      :mini-width="75"
      side="left"
      @mouseover="openLeftMiniDrawer(true)"
      @mouseleave="openLeftMiniDrawer(false)"
      :class="menuColorClass"
      class="overflow-light"
    >
      <q-toolbar :class="menuColorClass" class="fixed-top" style="z-index: 1">
        <q-btn
          v-show="!leftDrawerMini || !showLeftDrawerAsMini"
          flat
          dense
          :icon="leftDrawerMini ? 'arrow_right' : 'arrow_left'"
          aria-label="Menu"
          @click="toggleLeftMiniDrawer()"
          color="grey-4"
          size="lg"
          class="absolute-left btn-toggle-mini"
          style="width: 20px"
        >
          <q-tooltip>
            {{ t(leftDrawerMini ? 'expand' : 'collapse') }}
          </q-tooltip>
        </q-btn>

        <!-- Image in mini mode -->
        <img
          v-show="leftDrawerMini && showLeftDrawerAsMini"
          src="~assets/icon.png"
          style="max-height:50px;"
          class="absolute-center"
          alt="logo"
        >
        <!-- Image in expanded mode -->
        <img
          v-show="!leftDrawerMini || !showLeftDrawerAsMini"
          src="~assets/logo.png"
          style="max-height:50px;"
          class="absolute-center"
          alt="logo"
        >
      </q-toolbar>

      <drawer-menu-simplex class="q-mt-xl"/>
    </q-drawer>

    <q-page-container>
      <div v-if="loggedUser.expires_in" class="q-pt-md q-px-md">
        <q-banner inline-actions rounded class="bg-primary text-white">
          <div>
            {{ t('access_expiration_message', {date: formatIsoDateBr(loggedUser.expires_in)}) }}
          </div>
        </q-banner>
      </div>

      <router-view/>
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { loggedUser } from 'boot/user';
import { computed, onMounted, ref } from 'vue';
import { t } from 'src/services/utils/i18n';
import DrawerMenuSimplex from 'src/layout_components/DrawerMenuSimplex';
import UserMenu from 'src/layout_components/UserMenu';
import { formatIsoDateBr } from 'src/services/utils/date';

const showLeftDrawer = ref(false);
const leftDrawerMini = ref(false);
const mouseInsideLeftDrawer = ref(false);
const showLeftDrawerAsMini = ref(true);

const menuColorClass = computed(() => {
  return {
    development: 'bg-grey-5',
    homolog: 'bg-purple-4',
    production: 'bg-primary',
  }[process.env.ENVIRONMENT] || 'bg-red-5';
});

onMounted(() => {
  leftDrawerMini.value = !!+localStorage.getItem(loggedUser.id + '_leftDrawerMini');
});

function toggleLeftDrawer() {
  showLeftDrawer.value = !showLeftDrawer.value;
}

function toggleLeftMiniDrawer() {
  leftDrawerMini.value = !leftDrawerMini.value;
  localStorage.setItem(loggedUser.id + '_leftDrawerMini', +leftDrawerMini.value);
}

function openLeftMiniDrawer(open) {
  mouseInsideLeftDrawer.value = open;

  if (open) {
    setTimeout(() => {
      if (mouseInsideLeftDrawer.value) {
        showLeftDrawerAsMini.value = false;
      }
    }, 300);
  } else {
    showLeftDrawerAsMini.value = true;
  }
}
</script>

<style scoped>
.q-drawer--mobile .btn-toggle-mini {
  display: none;
}
</style>
