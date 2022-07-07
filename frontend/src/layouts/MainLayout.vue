<template>
  <q-layout view="lHh Lpr lFf">
    <q-header class="bg-white">
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
                  <q-item-label>Logout</q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </q-btn-dropdown>
        </div>
      </q-toolbar>
    </q-header>

    <app-drawer ref="appDrawer"/>

    <q-page-container>
      <router-view/>
    </q-page-container>
  </q-layout>
</template>

<script setup>
import AppDrawer from 'src/components/drawer/AppDrawer.vue'
import { postLogoutUser } from "src/services/login/login-api"
import { resetLoggedUser, resetUserInLocalStorage } from "boot/user"
import { useRouter } from 'vue-router'
import { Notify } from "quasar"
import { ref } from "vue";

const router = useRouter()
const appDrawer = ref(null)

const logoutUser = async () => {
  try {
    await postLogoutUser()

    resetLoggedUser()
    resetUserInLocalStorage()
    router.push({ name: 'login' })
  } catch {
    Notify.create({
      message: 'Falha ao deslogar!',
      type: 'negative'
    })
  }
}
</script>

<style scoped>
.pre-text-info {
  font-size: .75rem;
  font-weight: bold;
}
</style>
