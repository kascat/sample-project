<template>
  <q-drawer
    v-model="drawerOpen"
    show-if-above
    :mini="false"
    :mini-width="75"
    :side="drawerSide"
  >
    <sidebar-menu/>
  </q-drawer>
</template>


<script setup>
import { ref } from 'vue';
import SidebarMenu from './DrawerMenuSimplex';

const drawerOpen = ref(false);
const drawerSide = ref('left');

defineExpose({
  toggleDrawer: () => {
    drawerOpen.value = !drawerOpen.value;
  },
});
</script>
