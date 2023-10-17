<template>
  <VCard title="Просмотр логов работы посистем">
    <template #append>
      <VCheckbox
        color="success"
        true-icon="mdi-refresh-auto"
        false-icon="mdi-refresh-circle"
        label="Автообновление"
        v-model="autoUpdate"
        @change="start"
      ></VCheckbox>
    </template>
    <v-tabs
      v-model="tab"
      @click="tabChanged"
    >
      <v-tab value="one">Парсинг СММ</v-tab>
      <v-tab value="two">Работа TG</v-tab>
    </v-tabs>

    <v-card-text>
      <v-window v-model="tab">
        <v-window-item value="one">
          <textarea rows="100" style="width: 100%" v-model="this.logs.one" disabled> </textarea>
        </v-window-item>

        <v-window-item value="two">
          <textarea rows="100" style="width: 100%" v-model="this.logs.two" disabled> </textarea>
        </v-window-item>
      </v-window>
    </v-card-text>
  </VCard>
</template>

<script>
import axios from "axios";
import { useToast } from "vue-toastification";
import store from "@/store";
export default {
  data: () => ({
    tab: 'one',
    logs: {
      'one' : '',
      'two' : '',
    },
    autoUpdate: false,
    interval:''
  }),

  components:{
    axios
  },
  created () {
    this.getLogs()
  },
  methods: {
    getLogs(){
      this.$axios.post('/api/log/get', {tab: this.tab}).then((res) => {this.logs[this.tab] =  res.data.log.join(' ');} )
    },
    start(){
      if(this.autoUpdate){
        this.interval = setInterval(() => this.getLogs(), 60000);
      }
      else clearInterval(this.interval)
    },
    tabChanged(){
      //this.autoUpdate = false;
      this.getLogs()
    }
  },
  beforeUnmount() {
    clearInterval(this.interval)
  }
}
</script>

<style>
.custom_table_class thead tr th {
  font-size: 14px;
  text-transform: uppercase
}
</style>
