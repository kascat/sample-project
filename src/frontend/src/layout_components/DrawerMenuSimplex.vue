<template>
  <q-list>
    <q-item class="q-pa-none">
      <q-item-section class="rounded-borders bg-grey-3 q-ma-sm q-pa-md">
        <div class="row q-col-gutter-sm">
          <div class="text-subtitl text-bold text-center text-blue-grey-10 col-12">
            {{ loggedUser.name }}
            <q-separator class="q-mt-sm"/>
          </div>
          <div class="text-blue-grey-10 col-12 text-center">
            {{ loggedUser.email }}
          </div>
        </div>
      </q-item-section>
    </q-item>
    <span
      v-for="(item, i) in generalItems"
      :key="i"
    >
      <q-item
        v-if="!item.children"
        clickable
        v-ripple:green
        class="text-grey-9"
        active-class="active-link active-item"
        :to="item.to"
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
            :to="children.to"
            clickable
            v-ripple:blue
            active-class="active-link-children active-item"
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
import { loggedUser } from 'boot/user';
import { generateMenu } from 'src/services/menu/sidebar-menu';
import { t } from 'src/services/utils/i18n';

const generalItems = generateMenu();
</script>

<style scoped lang="scss">
.active-item {
  font-weight: bold;
  border-left: 3px solid transparent;
}

.active-link i,
.active-link {
  border-color: $secondary;
  color: $secondary !important;
}

.active-link-children i,
.active-link-children {
  border-color: $secondary;
  color: $secondary !important;
}
</style>
