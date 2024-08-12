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
      <reset-password-form/>
    </div>
  </div>
</template>

<script setup>
import ResetPasswordForm from 'components/auth/ResetPasswordForm';

</script>
<style scoped>
.login-page {
  min-height: 100vh;
  min-width: 100vw;
  padding: 20px 20px 5px 20px;
  background: rgb(0, 102, 153);
  background: radial-gradient(#9bc43e 0%, #0c5ca9 100%);
  display: flex;
}

.image-container {
  background: #3e7b27;
  border-radius: 15px;
  margin-bottom: 15px;
  padding: 10px;
  z-index: 2;
}
</style>
