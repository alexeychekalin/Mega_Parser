<template>
  <Spinner v-if="loading"/>
  <VCard title="Комиссии СММ">
    <v-data-table
      :headers="headers"
      :items="types"
      :search="search"
      class="elevation-1"
      style='padding-bottom: 10px'
    >
      <template v-slot:top>
        <v-toolbar
          density="compact"
        >
          <v-toolbar-title>
            <v-text-field
              v-model="search"
              density="compact"
              variant="plain"
              prepend-inner-icon="mdi-magnify"
              label="Поиск по всем полям"
              single-line
              hide-details
            ></v-text-field>
          </v-toolbar-title>
        </v-toolbar>
      </template>

      <template v-slot:item.commission="{ item }">
          <v-text-field
            v-model="types[types.indexOf(item.raw)].commission"
            variant='plain'
            type="number"
            style='padding-top: 0 !important;'
            min='0'
            max='100'
          ></v-text-field>
      </template>

      <template v-slot:no-data>
        <p class="text-subtitle-1 text-truncate">
          Ничего не найдено
        </p>
      </template>

    </v-data-table>
    <VBtn
      block
      class="rounded-t-0"
      @click='save'
    >
      Сохранить
    </VBtn>
  </VCard>
</template>


<script setup>
import { VDataTable } from 'vuetify/labs/VDataTable'
import Spinner from '@/layouts/spinner.vue'
</script>

<script>
import axios from "axios";
import { useToast } from "vue-toastification";
import store from "@/store";
export default {
  data: () => ({
    loading: false,
    search: '',
    headers: [
      { title: 'Название', align: 'center', key: 'typeName'},
      { title: 'Комиссия СММ', align: 'center', key: 'commission'},
    ],
    types: [],
  }),

  components:{
    axios
  },

  created () {
    this.getTypes()
  },

  methods: {
    getTypes (){
      axios.get('/api/types/monitor')
        .then(res => {
          this.types = res.data;
        })
        .catch(function (error) {
          useToast().error('Ошибка получения списка типов', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
          axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка при ПОЛУЧЕНИИ типов. Описание: ' + error, Place: 'smmCommission.vue' })
        });
    },

    save () {
      this.loading = true
      axios.post('/api/types/commission',
        {
          types: this.types,
        }
      )
        .then(res => {
          useToast().success('Комиссии СММ обновлены', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
        })
        .catch(function (error) {
          useToast().error('Ошибка обновления комиссия СММ', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
          axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка при ИЗМЕНЕНИИ комиссии СММ. Описание: ' + error, Place: 'smmCommission.vue' })
        });
      this.loading = false
    },
  },
}
</script>

<style>
.v-field__input {
  padding-top: 0 !important;
  text-align: center;
}
</style>
