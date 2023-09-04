<template>
<VRow>
  <VCol cols="12">
    <VCard title="Новый пользователь">
      <VCardText>
        <NewUser />
      </VCardText>
    </VCard>
  </VCol>
</VRow>
</template>


<script setup>
import NewUser from "@/views/pages/form-layouts/NewUser.vue";
</script>
