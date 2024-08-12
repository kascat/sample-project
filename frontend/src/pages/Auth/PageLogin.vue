<template>
  <div class="login-page row items-center justify-center">
    <div class="col-xs-12 col-sm-7 col-md-5 col-lg-4">
      <div class="row items-center justify-center">
        <div class="image-container">
          <img
            src="~assets/icon.png"
            alt="Logo image"
            style="max-height:90px;"
          >
        </div>
      </div>
      <login-form
        v-if="!showForgotPassword"
        @show-forgot-password="showForgotPassword = true"
      />
      <forgot-password-form
        v-if="showForgotPassword"
        @hide-forgot-password="showForgotPassword = false"
      />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import LoginForm from 'components/auth/LoginForm';
import ForgotPasswordForm from 'components/auth/ForgotPasswordForm';

let showForgotPassword = ref(false);
</script>
<style scoped>
.login-page {
  min-height: 100vh;
  min-width: 100vw;
  padding: 20px 20px 5px 20px;
  background: radial-gradient(#838383 0%, #000000 100%);
  display: flex;
}

.image-container {
  background: #141C1FFF;
  border-radius: 15px;
  margin-bottom: 15px;
  padding: 10px;
  z-index: 2;
}
</style>
