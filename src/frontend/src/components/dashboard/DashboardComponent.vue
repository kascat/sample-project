<template>
  <div class="justify-center row q-pa-xl">
    <img
      class="q-mt-md"
      src="~assets/logo.png"
      alt="Logo"
      style="max-height:90px; max-width: 80vw"
    />
    <div class="col-12 text-center q-mt-md text-h5">
      {{ t('app_name') }}
    </div>
  </div>
  <!-- desativado até ajustar dashboard -->
  <div v-if="false">
    <div class="row">
      <div class="col-xs-12 col-sm-6 col-md-4 q-pa-sm" v-for="(item, index) in cards" :key="index">
        <card-number
          v-if="item.canShow"
          :bgColor="item.bgColor"
          :textColor="item.textColor"
          :label="item.label"
          :icon="item.icon"
          :iconColor="item.iconColor"
          :value="item.value"
          number-effect
        />
      </div>
    </div>

    <div class="q-pa-sm">
      <card-expand
        icon="receipt"
        :title="t('data_from', { date: t('months.' + currentMonth)})"
        headerColor="blue-8"
        textColor="blue-1"
      >
        <template v-slot:content>
          <chart-js
            type="line"
            :data="dailyChartData"
            :options="chartOptions"
            :predefined_animation="2"
          />
        </template>
      </card-expand>
    </div>

    <div class="row q-col-gutter-md q-pa-sm">
      <div class="col-xs-12 col-md-6 col-lg-4">
        <widget-info-box/>
      </div>
      <div class="col-xs-12 col-md-6 col-lg-4">
        <widget-info-box/>
      </div>
    </div>

    <div class="row">
      <div class="col-xs-12 col-md-6 col-lg-4 q-pa-sm">
        <card-expand
          icon="receipt"
          :title="t('monthly')"
          headerColor="orange-5"
          textColor="orange-1"
        >
          <template v-slot:content>
            <chart-js
              type="bar"
              :data="monthlyChartData"
              :options="chartOptions"
              :predefined_animation="1"
            />
          </template>
        </card-expand>
      </div>

      <div class="col-xs-12 col-md-6 col-lg-4 q-pa-sm">
        <card-expand
          icon="receipt"
          :title="t('data_from', { date: currentYear})"
          headerColor="green-9"
          textColor="teal-1"
        >
          <template v-slot:content>
            <chart-js
              v-if="doughnutChartDataExample.length"
              type="doughnut"
              :data="doughnutChartData"
            />
            <div v-else class="text-center q-pa-xl">
              <q-icon name="comments_disabled" size="50px" class="text-grey-7 q-ma-xs"/>
            </div>
          </template>
        </card-expand>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { t } from 'src/services/utils/i18n';
import CardNumber from 'components/common/CardNumber';
import CardExpand from 'components/common/CardExpand';
import ChartJs from 'components/common/ChartJs';
import { checkIfLoggedUserHasAbility } from 'boot/user';
import { ABILITIES } from 'src/constants/abilities';
import WidgetInfoBox from 'components/common/WidgetInfoBox.vue';

const canShowUsers = ref(false);

const usersTotalCard = ref({
  canShow: canShowUsers,
  value: 0,
  label: t('users'),
  bgColor: 'purple-6',
  iconColor: 'purple-9',
  textColor: 'purple-2',
  icon: 'people',
});

const sampleTotalCard = ref({
  canShow: true,
  value: 0,
  label: t('example'),
  bgColor: 'orange-6',
  iconColor: 'orange-9',
  textColor: 'orange-2',
  icon: 'redeem',
});

const currentMonth = new Date().getMonth() + 1;
const currentYear = new Date().getFullYear();
const cards = ref([]);

// Data Examples
const dailyDataExample = ref([ 4, 8, 6, 7, 4, 5, 8, 6, 4, 5, 8, 9, 7, 4, 5, 6, 9, 8, 4, 2, 3, 5, 8, 5, 6, 7, 4, 2, 5, 7, 5 ]);
const doughnutChartDataExample = ref([ { label: 'Tipo 1', value: 15 }, {
  label: 'Tipo 2',
  value: 10,
}, { label: 'Tipo 3', value: 18 } ]);
const monthlyDataExample = ref([ 100, 86, 75, 115, 103, 92, 108, 85, 123, 109, 94, 113 ]);

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  scales: {
    y: {
      beginAtZero: true,
    },
  },
  plugins: {
    legend: {
      display: false,
    },
  },
  interaction: {
    mode: 'index',
    intersect: false,
  },
};

onMounted(async () => {
  canShowUsers.value = checkIfLoggedUserHasAbility(ABILITIES.USERS);

  if (canShowUsers.value) {
    // cards.value.push(usersTotalCard.value);
    // userTotals();
  }

  cards.value.push(sampleTotalCard.value);
  // sampleTotals();
});

async function userTotals() {
  try {
    const result = await getUsersTotal();
    usersTotalCard.value.value = result.total;
  } catch {
    //
  }
}

async function sampleTotals() {
  try {
    const result = await getSampleTotal();
    sampleTotalCard.value.value = result.total;
  } catch {
    //
  }
}

const monthlyChartData = computed(() => {
  const labels = [];
  const data = [];

  for (let month = 1; month <= 12; month++) {
    labels.push(t('months.' + month));

    if (month <= currentMonth) {
      data.push(monthlyDataExample.value[month] || 0);
    }
  }

  return {
    labels: labels,
    datasets: [ {
      label: t('value'),
      data: data,
      backgroundColor: '#e36d18',
      borderColor: '#e89e28',
    } ],
  };
});

const dailyChartData = computed(() => {
  const currentDate = new Date();
  const lastDayInMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0).getDate();
  const daysLabel = [];
  const daysTotal = [];

  for (let day = 1; day <= lastDayInMonth; day++) {
    daysLabel.push(day < 10 ? `0${day}` : day);

    if (day <= currentDate.getDate()) {
      daysTotal.push(dailyDataExample.value[day] || 0);
    }
  }

  return {
    labels: daysLabel,
    datasets: [ {
      label: t('value'),
      data: daysTotal,
      backgroundColor: '#31abc9',
      borderColor: '#163969',
      borderDash: [ 5, 5 ],
    } ],
  };
});

const doughnutChartData = computed(() => {
  const labels = [];
  const values = [];
  const colors = [];

  let colorVariation = 123;

  for (const item of doughnutChartDataExample.value) {
    labels.push(t(item.label || 'uninformed'));
    values.push(item.value || 0);
    colors.push(`#${colorVariation}`);
    colorVariation += 124;
  }

  return {
    labels: labels,
    datasets: [ {
      label: t('value'),
      data: values,
      backgroundColor: colors,
    } ],
  };
});
</script>
