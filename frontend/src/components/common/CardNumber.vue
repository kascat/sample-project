<template>
  <q-card :class="`bg-${bgColor}`">
    <q-card-section class="row">
      <div v-if="icon" class="col-3 border-r border-white">
        <q-icon :name="icon" size="50px" :class="`text-${iconColor}`"/>
      </div>
      <div :class="`col text-${textColor}`">
        <div class="text-h5">{{ label }}</div>
        <div class="text-h4 text-bold">{{ prefix }}{{ valueToShow }}</div>
      </div>
    </q-card-section>
  </q-card>
</template>

<script setup>
import { computed, ref, watch } from 'vue';

const props = defineProps({
  label: {
    default: '',
  },
  icon: {
    default: null,
  },
  bgColor: {
    default: 'white',
  },
  textColor: {
    default: 'grey-9',
  },
  iconColor: {
    default: 'grey-9',
  },
  value: {
    type: Number,
    default: 0,
  },
  prefix: {
    type: String,
    default: '',
  },
  numberEffect: {
    type: Boolean,
    default: false,
  },
  decimal: {
    type: Boolean,
    default: false,
  },
});

const pointer = ref(0);

const steps = 150;
let partTime = 5;
let increase = true;
let stepsCounter = 0;
let animationPartsValue = 0;

const valueToShow = computed(() => {
  const value = pointer.value.toFixed(2);
  return props.decimal ? value.replace('.', ',') : Math.round(parseFloat(value));
});

const incrementValue = () => {
  const valueToIncrement = pointer.value + (increase ? animationPartsValue : -animationPartsValue);

  pointer.value = increase ? Math.min(props.value, valueToIncrement) : Math.max(props.value, valueToIncrement);

  if (stepsCounter < steps && pointer.value !== props.value) {
    stepsCounter++;

    if (stepsCounter > (steps * 0.90)) {
      partTime += 10;
    } else {
      partTime = 5;
    }

    setTimeout(() => incrementValue(), partTime);
  }

  if (stepsCounter === steps) {
    pointer.value = +props.value;
  }
};

const changeValue = (newValue, oldValue) => {
  if (props.numberEffect) {
    partTime = 5;
    stepsCounter = 0;
    animationPartsValue = Math.abs(newValue - pointer.value) / steps * 1.01;
    increase = newValue > oldValue;

    incrementValue();
  } else {
    pointer.value = +props.value;
  }
};

watch(() => props.value, changeValue);

changeValue(props.value, 0);

</script>
