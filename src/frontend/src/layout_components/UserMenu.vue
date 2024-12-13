<template>
  <q-list style="width: 300px; max-width: 95vw">
    <q-item class="q-pa-none">
      <q-item-section class="rounded-borders bg-grey-3 q-ma-sm q-pa-md">
        <div class="text-blue-grey-10">
          {{ loggedUser.name }}
        </div>
        <div class="text-blue-grey-8">
          {{ loggedUser.email }}
        </div>
      </q-item-section>
    </q-item>

    <q-separator/>

    <span
      v-for="(item, i) in menuItems"
      :key="i"
    >
      <q-separator v-if="item.separator"/>

      <q-item
        v-if="!item.children"
        clickable
        v-close-popup
        class="text-grey-10"
        active-class="active-link active-item"
        :to="item.to"
        @click="item.action"
      >
        <q-item-section side>
          <q-icon :name="item.icon"/>
        </q-item-section>
        <q-item-section>
          {{ t(item.label) }}
        </q-item-section>
      </q-item>

      <q-expansion-item
        v-else-if="item.children"
        header-class="text-grey-9"
        expand-icon="chevron_right"
        expanded-icon="keyboard_arrow_down"
      >
        <template v-slot:header>
          <q-item-section side>
            <q-icon :name="item.icon" color="grey-9"/>
          </q-item-section>
          <q-item-section>
            {{ t(item.label) }}
          </q-item-section>
        </template>
        <q-list>
          <q-item
            class="text-grey-9 q-pl-lg"
            v-for="(children, j) in item.children"
            :key="j"
            clickable
            v-close-popup
            active-class="active-link-children active-item"
            :to="children.to"
            @click="item.action"
          >
            <q-item-section side>
              <q-icon :name="children.icon"/>
            </q-item-section>
            <q-item-section class="q-pl-sm">
              <q-item-label>
                {{ t(children.label) }}
              </q-item-label>
            </q-item-section>
          </q-item>
        </q-list>
      </q-expansion-item>
    </span>
  </q-list>
</template>

<script setup>
import { loggedUser, resetLoggedUser, resetUserInLocalStorage } from 'boot/user';
import { t } from 'src/services/utils/i18n';
import userMenu from 'src/services/menu/user-menu';
import { logoutUser } from 'src/services/auth/auth-api';
import { Notify } from 'quasar';
import { useRouter } from 'vue-router';

const router = useRouter();

const menuItems = userMenu({
  logout: async () => {
    try {
      await logoutUser();

      resetLoggedUser();
      resetUserInLocalStorage();
      router.push({ name: 'login' });
    } catch (e) {
      Notify.create({
        message: t('unexpected_error'),
        type: 'negative',
      });
    }
  },
});
</script>

<style scoped lang="scss">
.active-item {
  font-weight: bold;
  border-right: 3px solid transparent;
}

.active-link i,
.active-link {
  border-color: $primary;
  color: $primary !important;
}

.active-link-children i,
.active-link-children {
  border-color: $primary;
  color: $primary !important;
}
</style>
