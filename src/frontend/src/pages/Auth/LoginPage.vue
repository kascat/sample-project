<template>
  <div class="bg-page row items-center justify-center">
    <div class="col-xs-12 col-sm-7 col-md-5 col-lg-4">
      <div class="row items-center justify-center">
        <div class="image-container">
          <img
            src="~assets/logo.png"
            alt="Logo"
            style="max-height:90px; max-width: 80vw"
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

const showForgotPassword = ref(false);
</script>
<style scoped>
.bg-page {
  min-height: 100vh;
  min-width: 100vw;
  padding: 20px 20px 5px 20px;
  background: radial-gradient(#2C5364 0%, #0F2027 100%);
  display: flex;
}

.image-container {
  //background: #141C1FFF;
  border-radius: 15px;
  margin-bottom: 15px;
  padding: 10px;
  z-index: 2;
}
</style>
