<template>
  <v-layout row>
    <v-flex  sm10>
      <template>

        <video-player  class="video-player-box"
                 ref="videoPlayer"
                 :options="playerOptions"
                 :playsinline="true"
                 customEventName="customstatechangedeventname"
                  @play="onPlayerPlay($event)"
                 @pause="onPlayerPause($event)"
                 @ended="onPlayerEnded($event)"
                 @ready="playerReadied">
        </video-player>
      </template>

    </v-flex>
    <v-flex  sm10 offset-sm1>
      <v-btn color="primary" flat-right @click="set_event">Add</v-btn>    
      <v-btn color="primary" flat-right @click="reload">load</v-btn>    
      </v-flex>
  </v-layout>
</template>
<script src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
<script>
  import * as actions from '../../store/action-types'
  import withSnackbar from '../mixins/withSnackbar'
  import 'videojs-vimeo'
  import 'videojs-offset'
  import './videojs-offset.js'
  export default {
    mixins: [withSnackbar],
    data() {
      return {
          playerOptions: {
            // videojs options
            sources: [{
            type: "video/vimeo",
            src: "https://vimeo.com/153979733"
           }],
            techOrder: ["vimeo"]

        },
        change_value:20,
      }
    },
    mounted() {
      console.log('this is current player instance object', this.player)
    },
    computed: {
      player() {
        return this.$refs.videoPlayer.player
      }
    },
    methods: {
      set_event()
      {
        console.log('+++++++++++',this.player)
        // this.player.currentTime(10);
        this.player.offset({
            start: this.change_value,
            end: 50,
            restart_beginning: false //Should the video go to the beginning when it ends
          });
      },
      reload(){
        this.change_value = 10;
        // console.log(this.change_value);
        // this.player.trigger('loadstart');

        // this.player.load();
      },
      // listen event
      onPlayerPlay(player) {
        console.log('player play!', player)
      },
      onPlayerPause(player) {
        // console.log('player pause!', player)
      },
      // ...player event
       // or listen state event
      playerStateChanged(playerCurrentState) {
        // console.log('player current update state', playerCurrentState)
      },
      // player is ready
      playerReadied(player) {
        console.log('the player is readied', player)
        // you can use it to do something...
        // player.[methods]
      },
      onPlayerTimeupdate(player)
      {
        console.log('this player time update', player)
      },
      onPlayerEnded(player)
      {

      }
    },
  }
</script>