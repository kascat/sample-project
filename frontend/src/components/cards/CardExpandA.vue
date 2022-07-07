<template>
  <q-card>
    <q-card-actions :class="headerClass">
      <q-btn
        flat
        :icon="icon"
        :ripple="false"
        :label="title"
      />
      <q-space />
      <q-btn
        round
        flat
        dense
        :icon="expanded ? 'keyboard_arrow_up' : 'keyboard_arrow_down'"
        @click="expanded = !expanded"
      />
    </q-card-actions>
    <q-slide-transition>
      <div v-show="expanded">
        <q-separator />
        <q-card-section>
          <slot name="content"/>
        </q-card-section>
      </div>
    </q-slide-transition>
  </q-card>
</template>

<script>
export default {
  props: {
    icon: {
      type: String,
      default: 'home'
    },
    title: {
      type: String,
      default: ''
    },
    headerColor: {
      type: String,
      default: 'blue'
    },
    textColor: {
      type: String,
      default: 'white'
    }
  },
  data() {
    return {
      expanded: true
    }
  },
  computed: {
    headerClass() {
      return `bg-${this.headerColor} text-${this.textColor}`
    }
  }
}
</script>
