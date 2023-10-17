<script setup>
import { useTheme } from 'vuetify'
import upgradeBannerDark from '@images/pro/upgrade-banner-dark.png'
import upgradeBannerLight from '@images/pro/upgrade-banner-light.png'
import VerticalNavLayout from '@layouts/components/VerticalNavLayout.vue'
import VerticalNavLink from '@layouts/components/VerticalNavLink.vue'
import VerticalNavSectionTitle from '@/@layouts/components/VerticalNavSectionTitle.vue'


// Components
import Footer from '@/layouts/components/Footer.vue'
import NavbarThemeSwitcher from '@/layouts/components/NavbarThemeSwitcher.vue'
import UserProfile from '@/layouts/components/UserProfile.vue'

const vuetifyTheme = useTheme()

</script>

<script>
import store from "@/store";
export default {
  data: () => ({
    items: [{
      'Не найдена модель': [{icon: 'mdi-robot-vacuum-alert', link: '/errors'}],
      'Недостаточно полей': [{icon: 'mdi-robot-vacuum-alert', link: '/errors'}],
      'Неизвестный тип': [{icon: 'mdi-call-merge', link: '/notype'}],
      'Не найдено на СММ': [{icon: 'mdi-store-off-outline', link: '/nosmm'}],
  }],
    stats:[],
    sm:0,
    countAll : 0,
    stats2:[]
  }),

  methods: {
    async logout() {
      await this.$axios.post('/logout').then(() => {
        store.dispatch('auth/logout')
        this.$router.replace('login')
      })
    },
    Statistics() {
      this.$axios.get('/api/product/stats').then((res) => {
        this.stats = res.data[0]
        this.countAll = res.data[0].reduce((sum, { count }) => sum + count, 0)
        this.stats2 = res.data[1]
      })
    },
  },

  created () {
    this.Statistics()
  },
}

</script>

<template>
  <VerticalNavLayout>
    <!-- 👉 navbar -->
    <template #navbar="{ toggleVerticalOverlayNavActive }">
      <div class="d-flex h-100 align-center">
        <!-- 👉 Vertical nav toggle in overlay mode -->
        <IconBtn
          class="ms-n3 d-lg-none"
          @click="toggleVerticalOverlayNavActive(true)"
        >
          <VIcon icon="mdi-menu" />
        </IconBtn>

        <VSpacer />

        <v-menu
          v-if="countAll !== 0"
          open-on-hover
        >
          <template v-slot:activator="{ props }">
            <IconBtn class="me-2" v-bind="props">
              <v-badge :content="countAll" color="error">
                <v-icon>mdi-bell-outline</v-icon>
              </v-badge>
            </IconBtn>
          </template>

          <v-list>
            <v-list-item
              v-for="(i, index) in stats"
              :key="index"
              :href="items[0][i.typeName][0].link"
              :prepend-icon="items[0][i.typeName][0].icon"
              :title="i.typeName"
            >
              <template v-slot:append>
                <v-badge
                  color="error"
                  :content="i.count"
                  inline
                ></v-badge>
              </template>
            </v-list-item>
          </v-list>
        </v-menu>

        <NavbarThemeSwitcher class="me-2" />

        <IconBtn class="me-2">
          <VIcon
            icon="mdi-sign-out"
            @click="logout"
          />
        </IconBtn>


        <UserProfile />

      </div>
    </template>

    <template #vertical-nav-content>
      <VerticalNavLink
        :item="{
          title: 'Дашборд',
          icon: 'mdi-home-outline',
          to: '/dashboard',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Мониторинг',
          icon: 'mdi-cart-variant',
          to: '/monitor',
          badge: stats2.monitor,
          colorBadge: 'success'
        }"
      />

      <!-- 👉 Pages -->
      <VerticalNavSectionTitle
        :item="{
          heading: 'Мониторинг',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Товары',
          icon: 'mdi-shopping-search-outline',
          to: '/products',
          badge: stats2.products,
          colorBadge: 'warning'
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Категории',
          icon: 'mdi-store-search-outline',
          to: '/monitorTypes',
          badge: stats2.type,
          colorBadge: 'warning'
        }"
      />
      <!-- 👉 Pages -->
      <VerticalNavSectionTitle
        :item="{
          heading: 'Классификация',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Ошибки',
          icon: 'mdi-robot-vacuum-alert',
          to: '/errors',
          colorBadge: 'error',
          badge: stats.filter(x => x.Type === 100 || x.Type === 101).reduce((ac, obj) =>  {return ac + obj.count}, 0)
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Неопределен тип',
          icon: 'mdi-call-merge',
          to: '/notype',
          colorBadge: 'error',
          badge: stats.filter(x => x.Type === 99).reduce((ac, obj) =>  {return ac + obj.count}, 0)
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Не найден на СММ',
          icon: 'mdi-store-off-outline',
          to: '/nosmm',
          colorBadge: 'error',
          badge: stats.filter(x => x.Type === 102).reduce((ac, obj) =>  {return ac + obj.count}, 0)
        }"
      />
      <!-- 👉 Pages -->
      <VerticalNavSectionTitle
        :item="{
          heading: 'Настройки',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Новый товар',
          icon: 'mdi-cart-arrow-down',
          to: '/productsnew',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Типы',
          icon: 'mdi-store-cog-outline',
          to: '/types',
          badge: stats2.typeAll,
          colorBadge: 'info'
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Цвета',
          icon: 'mdi-palette',
          to: '/colors',
          badge: stats2.colors,
          colorBadge: 'info'
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Поставщики',
          icon: 'mdi-account-group-outline',
          to: '/providers',
          badge: stats2.providers,
          colorBadge: 'info'
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Соответствия',
          icon: 'mdi-link-variant',
          to: '/edits',
          badge: stats2.edits,
          colorBadge: 'info'
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Логи',
          icon: 'mdi-math-log',
          to: '/logs',
        }"
      />
      <!-- 👉 Pages -->
      <VerticalNavSectionTitle
        :item="{
          heading: 'Пользователи',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Список',
          icon: 'mdi-account-details-outline',
          to: '/users',
          badge: stats2.users,
          colorBadge: 'info'
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Новый',
          icon: 'mdi-account-plus-outline',
          to: '/usersnew',
        }"
      />
      <!-- 👉 User Interface
      <VerticalNavSectionTitle
        :item="{
          heading: 'User Interface',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Typography',
          icon: 'mdi-alpha-t-box-outline',
          to: '/typography',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Icons',
          icon: 'mdi-eye-outline',
          to: '/icons',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Cards',
          icon: 'mdi-credit-card-outline',
          to: '/cards',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Tables',
          icon: 'mdi-table',
          to: '/tables',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Form Layouts',
          icon: 'mdi-form-select',
          to: '/form-layouts',
        }"
      />
       -->
    </template>

    <!-- 👉 Pages -->
    <slot />

    <!-- 👉 Footer -->
    <template #footer>
      <Footer />
    </template>
  </VerticalNavLayout>
</template>

<style lang="scss" scoped>
.meta-key {
  border: thin solid rgba(var(--v-border-color), var(--v-border-opacity));
  border-radius: 6px;
  block-size: 1.5625rem;
  line-height: 1.3125rem;
  padding-block: 0.125rem;
  padding-inline: 0.25rem;
}
</style>
