<template>
  <q-page padding>
    <div class="row">
      <div class="col">
        <table-basic/>
      </div>
      <div class="col">
        COL2
      </div>
    </div>
  </q-page>
</template>

<script>
import TableBasic from 'src/components/tables/TableBasic.vue'
export default {
  components: { TableBasic },
}
</script>
