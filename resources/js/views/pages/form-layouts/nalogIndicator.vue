<template>
  <Spinner v-if="loading"/>
  <VCard title="Экономические показатели">
    <v-data-table
      :headers="headers"
      :items="types"
      :search="search"
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

      <template v-slot:item.value="{ item }">
        <v-text-field
          v-model="types[types.indexOf(item.raw)].value"
          variant='plain'
          type="number"
          style='padding-top: 0 !important;'
          min='0'
          max='100'
        ></v-text-field>
      </template>

      <template v-slot:bottom> </template>

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
      { title: 'Название', align: 'center', key: 'name'},
      { title: 'Показатель', align: 'center', key: 'value'},
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
      axios.get('/api/tax')
        .then(res => {
          this.types = res.data;
        })
        .catch(function (error) {
          useToast().error('Ошибка получения списка налогов', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
          axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка при ПОЛУЧЕНИИ налогов. Описание: ' + error, Place: 'nalogIndicator.vue' })
        });
    },

    save () {
      this.loading = true
      axios.post('/api/tax/update',
        {
          tax: this.types,
        }
      )
        .then(res => {
          useToast().success('Экономические показатели обновлены', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
        })
        .catch(function (error) {
          useToast().error('Ошибка обновления показателей', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
          axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка при ИЗМЕНЕНИИ экономических показателей. Описание: ' + error, Place: 'nalogIndicator.vue' })
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
