<script setup>
import AnalyticsAward from '@/views/dashboard/AnalyticsAward.vue'
import NewProduct from "@/views/pages/form-layouts/NewProduct.vue";
import NewUser from "@/views/pages/form-layouts/NewUser.vue";
import Monitor from "@/views/dashboard/Monitor.vue";
import DayStats from '@/views/dashboard/DayStats.vue'
import LastUpdate from '@/views/dashboard/LastUpdate.vue'
</script>

<template>
  <VRow class="match-height">
    <VCol
      cols="12"
      md="4"
    >
      <AnalyticsAward />
    </VCol>
    <VCol
      cols="12"
      md="8"
    >
      <Monitor />
    </VCol>

    <VCol
      cols="12"
      md="4"
    >
      <LastUpdate />
    </VCol>

    <VCol
      cols="12"
      md="8"
    >
      <DayStats />
    </VCol>

    <VCol cols="12" md="6" >
      <VCard title="Новый товар">
        <VCardText>
          <NewProduct />
        </VCardText>
      </VCard>
    </VCol>
    <VCol cols="12" md="6">
      <VCard title="Новый пользователь">
        <VCardText>
          <NewUser />
        </VCardText>
      </VCard>
    </VCol>

<!--
    <VCol
      cols="12"
      md="4"
    >
      <AnalyticsWeeklyOverview />
    </VCol>

    <VCol
      cols="12"
      md="4"
    >
      <AnalyticsTotalEarning />
    </VCol>

    <VCol
      cols="12"
      md="4"
    >
      <VRow class="match-height">
        <VCol
          cols="12"
          sm="6"
        >
          <AnalyticsTotalProfitLineCharts />
        </VCol>

        <VCol
          cols="12"
          sm="6"
        >
          <CardStatisticsVertical v-bind="totalProfit" />
        </VCol>

        <VCol
          cols="12"
          sm="6"
        >
          <CardStatisticsVertical v-bind="newProject" />
        </VCol>

        <VCol
          cols="12"
          sm="6"
        >
          <AnalyticsBarCharts />
        </VCol>
      </VRow>
    </VCol>

    <VCol
      cols="12"
      md="4"
    >
      <AnalyticsSalesByCountries />
    </VCol>

    <VCol
      cols="12"
      md="8"
    >
      <AnalyticsDepositWithdraw />
    </VCol>

    <VCol cols="12">
      <AnalyticsUserTable />
    </VCol>
    -->
  </VRow>
</template>
