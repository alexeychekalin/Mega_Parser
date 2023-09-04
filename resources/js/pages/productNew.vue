<template>
<VRow>
  <VCol cols="12">
    <VCard title="Новый товар">
      <VCardText>
        <NewProduct />
      </VCardText>
    </VCard>
  </VCol>
</VRow>
</template>


<script setup>
import NewProduct from "@/views/pages/form-layouts/NewProduct.vue";
</script>
