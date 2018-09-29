<template>
  <v-layout row>
    <v-flex  sm10>
      <template>
       
        
      </template>

    </v-flex>

    <v-flex  sm10 offset-sm1>
       
    </v-flex>
  </v-layout>
</template>

<style scoped>
    .facebook {
        width: 20px;
    }
</style>

<script>
  import * as actions from '../../store/action-types'
  import withSnackbar from '../mixins/withSnackbar'
  import videojs from 'video.js'
  // something...
  //  window.videojs = require('video.js');

  export default {
    mixins: [withSnackbar],
    data () {
      return {
        errors: [],
        internalAction: this.action,
        loginLoading: false,
        selected:'',
        videos:[],


        player:'',
         url: 'https://someurl.com',
                volume: 1
      }
    },
     
  }
</script>
