<template>
  <div style="min-height: 100vh;" :class="menuColorClass">
    <q-toolbar class="bg-transparent">
      <img
        src="~assets/icon.png"
        style="max-height:50px;"
        class="absolute-center"
      >
    </q-toolbar>
    <q-card flat :class="menuColorClass">
      <q-card-section>
        <q-item
          clickable
          class="bg-grey-3 q-pt-md q-pb-md"
          style="border-radius: 1rem;"
        >
          <q-item-section>
            <div class="text-subtitle2 text-blue-grey-10">
              {{ loggedUser.name }}
            </div>
            <div class="text-blue-grey-8">
              {{ loggedUser.email }}
            </div>
          </q-item-section>
        </q-item>
      </q-card-section>
    </q-card>
    <q-list>
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
            <q-icon
              :name="item.icon"
            />
          </q-item-section>
          <q-item-section>
            {{ item.label }}
          </q-item-section>
        </q-item>
        <!--f is normal mode, display expansion items -->
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
              {{ item.label }}
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
                <q-icon
                  :name="children.icon"
                />
              </q-item-section>
              <q-item-section class="q-pl-sm">
                <q-item-label>
                  {{ children.label }}
                </q-item-label>
              </q-item-section>
            </q-item>
          </q-list>
        </q-expansion-item>
      </span>
    </q-list>
  </div>
</template>

<script setup>
import { loggedUser } from 'boot/user';
import { generateMenu } from 'src/services/utils/menu';
import { computed } from 'vue';

const menuColorClass = computed(() => {
  return {
    development: 'bg-grey-4',
    homolog: 'bg-orange-4',
    production: 'bg-purple-3',
  }[process.env.ENVIRONMENT] || 'bg-red-5';
});

const generalItems = generateMenu();
</script>

<style scoped lang="scss">
.active-item {
  font-weight: bold;
  border-right: 3px solid #AAA;
}

.active-link i,
.active-link {
  border-color: $primary;
  color: $primary !important;
}

.active-link-children i,
.active-link-children {
  border-color: $secondary;
  color: $secondary !important;
}
</style>
