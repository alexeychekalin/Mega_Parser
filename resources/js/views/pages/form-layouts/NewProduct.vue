<script>
import { useToast } from "vue-toastification";
import axios from "axios";
import store from "@/store";
export default {
  data: () => ({
    Model:'',
    Color: '',
    PurchasePrice:'',
    SellPrice: '',
    Bonus: false,
    CardCash: false,
    Monitor: false,
    Rostest: false,
    Wholesaler: '',
    Retailer: '',
    Type: '',
    types:[],
    provider:[],
    rules: {
      required: value => !!value || 'Поле обязательно',
      //number: value => value.length <= 20 || 'Max 20 characters',
      number: value => {
        const pattern = /^\d*(\.\d{1,2})?$/
        return pattern.test(value) || 'Введите число, формат 12345.67'
      },
    }
  }),
  computed: {

  },

  created () {
    this.getTypes()
    this.getProviders()
  },

  methods: {
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

    getProviders (){
      axios.get('/api/providers')
        .then(res => {
          this.provider = res.data;
        })
        .catch(function (error) {
          useToast().error('Ошибка получения списка поставщиков', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
          axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка при ПОЛУЧЕНИИ поставщиков. Описание: ' + error, Place: 'product.vue' })
        });
    },

    clear(){
      this.Bonus = false
      this.Monitor = false
      this.CardCash = false
      this.Rostest = false
    },

    newProduct(){
      console.log(this.Wholesaler)
      axios.post('/api/product',
        {
          Model: this.Model,
          PurchasePrice: this.PurchasePrice,
          SellPrice: this.SellPrice,
          Bonus: this.Bonus,
          CardCash: this.CardCash,
          Monitor: this.Monitor,
          Type: this.Type,
          ProductId: this.ProductId,
          Color: this.Color,
          Rostest: this.Rostest,
          Wholesaler: this.Wholesaler,
          Retailer: this.Retailer,
        } )
        .then(res => {
          useToast().success('Товар создан', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
          if(this.Color !== "")
          {
            axios.post('/api/colors/check',{Color: this.Color,})
              .then(res => {
                if(!res.data)
                  useToast().success('Цвет добавлен в классификатор', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
              })
              .catch(function (error) {
                useToast().error('Ошибка добавления цвета', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
                axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка при ДОБАВЛЕНИИ цвета: '+ this.Model + '. Описание: ' + error, Place: 'Dashboard/NewProduct.vue' })
              });
          }
          this.$refs.newProduct.reset()
          this.clear()
        })
        .catch(function (error) {
          useToast().error('Ошибка создания товара', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
          axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID, Message: 'Ошибка при СОЗДАНИИ товара: '+ name + '. Описание: ' + error, Place: 'Dashboard/NewProduct.vue' })
        });
    }
  }
}
</script>

<template>
  <VForm
    @submit.prevent="newProduct"
    ref="newProduct"
  >
    <VRow>
      <VCol
        cols="12"
        md="6"
      >
        <v-text-field
          v-model="Model"
          label="Название"
          placeholder="Полное название"
          :rules="[rules.required]"
          prepend-inner-icon="mdi-shopping"
        />
      </VCol>

      <VCol
        cols="12"
        md="6"
      >
        <v-autocomplete
          v-model="Type"
          label="Тип"
          :items="types"
          item-title="typeName"
          item-value="typeID"
          :rules="[rules.required]"
          prepend-inner-icon="mdi-format-list-bulleted-type"
        ></v-autocomplete >
      </VCol>

      <VCol
        cols="12"
        md="6"
      >
        <v-select
          v-model="Wholesaler"
          label="Поставщик"
          :items="provider"
          item-title="providerName"
          item-value="providerID"
          prepend-inner-icon="mdi-basket-plus"
        ></v-select>
      </VCol>
      <VCol
        cols="12"
        md="6"
      >
        <v-text-field
          v-model="Retailer"
          label="Ритейлер"
          placeholder="Ритейлер товара"
          prepend-inner-icon="mdi-cart-arrow-right"
        />
      </VCol>

      <VCol
        cols="12"
        md="6"
      >
        <v-text-field
          v-model="PurchasePrice"
          label="Закупочная цена"
          placeholder="Закупочная цена"
          :rules="[rules.required, rules.number]"
          prepend-inner-icon="mdi-currency-rub"
        />
      </VCol>

      <VCol
        cols="12"
        md="6"
      >
        <v-text-field
          v-model="SellPrice"
          label="Рыночная цена"
          placeholder="Рыночная цена"
          :rules="[rules.required, rules.number]"
          prepend-inner-icon="mdi-currency-rub"
        />
      </VCol>

      <VCol cols="12" sm="3" class='d-flex justify-center'>
        <VCheckbox
          color="success"
          true-icon="mdi-alpha-r-circle-outline"
          false-icon="mdi-alpha-s-circle-outline"
          label="Ростест"
          v-model="Rostest"
        ></VCheckbox>
      </VCol>
      <VCol cols="12" sm="3" class='d-flex justify-center'>
        <VCheckbox
          color="success"
          true-icon="mdi-credit-card-check-outline"
          false-icon="mdi-credit-card-off-outline"
          label="Оплата картой"
          v-model="CardCash"
        ></VCheckbox>
      </VCol>
      <VCol cols="12" sm="3" class='d-flex justify-center'>
        <VCheckbox
          color="success"
          true-icon="mdi-gift-open-outline"
          false-icon="mdi-gift-off-outline"
          label="Бонусы Спасибо"
          v-model="Bonus"
        ></VCheckbox>
      </VCol>
      <VCol cols="12" sm="3" class='d-flex justify-center'>
        <VCheckbox
          color="success"
          true-icon="mdi-shopping-search"
          false-icon="mdi-shopping-search-outline"
          label="Мониторинг"
          v-model="Monitor"
        ></VCheckbox>
      </VCol>

      <VCol
        cols="12"
        class="d-flex gap-6"
        md="3"
      >
        <VBtn
          type="submit"
          variant="text"
        >
          Добавить
        </VBtn>

        <VBtn
          type="reset"
          color="secondary"
          variant="text"
          @click="clear"
        >
          Очистить
        </VBtn>
      </VCol>
    </VRow>
  </VForm>
</template>
