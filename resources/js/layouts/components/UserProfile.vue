<script setup>
import avatar1 from '@images/avatars/avatar-1.png'
const isPasswordVisible = ref(false)
</script>
<script>
import store from "@/store";
import axios from "axios";
import {useToast} from "vue-toastification";

export default {
  data: () => ({
    dialog: false,
    password:''
  }),
  methods: {
    async logout() {
      await this.$axios.post('/logout').then(() => {
        store.dispatch('auth/logout')
        this.$router.replace('login')
      })
    },
    changePassword(){
      axios.post('/api/users/change', {password: this.password, UserID: store.state.auth.user.UserID}).then(res => {
        useToast().success('Пароль обновлен')
        this.dialog = false;
      })
        .catch(function (error) {
          useToast().error('Ошибка смены пароля')
          axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка при изменении пароля. Пользователь:  '+ store.state.auth.user.FIO + '. Описание: ' + error, Place: 'UserProfile.vue' })
        });
    }
  }
}
</script>

<template>
  <v-dialog
    v-model="dialog"
    persistent
    width="300"
  >
    <v-card>
      <v-card-title>
        <span class="text-h5">Новый пароль</span>
      </v-card-title>
      <v-card-text>
        <v-container>
          <v-row>
            <v-col
            >
              <VTextField
                v-model="password"
                label="Пароль"
                :type="isPasswordVisible ? 'text' : 'password'"
                :append-inner-icon="isPasswordVisible ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
                @click:append-inner="isPasswordVisible = !isPasswordVisible"
                required
              />
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn
          color="blue-darken-1"
          variant="text"
          @click="dialog = false"
        >
          Отмена
        </v-btn>
        <v-btn
          color="blue-darken-1"
          variant="text"
          @click="changePassword"
        >
          Сохранить
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
  <VBadge
    dot
    location="bottom right"
    offset-x="3"
    offset-y="3"
    color="success"
    bordered
  >
    <VAvatar
      class="cursor-pointer"
      color="primary"
      variant="tonal"
    >
      <VImg :src="avatar1" />

      <!-- SECTION Menu -->
      <VMenu
        activator="parent"
        width="230"
        location="bottom end"
        offset="14px"
      >
        <VList>
          <!-- 👉 User Avatar & Name -->
          <VListItem>
            <template #prepend>
              <VListItemAction start>
                <VBadge
                  dot
                  location="bottom right"
                  offset-x="3"
                  offset-y="3"
                  color="success"
                >
                  <VAvatar
                    color="primary"
                    variant="tonal"
                  >
                    <VImg :src="avatar1" />
                  </VAvatar>
                </VBadge>
              </VListItemAction>
            </template>

            <VListItemTitle class="font-weight-semibold">
              {{store.state.auth.user.FIO}}
            </VListItemTitle>
          </VListItem>
          <VDivider class="my-2" />

          <!-- 👉 Settings -->
          <VListItem link>
            <template #prepend>
              <VIcon
                class="me-2"
                icon="mdi-cog-outline"
                size="22"
              />
            </template>

            <VListItemTitle @click="dialog = true">Сменить пароль</VListItemTitle>
          </VListItem>

          <!-- Divider -->
          <VDivider class="my-2" />

          <!-- 👉 Logout -->
          <VListItem to="/login">
            <template #prepend>
              <VIcon
                class="me-2"
                icon="mdi-logout"
                size="22"
              />
            </template>

            <VListItemTitle @click="logout">Выйти</VListItemTitle>
          </VListItem>
        </VList>
      </VMenu>
      <!-- !SECTION -->
    </VAvatar>
  </VBadge>
</template>
