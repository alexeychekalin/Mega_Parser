<template>
    <VCol cols="12">
      <VCard title="Headlines">
        <v-tabs
          v-model="tab"
          color="deep-purple-accent-4"
          align-tabs="center"
        >
          <v-tab :value="1">Landscape</v-tab>
          <v-tab :value="2">City</v-tab>
          <v-tab :value="3">Abstract</v-tab>
        </v-tabs>
        <v-window v-model="tab">
          <v-window-item
            :key="1"
            :value="1"
          >
            <VCardText class="d-flex flex-column gap-y-8">
              {{logs.api}}
            </VCardText>
          </v-window-item>
        </v-window>

      </VCard>
    </VCol>
</template>

<script>
import axios from "axios";
import { useToast } from "vue-toastification";
import store from "@/store";
export default {
  data: () => ({
    tab: null,
    logs: []
  }),

  components:{
    axios
  },


  created () {
    this.getLogs()
  },

  methods: {
    getLogs(){
      this.$axios.get('/api/log').then((res) => this.logs = JSON.stringify(res.data.api))
    }
  },
}
</script>

<style>
.custom_table_class thead tr th {
  font-size: 14px;
  text-transform: uppercase
}
</style>
