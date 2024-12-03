<template>
  <div class="input-group mb-3 flex-nowrap">
    <input autocomplete="off" :value='token' readonly :type="hideToken ? 'password': 'text'" class="form-control" />
    <div class="input-group-append">
      <div class="input-group-text">
        <span @click="hideToken = !hideToken" :class="{'fas fa-eye': hideToken, 'fas fa-eye-slash': !hideToken}"></span>
      </div>
    </div>
    <button id="copy-btn" @click="copyToken" class="btn btn-success ml-3">copier</button>
  </div>
</template>

<script setup>
    import {ref} from "vue";
    import Toastify from 'toastify-js'

    const props = defineProps({
        token: String
    });

    const hideToken = ref(true)

    const copyToken = () => {
      navigator.clipboard.writeText(props.token);
      Toastify({
        text: "copié",
        className: "success",
        style: {
          background: "black",
        },
      }).showToast();
    }
</script>

<style scoped>
  .fa-eye:hover, .fa-eye-slash:hover {
    cursor: pointer;
  }
</style>
