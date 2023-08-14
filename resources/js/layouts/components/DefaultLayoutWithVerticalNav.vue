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

const upgradeBanner = computed(() => {
  return vuetifyTheme.global.name.value === 'light' ? upgradeBannerLight : upgradeBannerDark
})
</script>

<script>
import { mapActions } from 'vuex'
export default {

  methods: {
    ...mapActions({
      signOut: 'auth/logout',
    }),
    async logout() {
      await this.$axios.post('/logout').then(() => {
        this.signOut()
        this.$router.replace('login')
      })
    },
  }
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

        <IconBtn class="me-2">
          <VIcon icon="mdi-bell-outline" />
        </IconBtn>

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
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Новый',
          icon: 'mdi-account-plus-outline',
          to: '/users',
        }"
      />

      <!-- 👉 Pages -->
      <VerticalNavSectionTitle
        :item="{
          heading: 'Товары',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Список',
          icon: 'mdi-cart-variant',
          to: '/products',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Типы',
          icon: 'mdi-store-cog-outline',
          to: '/products',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Классификация',
          icon: 'mdi-store-alert-outline',
          to: '/products',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Новый',
          icon: 'mdi-cart-arrow-down',
          to: '/products',
        }"
      />

      <!-- 👉 Pages -->
      <VerticalNavSectionTitle
        :item="{
          heading: 'Pages',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Login',
          icon: 'mdi-login',
          to: '/login',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Register',
          icon: 'mdi-account-plus-outline',
          to: '/register',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Error',
          icon: 'mdi-information-outline',
          to: '/no-existence',
        }"
      />

      <!-- 👉 User Interface -->
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
