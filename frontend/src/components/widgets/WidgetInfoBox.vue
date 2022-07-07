<template>
  <q-card>
    <q-card-section class="no-padding">
      <div class="row bg-teal text-white">
        <div class="col bg-green">
          <div class="column justify-center">
            <div class="col">
              <q-icon
                size="lg"
                name="money"
              />
            </div>
          </div>
        </div>
        <div class="col-8">
          <div class="column justify-evenly">
            <div class="col text-subtitle2"> Title of widget </div>
            <div class="col text-bold"> 100 </div>
            <div class="col">
              <q-linear-progress
                size="sm"
                color="white"
                :value="0.5"
              />
            </div>
            <div class="col">
              Increase 10% in 15 days
            </div>
          </div>
        </div>
      </div>
    </q-card-section>
  </q-card>
</template>

<script>
export default {
  name: 'WidgetInfoBox',
}
</script>

<style scoped>
.justify-evenly {
  width: 100%;
  padding: 10px 0;
}
.justify-evenly > div {
  padding: 5px 0;
}
</style>
