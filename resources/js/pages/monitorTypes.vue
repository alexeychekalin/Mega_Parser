<template>
  <v-data-table
    :headers="headers"
    :items="types"
    :search="search"
    class="custom_table_class"
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
            append-icon="mdi-magnify"
            label="Поиск по всем полям"
            single-line
            hide-details
          ></v-text-field>
        </v-toolbar-title>
      </v-toolbar>
    </template>

    <template v-slot:item.monitor="{ item }" >
      <VAvatar
        size="40"
        variant="tonal"
        :color="item.columns.monitor === 1 ? 'success' : 'error'"
        class="me-3"
        @click="set(item.raw, !item.columns.monitor)"
        style="cursor: pointer"
      >
        {{ item.columns.monitor === 1 ? "Да" : "Нет"}}
      </VAvatar>
    </template>

    <template v-slot:no-data>
      <p class="text-subtitle-1 text-truncate">
        Ничего не найдено
      </p>
    </template>
  </v-data-table>
</template>


<script setup>
import { VDataTable } from 'vuetify/labs/VDataTable'
</script>

<script>
import axios from "axios";
import { useToast } from "vue-toastification";
import store from "@/store";
export default {
  data: () => ({
    search: '',
    dialog: false,
    dialogDelete: false,
    headers: [
      { title: 'Название', align: 'center', key: 'typeName'},
      { title: 'Мониторинг', key: 'monitor', sortable: false, align: 'center' },
    ],
    types: [],
    editedIndex: -1,
    editedItem: {
      typeName: '',
    },
    defaultItem: {
      typeName: '',
    },
    rules: {
      required: value => !!value || 'Поле обязательно',
    }
  }),

  components:{
    axios
  },

  computed: {
    formTitle () {
      return this.editedIndex === -1 ? 'Новый тип' : 'Редактирование типа'
    },
  },

  watch: {
    dialog (val) {
      val || this.close()
    },
    dialogDelete (val) {
      val || this.closeDelete()
    },
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
          useToast().error('Ошибка получения списка типов')
          axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка при ПОЛУЧЕНИИ типов. Описание: ' + error, Place: 'monitorTypes.vue' })
        });
    },

    set(item, set){
      this.editedIndex = this.types.indexOf(item)
      this.types[this.editedIndex].monitor = set ? 1 : 0
      console.log(this.types[this.editedIndex].monitor)
      axios.post('api/product/setbytype', {set : set, typeID : item.typeID}).then(res => {
        useToast().success('Данные о мониторинге обновлены')
        axios.post('api/types/setbytype', {set : set, typeID : item.typeID})
          .catch(function (error) {
            useToast().error('Ошибка установки мониторинга ' + this.types[this.editedIndex].typeName)
            axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка при ПОСТАНОВКЕ на мониторинг по типу: '+ this.types[this.editedIndex].typeName + '. Описание: ' + error, Place: 'monitorTypes.vue' })
          });
      })
        .catch(function (error) {
          useToast().error('Ошибка установки мониторинга ' + this.types[this.editedIndex].typeName)
          axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка при ПОСТАНОВКЕ на мониторинг по типу: '+ this.types[this.editedIndex].typeName + '. Описание: ' + error, Place: 'monitorTypes.vue' })
        });
    },
  },
}
</script>

<style>
.custom_table_class thead tr th {
  font-size: 14px;
  text-transform: uppercase
}
</style>
