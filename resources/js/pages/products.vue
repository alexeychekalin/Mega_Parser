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
        <v-dialog
          v-model="dialog"
          scrollable
          width="auto"
        >
          <template v-slot:activator="{ props }">
            <v-btn
              class="ma-2"
              variant="text"
              icon="mdi-cart-plus"
              v-bind="props"
            >
            </v-btn>
          </template>
          <v-form @submit.prevent="save">
            <v-card style="max-height: 700px;">
              <v-card-title>
                <span class="text-h5">{{ formTitle }}</span>
              </v-card-title>

              <v-card-text>
                <v-container>
                  <v-row>
                    <v-col
                      cols="12"
                      sm="6"
                      md="6"
                    >
                      <v-text-field
                        v-model="editedItem.Model"
                        label="Название"
                        :rules="[rules.required]"
                      ></v-text-field>
                    </v-col>
                    <v-col
                      cols="12"
                      sm="6"
                      md="6"
                    >
                      <v-autocomplete
                        v-model="editedItem.Type"
                        label="Тип"
                        :items="types"
                        item-title="typeName"
                        item-value="typeID"
                        :rules="[rules.required]"
                      ></v-autocomplete>
                    </v-col>
                  </v-row>
                  <v-row>
                    <v-col
                      cols="12"
                      sm="4"
                      md="4"
                    >
                      <v-select
                        v-model="editedItem.providerName"
                        label="Поставщик"
                        :items="provider"
                        item-title="providerName"
                        item-value="providerID"
                      ></v-select>
                    </v-col>
                    <v-col
                      cols="12"
                      sm="4"
                      md="4"
                    >
                      <v-text-field
                        v-model="editedItem.Retailer"
                        label="Ретейлер"
                      ></v-text-field>
                    </v-col>
                    <v-col
                      cols="12"
                      sm="4"
                      md="4"
                    >
                      <v-text-field
                        v-model="editedItem.FeedID"
                        label="FeedID"
                      ></v-text-field>
                    </v-col>
                  </v-row>
                  <v-row>
                    <v-col
                      cols="12"
                      sm="4"
                      md="4"
                    >
                      <v-text-field
                        v-model="editedItem.PurchasePrice"
                        label="Цена закупки"
                        :rules="[rules.required]"
                      ></v-text-field>
                    </v-col>
                    <v-col
                      cols="12"
                      sm="4"
                      md="4"
                    >
                      <v-text-field
                        v-model="editedItem.SellPrice"
                        label="Цена продажи"
                      ></v-text-field>
                    </v-col>
                    <v-col
                      cols="12"
                      sm="4"
                      md="4"
                    >
                      <v-text-field
                        v-model="editedItem.Color"
                        label="Цвет"
                      ></v-text-field>
                    </v-col>
                    <VCol cols="12" md="3" class='d-flex justify-center'>
                      <VCheckbox
                        color="success"
                        true-icon="mdi-alpha-r-circle-outline"
                        false-icon="mdi-alpha-s-circle-outline"
                        label="Ростест"
                        v-model="editedItem.Rostest"
                      ></VCheckbox>
                    </VCol>
                    <VCol cols="12" md="3" class='d-flex justify-center'>
                      <VCheckbox
                        color="success"
                        true-icon="mdi-credit-card-check-outline"
                        false-icon="mdi-credit-card-off-outline"
                        label="Оплата картой"
                        v-model="editedItem.CardCash"
                      ></VCheckbox>
                    </VCol>
                    <VCol cols="12" md="3" class='d-flex justify-center'>
                      <VCheckbox
                        color="success"
                        true-icon="mdi-gift-open-outline"
                        false-icon="mdi-gift-off-outline"
                        label="Бонусы Спасибо"
                        v-model="editedItem.Bonus"
                      ></VCheckbox>
                    </VCol>
                    <VCol cols="12" md="3" class='d-flex justify-center'>
                      <VCheckbox
                        color="success"
                        true-icon="mdi-shopping-search"
                        false-icon="mdi-shopping-search-outline"
                        label="Мониторинг"
                        v-model="editedItem.Monitor"
                      ></VCheckbox>
                    </VCol>
                  </v-row>
                </v-container>
              </v-card-text>

              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                  color="blue-darken-1"
                  variant="text"
                  @click="close"
                >
                  Закрыть
                </v-btn>
                <v-btn
                  :disabled="false"
                  color="blue-darken-1"
                  variant="text"
                  type="submit"
                >
                  Сохранить
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-form>
        </v-dialog>
      </v-toolbar>

    </template>

<!--
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
-->
<!--
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
-->
    <template v-slot:item.parseDate="{ item }">
      {{item.columns.parseDate !== null ? moment(item.columns.parseDate).format("DD.MM.YY") : ''}}
    </template>

    <template v-slot:item.actions="{ item }">
      <div style="white-space: nowrap">
        <v-tooltip text='Мониторинг'
                   location="top"
        >
          <template v-slot:activator="{ props }">
            <v-btn
              icon
              v-bind="props"
              @click="set(item.raw, 'Monitor', !products[products.indexOf(item.raw)].Monitor, 'мониторинге')"
              size="40px"
              class='mr-1'
              :color="getColor(products[products.indexOf(item.raw)].Monitor)"
            >
              <v-icon color="grey-lighten-1">
                mdi-shopping-search-outline
              </v-icon>
            </v-btn>
          </template>
          Мониторинг
        </v-tooltip>
        <v-tooltip text='Ростест'
                   location="top"
        >
          <template v-slot:activator="{ props }">
            <v-btn
              icon
              v-bind="props"
              @click="set(item.raw, 'Rostest', !products[products.indexOf(item.raw)].Rostest, 'Ростесте')"
              size="40px"
              class='mr-1'
              :color="getColor(products[products.indexOf(item.raw)].Rostest)"
            >
              <v-icon color="grey-lighten-1">
                mdi-alpha-r-circle-outline
              </v-icon>
            </v-btn>
          </template>
          Ростест
        </v-tooltip>
        <v-tooltip
          location="top"
        >
          <template v-slot:activator="{ props }">
            <v-btn
              icon
              v-bind="props"
              @click="editItem(item.raw)"
              color="primary"
              size="40px"
            >
              <v-icon color="grey-lighten-1">
                mdi-pencil
              </v-icon>
            </v-btn>
          </template>
          <span>Редактировать</span>
        </v-tooltip>
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
      </div>
      </template>

    <template v-slot:no-data>
      <p class="text-subtitle-1 text-truncate">
        Ничего не найдено
      </p>
    </template>

    <template v-slot:item.typeName="{ item }">
      <v-select
        class="fit"
        v-model="item.columns.typeName"
        :items="types"
        item-title="typeName"
        item-value="typeID"
        variant="solo"
        @update:model-value = "searchSimilar(item.value.Model, item.columns.typeName, item.value.ProductId)"
      ></v-select>
    </template>
    <template v-slot:item.FeedID="{ item }">
      <v-text-field
        variant="solo"
        v-model="item.columns.FeedID"
        @blur='updateFeedID(item.columns.FeedID, item.value.ProductId, item.raw)'
      ></v-text-field>
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

  <v-dialog
    v-model="typesDialog"
    width="auto"
  >
    <template v-slot:default="{ isActive }">
      <v-card>
        <v-alert
          color="warning"
          :title="'Найдены похожие на '+ similar +  ' товары:' "
          variant="outlined"
          border="start"
          border-color="warning"
        >
          <v-list :lines="newTypes.length" select-strategy="classic" style="max-height: 400px;">
            <v-list-subheader>Выбранный тип: {{newType}}</v-list-subheader>
            <v-list-item v-for="(item, key, index) in newTypes" :value=key>
              <template v-slot:prepend="{ isActive=true }">
                <v-list-item-action start>
                  <v-checkbox :key="item['ProductId']"   :label="item['Model']" :model-value="isActive" v-model="newTypes[key]['selected']" color="success"></v-checkbox>
                </v-list-item-action>
              </template>
            </v-list-item>
          </v-list>
          <v-card-actions class="justify-end">
            <v-btn
              variant="text"
              @click="updateTypes"
            >Обновить</v-btn>
          </v-card-actions>
        </v-alert>

      </v-card>
    </template>
  </v-dialog>

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
    dialog: false,
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
      { title: 'FeedID', key: 'FeedID', align: 'center' },
      // { title: 'Дата', key: 'SberParseDate', align: 'center' },
      // { title: 'Бонусы', key: 'Bonus', align: 'center' },
      // { title: 'Карта', key: 'CardCash', align: 'center' },
      //{ title: 'РСТ', key: 'Rostest', align: 'center' },
      //{ title: 'СММ', key: 'Monitor', align: 'center' },
      { title: '', key: 'actions', sortable: false, align: 'center' },
    ],
    products: [],
    editedIndex: -1,
    editedItem: {
      Model:'',
      PurchasePrice:'',
      SellPrice: '',
      Bonus: false,
      CardCash: false,
      Monitor: false,
      Rostest: false,
      Type: '',
      ProductId: '',
      Color: '',
      Wholesaler: '',
      Retailer: '',
      FeedID: '',
    },
    defaultItem: {
      Model:'',
      PurchasePrice:'',
      SellPrice: '',
      Bonus: false,
      CardCash: false,
      Monitor: false,
      Rostest: false,
      Type: '',
      ProductId: '',
      Color: '',
      Wholesaler: '',
      Retailer: '',
      FeedID:'',
    },
    oldType:'',
    newTypes:[],
    newType:'',
    similar:'',
    typesDialog: false,
    rules: {
      required: value => !!value || 'Поле обязательно',
      //number: value => value.length <= 20 || 'Max 20 characters',
      number: value => {
        const pattern = /^\d*(\.\d{1,2})?$/
        return pattern.test(value) || 'Введите число, формат 12345.67'
      },
    }
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

  watch: {
    dialog (val) {
      val || this.close()
    },
    dialogDelete (val) {
      val || this.closeDelete()
    },
  },
  created () {
    this.getProducts()
    this.getTypes()
  },

  methods: {
    updateFeedID(feedid, id, item){
      if(feedid === null || feedid === ' ' || feedid === '') return
      axios.post('api/product/feedid', {ProductId : id, FeedID: feedid}).then(res => {
        useToast().success('FeedID обновлен', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
        this.products[this.products.indexOf(item)].FeedID = feedid;
      })
        .catch(function (error) {
          useToast().error('Ошибка обновления FeedID', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
          axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка при ИЗМЕНЕНИИ FeedID у товара с ID '+ id + '. Описание: ' + error, Place: 'products.vue' })
        });
    },

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
      return value === 1 || value === true ? 'success' : 'warning'
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

    editItem (item) {
      this.editedIndex = this.products.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.editedItem.Monitor = this.editedItem.Monitor === 1
      this.editedItem.Bonus = this.editedItem.Bonus === 1
      this.editedItem.CardCash = this.editedItem.CardCash === 1
      this.editedItem.Rostest = this.editedItem.Rostest === 1
      this.oldModel = item.Model
      this.dialog = true
      this.oldType = item.Type
    },

    close () {
      this.dialog = false
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
      })
    },

    save: function () {
      this.loading = true
      if (this.editedItem.providerName !== null && this.editedItem.providerName !== "" && !isNaN(this.editedItem.providerName)) {
        let provider = this.provider.find(f => f.providerID === this.editedItem.providerName)
        this.editedItem.Wholesaler = provider.providerID
        this.editedItem.providerName = provider.providerName
      }
      let updatedProduct = this.editedItem;
      axios.post('/api/product/update',
        {
          Model: updatedProduct.Model,
          PurchasePrice: updatedProduct.PurchasePrice,
          SellPrice: updatedProduct.SellPrice,
          Bonus: updatedProduct.Bonus,
          CardCash: updatedProduct.CardCash,
          Monitor: updatedProduct.Monitor,
          Type: updatedProduct.Type,
          ProductId: updatedProduct.ProductId,
          Color: updatedProduct.Color,
          Rostest: updatedProduct.Rostest,
          Wholesaler: updatedProduct.Wholesaler,
          Retailer: updatedProduct.Retailer,
          FeedID: updatedProduct.FeedID,
        }
      )
        .then(res => {
          useToast().success('Товар обновлен', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
          this.close()
          this.editedItem.Bonus = this.editedItem.Bonus === true ? 1 : 0
          this.editedItem.CardCash = this.editedItem.CardCash === true ? 1 : 0
          this.editedItem.Monitor = this.editedItem.Monitor === true ? 1 : 0
          this.editedItem.Rostest = this.editedItem.Rostest === true ? 1 : 0

          Object.assign(this.products[this.editedIndex], this.editedItem)

          this.products[this.editedIndex]['typeName'] = this.types.find(f => f.typeID === updatedProduct.Type).typeName
          if (this.editedItem.SellPrice !== "" && this.editedItem.SellPrice !== null && this.editedItem.PurchasePrice !== "" && this.editedItem.PurchasePrice != null) {
            this.products[this.editedIndex]['profit'] = ((1 - (this.products[this.editedIndex]['PurchasePrice'].replace("\u00A0", '') / this.products[this.editedIndex]['SellPrice'].replace("\u00A0", ''))) * 100).toFixed(2)
          }

/*
          if(updatedProduct.Model !== this.oldModel){
            axios.post('/api/product/addEdits', {new: updatedProduct.Model, old : this.oldModel})
              .then(res => {
                if (!res.data)
                  useToast().success('Изменение модели добавлено в классификатор', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
              })
              .catch(function (error) {
                useToast().error('Ошибка добавления измененной модели в классификатор', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
                axios.post('/api/log', {
                  Time: Date.now(),
                  User: store.state.auth.user.UserID,
                  Message: 'Ошибка при ДОБАВЛЕНИИ измененной модели в классификатор: ' + updatedProduct.Model + '. Описание: ' + error,
                  Place: 'products.vue'
                })
              });
          }
*/
          if (updatedProduct.Color !== null && updatedProduct.Color !== "") {
            axios.post('/api/colors/check', {Color: updatedProduct.Color,})
              .then(res => {
                if (!res.data)
                  useToast().success('Цвет добавлен в классификатор', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
              })
              .catch(function (error) {
                useToast().error('Ошибка добавления цвета', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
                axios.post('/api/log', {
                  Time: Date.now(),
                  User: store.state.auth.user.UserID,
                  Message: 'Ошибка при ДОБАВЛЕНИИ цвета: ' + updatedProduct.Model + '. Описание: ' + error,
                  Place: 'products.vue'
                })
              });
          }

          if(this.oldType !== updatedProduct.Type){
            useToast().info('Запущен поиск похожих типов', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
            this.$axios.get('/api/' +
              'shell/update').then((res) => { res.data.answer === 1 ? useToast().success('Поиск завершен', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16}) : useToast().error('Произошла ошибка. Смотрите логи', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})})

          }

          this.loading = false
        })
        .catch(function (error) {
          useToast().error('Ошибка обновления товара', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
          axios.post('/api/log', {
            Time: Date.now(),
            User: store.state.auth.user.UserID,
            Message: 'Ошибка при ИЗМЕНЕНИИ товара: ' + updatedProduct.Model + '. Описание: ' + error,
            Place: 'products.vue'
          })
        });
    },

    searchSimilar(model, Type, id){
      console.log(Type)
      this.newType = this.types.find(f => f.typeID === Type).typeName;
      this.similar = model
      axios.post('api/product/getSimilarTypeProducts', {Model : model}).then(res => {
        if(res.data.length >= 2){
          this.typesDialog = true
          this.newTypes = res.data.map(item => {
            return {
              ...item,
              selected : true
            }
          })
        }
        else{
          axios.post('api/product/settype', {Type : Type, ProductId: id}).then(res => {
            this.getProducts()
            useToast().success('Тип товара обновлен', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
          })
            .catch(function (error) {
              useToast().error('Ошибка изменения типа', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
              axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка при изменении типа: Тип ' + Type + '. Описание: ' + error, Place: 'notype.vue/searchSimilar' })
            });
        }
      })
        .catch(function (error) {
          useToast().error('Ошибка поиска схожих товаров, при изменении типа', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
          axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка поиска схожих товаров, при изменении типа: Тип ' + Type + '. Описание: ' + error, Place: 'notype.vue/searchSimilar' })
        });
    },

    updateTypes(){
      let toUpdate = this.newTypes.filter((x) => x.selected === true).map(value => value.ProductId);
      axios.post('api/product/updateSimilarType', {Models : toUpdate, Type: this.types.find(f => f.typeName === this.newType).typeID}).then(res => {
        this.newTypes = []
        this.similar = ''
        this.newType = ''
        this.typesDialog = false
        this.getProducts()
        useToast().success('Тип ' + toUpdate.length + ' товаров обновлен', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
      })
        .catch(function (error) {
          useToast().error('Ошибка обновления схожих товаров, при изменении типа', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
          axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка обновления схожих товаров, при изменении типа: Тип ' + this.newType + '. Описание: ' + error, Place: 'notype.vue/updateTypes' })
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
.v-select.fit {
  //width: max-content;
}

</style>
