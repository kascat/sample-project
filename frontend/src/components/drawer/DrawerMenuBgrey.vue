<template>
  <div class="bg-blue-grey-9" style="min-height: 100vh;">
    <q-card class="bg-transparent">
      <q-card-section>
        <q-item
          clickable
          class="bg-blue-grey-10 q-pt-md q-pb-md"
          style="border-radius: 1rem;"
        >
          <q-item-section side>
            <q-avatar rounded>
              <q-img
                src="https://randomuser.me/api/portraits/women/85.jpg"
                style="border-radius: 50%;"
              />
            </q-avatar>
          </q-item-section>
          <q-item-section>
            <div class="text-subtitle2 text-blue-grey-2">
              Erin Daniels
            </div>
            <div class="text-blue-grey-4">
              erin.daniels@example.com
            </div>
          </q-item-section>
        </q-item>
      </q-card-section>
    </q-card>
    <q-separator color="blue-grey-3"/>
    <q-list dark class="text-bold">
      <q-item>
        <q-item-section>
          <q-item-label class="text-uppercase text-bold text-blue-grey-3">
            General
          </q-item-label>
        </q-item-section>
      </q-item>
      <q-item
        dark
        clickable
        v-for="i in 4"
        :key="i"
        class="text-grey-3 q-ml-md q-mr-md text-blue-grey-2"
        style="border-radius: 1rem;"
      >
        <q-item-section side>
          <q-icon name="home" color="white"/>
        </q-item-section>
        <q-item-section>
          Menu Label
        </q-item-section>
      </q-item>
      <q-item>
        <q-item-section>
          <q-item-label class="text-uppercase text-bold text-blue-grey-3">
            Management
          </q-item-label>
        </q-item-section>
      </q-item>
      <q-expansion-item
        expand-separator
        header-class="text-blue-grey-2 q-ml-md q-mr-md"
        header-style="border-radius: 1rem;"
        expand-icon="chevron_right"
        expanded-icon="keyboard_arrow_down"
        v-for="i in 4"
        :key="i"
      >
        <template v-slot:header>
          <q-item-section side>
            <q-icon name="bluetooth" color="white"/>
          </q-item-section>
          <q-item-section>
            Bluetooth technology
          </q-item-section>
        </template>
        <q-list class="q-ml-md q-mr-md">
          <q-item
            dark
            clickable
            v-for="i in 4"
            :key="i"
            class="text-blue-grey-2"
            style="border-radius: 1rem;"
          >
            <q-item-section>
              Menu Label
            </q-item-section>
          </q-item>
        </q-list>
      </q-expansion-item>
    </q-list>
  </div>
</template>

<script>
export default {
  name: 'DrawerMenuBgrey'
}
</script>

<style scoped>
.q-expansion-item__content .q-item {
  padding-left: 3.5rem;
}
</style>
