<template>
  <q-layout view="lHh LpR fff" class="layout">

    <q-header elevated class="bg-primary text-white">
      <q-toolbar>
        <q-toolbar-title class="row items-center q-col-gutter-sm">
          <img
            src="~assets/logo.png"
            style="max-height:50px;"
            alt="logo"
          >
        </q-toolbar-title>
      </q-toolbar>
    </q-header>

    <q-page-container>
      <router-view/>
    </q-page-container>

  </q-layout>
</template>

<script setup>
import { t } from 'src/services/utils/i18n';
</script>
