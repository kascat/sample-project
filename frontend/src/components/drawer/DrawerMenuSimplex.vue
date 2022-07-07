<template>
  <div style="min-height: 100vh;">
    <q-toolbar class="bg-transparent">
      <img
        src="~assets/logo.jpeg"
        style="max-height:50px;"
        class="absolute-center"
      >
    </q-toolbar>
    <q-card flat>
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
          :active="item.icon === 'o_shopping_bag'"
          active-class="active-link"
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
              active-class="active-link-children"
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
import { loggedUser } from 'boot/user'
import { generateMenu } from 'src/services/utils/menu'

const generalItems = generateMenu()
</script>

<style scoped>
.active-link {
  font-weight: bold;
  border-right: 3px solid #4caf50;
}

.active-link-children {
  font-weight: bold;
  border-right: 3px solid #486eb3;
}

.active-link i,
.active-link .q-item__section {
  color: #4caf50 !important;
}

.active-link-children i,
.active-link-children .q-item__section {
  color: #486eb3 !important;
}
</style>
