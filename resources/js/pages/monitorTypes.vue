<template>
  <VCard title="Мониторинг по категориям">
    <v-data-table
    :headers="headers"
    :items="types"
    :search="search"
    class="custom_table_class"
    hover="true"
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
        <v-divider
          class="mx-4"
          inset
          vertical
        ></v-divider>
      </v-toolbar>
    </template>

    <template v-slot:item.monitor="{ item }" >
      <v-tooltip text='Мониторинг'
                 location="top"
      >
        <template v-slot:activator="{ props }">
          <v-btn
            icon
            v-bind="props"
            @click="set(item.raw, !item.columns.monitor)"
            size="40px"
            class='mr-1'
            :color="item.columns.monitor === 1 ? 'success' : 'warning'"
          >
            <v-icon color="grey-lighten-1">
              mdi-shopping-search-outline
            </v-icon>
          </v-btn>
        </template>
        Мониторинг
      </v-tooltip>
    </template>

    <template v-slot:no-data>
      <p class="text-subtitle-1 text-truncate">
        Ничего не найдено
      </p>
    </template>
  </v-data-table>
  </VCard>
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
          useToast().error('Ошибка получения списка типов', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
          axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка при ПОЛУЧЕНИИ типов. Описание: ' + error, Place: 'monitorTypes.vue' })
        });
    },

    set(item, set){
      this.editedIndex = this.types.indexOf(item)
      this.types[this.editedIndex].monitor = set ? 1 : 0
      console.log(this.types[this.editedIndex].monitor)
      axios.post('api/product/setbytype', {set : set, typeID : item.typeID}).then(res => {
        useToast().success('Данные о мониторинге обновлены', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
        axios.post('api/types/setbytype', {set : set, typeID : item.typeID})
          .catch(function (error) {
            useToast().error('Ошибка установки мониторинга ' + this.types[this.editedIndex].typeName, {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
            axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка при ПОСТАНОВКЕ на мониторинг по типу: '+ this.types[this.editedIndex].typeName + '. Описание: ' + error, Place: 'monitorTypes.vue' })
          });
      })
        .catch(function (error) {
          useToast().error('Ошибка установки мониторинга ' + this.types[this.editedIndex].typeName, {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
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
