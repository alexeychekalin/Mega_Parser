<template>
  <Spinner v-if="loading"/>
  <VCard title="Включить в мониторинг">
    <v-data-table
    v-model:page="page"
    :search="search"
    :headers="headers"
    :items="filteredProducts"
    :items-per-page="itemsPerPage"
    row-class="my-row-class"
    hide-default-footer
    class="custom_table_class"
    v-model:sort-by="sortBy"
    hover="true"
  >
    <template v-slot:bottom>
      <VRow>
        <VCol cols="12" md="3"></VCol>
        <VCol cols="12" md="6">
          <div class="text-center pt-2 mt-10">
            <v-pagination
              v-model="page"
              :length="pageCount"
            ></v-pagination>
          </div>
        </VCol>
      </VRow>
    </template>

    <template v-slot:thead>
      <tr class="grey lighten-3">
        <th v-for="header in headers" :key="header.title">
          <div v-if="filters.hasOwnProperty(header.key)">
            <v-col cols="12" >
              <v-autocomplete
                flat
                hide-details
                multiple
                attach
                chips
                dense
                clearable
                :items="columnValueList(header.key)"
                v-model="filters[header.key]"
                variant="underlined"
                prepend-inner-icon="mdi-magnify"
                class = "filter-search"
              >
                <template v-slot:chip="{ item, index }">
                  <v-chip v-if="index < 2">
											 <span>
                        {{ item.value }}
                      </span>
                  </v-chip>
                  <span v-if="index === 2" class="grey--text caption" >
                      (+{{ filters[header.key].length - 2 }} )
                      </span>
                </template>
              </v-autocomplete>
            </v-col>
          </div>
        </th>
      </tr>
    </template>

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

    <template v-slot:item.Monitor="{ item }" >
      <VAvatar
        size="40"
        variant="tonal"
        :color="getColor(item.columns.Monitor)"
        class="me-3"
        @click="set(item.raw, 'Monitor', !item.columns.Monitor, 'мониторинге')"
        style="cursor: pointer"
      >
        {{ item.columns.Monitor === 1 || item.columns.Monitor === true ? "Да" : "Нет"}}
      </VAvatar>
    </template>

    <template v-slot:item.Rostest="{ item }">
      <VAvatar
        size="40"
        variant="tonal"
        :color="getColor(item.columns.Rostest)"
        class="me-3"
        @click="set(item.raw, 'Rostest', !item.columns.Rostest, 'Ростесте')"
        style="cursor: pointer"
      >
        {{ item.columns.Rostest === 1 ? "Да" : "Нет" }}
      </VAvatar>
    </template>

    <template v-slot:item.parseDate="{ item }">
      {{item.columns.parseDate !== null ? moment(item.columns.parseDate).format("DD.MM.YY") : ''}}
    </template>

    <template v-slot:item.actions="{ item }">
          <v-tooltip
            location="top"
          >
            <template v-slot:activator="{ props }">
              <v-btn
                icon
                v-bind="props"
                color="error"
                class="ma-1"
                @click="deleteItem(item.raw)"
              >
                <v-icon color="grey-lighten-1">
                  mdi-delete
                </v-icon>
              </v-btn>
            </template>
            <span>Удалить</span>
          </v-tooltip>
      </template>

    <template v-slot:no-data>
      <p class="text-subtitle-1 text-truncate">
        Ничего не найдено
      </p>
    </template>

  </v-data-table>
    <v-dialog v-model="dialogDelete" max-width="500px">
      <v-card>
        <v-card-title class="text-h5">Удалить?</v-card-title>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="blue-darken-1" variant="text" @click="closeDelete">Отмена</v-btn>
          <v-btn color="blue-darken-1" variant="text" @click="deleteItemConfirm">Удалить</v-btn>
          <v-spacer></v-spacer>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </VCard>
</template>


<script setup>
import { VDataTable } from 'vuetify/labs/VDataTable'
import Spinner from "@/layouts/spinner.vue";
</script>

<script>
import axios from "axios";
import { useToast } from "vue-toastification";
import store from "@/store";
import * as XLSX from 'xlsx/xlsx.mjs';
import moment from 'moment'
export default {
  data: () => ({
    loading: false,
    dialogDelete: false,
    sortBy: [{ key: 'parseDate', order: 'desc' }],
    page:1,
    search:'',
    itemsPerPage: 15,
    types:[],
    filters: {
      ClassName:[],
      //Bonus: [],
      //CardCash: [],
      typeName: [],
      providerName:[]
    },
    headers: [
      // { title: 'Рент', key: 'profit', align: 'center' },
      { title: 'Название', align: 'center', key: 'Model', width: '25%'},
      { title: 'Цвет', align: 'center', key: 'Color'},
      { title: 'Тип', key: 'typeName', sortable: false, align: 'center' },
      { title: 'Поставщик', key: 'providerName', align: 'center' },
      { title: 'Закупка', key: 'PurchasePrice', align: 'center' },
      // { title: 'Продажа', key: 'SellPrice', sortable: false, align: 'center' },
      { title: 'Дата', key: 'parseDate', align: 'center' },
      // { title: 'Дата', key: 'SberParseDate', align: 'center' },
      // { title: 'Бонусы', key: 'Bonus', align: 'center' },
      // { title: 'Карта', key: 'CardCash', align: 'center' },
      { title: 'РСТ', key: 'Rostest', align: 'center' },
      { title: 'Мониторинг', key: 'Monitor', align: 'center' },
      { title: 'Действия', key: 'actions', sortable: false, align: 'center' },
    ],
    products: [],
  }),

  components:{
    axios
  },
  computed: {
    filteredProducts() {
      return this.products.filter((d) => {
        return Object.keys(this.filters).every((f) => {
          return this.filters[f].length < 1 || this.filters[f].includes(d[f]);
        });
      });
    },
    pageCount () {
      return Math.ceil(this.products.length / this.itemsPerPage)
    },
  },

  created () {
    this.getProducts()
    this.getTypes()
  },

  methods: {
    deleteItem (item) {
      this.editedIndex = this.products.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.dialogDelete = true
    },

    deleteItemConfirm () {
      let currentProduct = this.products[this.editedIndex]
      axios.post('api/product/delete', {ProductID : currentProduct.ProductId}).then(res => {
        useToast().success('Товар удален', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
        this.products.splice(this.editedIndex, 1)
        this.closeDelete()
      })
        .catch(function (error) {
          useToast().error('Ошибка удаления товара', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
          axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка при УДАЛЕНИИ товара: '+ currentProduct.Model + '. Описание: ' + error, Place: 'products.vue' })
        });
    },

    closeDelete () {
      this.dialogDelete = false
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
      })
    },

    columnValueList: function (val) {
      return this.products.map((d) => d[val]).filter((value, index, self) => self.indexOf(value) === index);
    },

    getTypes (){
      axios.get('/api/types')
        .then(res => {
          this.types = res.data;
        })
        .catch(function (error) {
          useToast().error('Ошибка получения списка типов', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
          axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка при ПОЛУЧЕНИИ типов. Описание: ' + error, Place: 'NewProduct.vue' })
        });
    },

    getColor (value) {
      return value === 1 || value === true ? 'success' : 'error'
    },

    formatNumber(i){
      return Intl.NumberFormat('ru-RU').format(i.replace(/[\u0000-\u001F\u007F-\u009F\u00A0]/g, "").replace('₽', '').replace(',','').replace(' ',''))
    },

    getProducts (){
      this.loading = true
      axios.get('/api/product/monitor')
        .then(res => {
          this.loading = false
          this.products = res.data.map(item => {
            return {
              ...item,
              SellPrice : item.SellPrice !== null ? this.formatNumber(item.SellPrice) : null,
              PurchasePrice : item.PurchasePrice !== null ? this.formatNumber(item.PurchasePrice) : null,
            }
          });
        })
    },

    set(item, what, set, toast){
      this.editedIndex = this.products.indexOf(item)
      this.products[this.editedIndex][what] = set ? 1 : 0
      axios.post('api/product/set', {what : what, set : set, ProductId : item.ProductId}).then(res => {
        useToast().success('Данные о ' + toast + ' обновлены', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
      })
        .catch(function (error) {
          useToast().error('Ошибка обновления данных о ' + toast, {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
          axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка при ОБНОВЛЕНИИ данных о: '+ toast + '. Описание: ' + error, Place: 'product.vue' })
        });
    },

  },
}
</script>

<style>
.filter-search{
  margin: 0 10px 5px 10px;
}
.custom_table_class thead tr th {
  font-size: 14px;
  text-transform: uppercase
}

</style>
