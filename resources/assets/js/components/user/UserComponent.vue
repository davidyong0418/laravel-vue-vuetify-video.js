<template>

  <v-layout>
    <v-flex xs12 sm6 offset-sm3 offset-sm2>
      <v-card sm6>
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
        <v-card-text>
            <span class="grey--text text-md-center">Number 10</span><br>
            <h3 class="headline mb-0 text-md-center">Kangaroo Valley Safari</h3>
            <p class="text-md-center">Whitehaven Beach</p>
            <p class="text-md-center">Whitsunday Island, Whitsunday Islands</p>
        </v-card-text>
       
          <v-list v-if="quiz==false">
            <template v-for="(question_item, index) in current_step_quiz">
                  <h3 class="text-left">{{question_item.question}}</h3>
                    <v-radio-group v-model="current_step_answer[index]" class="ml-3">
                        <v-radio v-for="(answer_item, answer_index) in question_item.answers" :key="answer_index" :label="answer_item.answer" :value="answer_index"></v-radio>
                    </v-radio-group>
          </template>
        </v-list>

        <v-card-actions>
          <v-btn flat-right v-if="accept_btn == true" color="orange" @click="accept">OK</v-btn>
          <v-btn flat-right v-if="next_btn == true" color="orange" @click="next_video_step">Next</v-btn>
          <v-btn flat-right v-if="replay_btn == true" color="orange" @click="replay_video">Replay</v-btn>
        </v-card-actions>
      </v-card>
    </v-flex>
  </v-layout>
</template>

<script>
  import * as actions from '../../store/action-types'
  import withSnackbar from '../mixins/withSnackbar'
  import 'videojs-vimeo'
  import './videojs-offset.js'
  export default {
    mixins: [withSnackbar],
    data() {
      return {
        video_url:'',
        video_data:{},
        step_data:{},
        radioGroup:'',
        start_offset:0,
        end_offset:0,
        current_step:{},
        step_oder:0,
        current_step_quiz:[],
        current_step_answer:[],
        quiz:false,
        accept_btn:false,
        next_btn:false,
        replay_btn:false,
        playerOptions: {
          // videojs options
          sources: [{
          type: "video/vimeo",
          src: 'https://vimeo.com/291838509'
          }],
          techOrder: ["vimeo"],

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
    created () {
      this.get_quiz_info();
    },
    methods: {
      replay_video(){
        this.player.load();
      },
      next_video_step(){
        this.start_offset = ;
        this.end_offset = ;
        this.set_offset();
      },
      accept(){
        console.log(this.current_step_answer)
        var send_data = {};
        send_data['selected_ids'] = this.current_step_answer;
        send_data['current_quiz'] = this.current_step_quiz;
        axios.post('/api/user/user-quiz/accept', 
        {
          data: JSON.stringify(send_data)
        },
        {
            headers:{
              'Content-Type':'applicaton/json',
            }
          }).then(function(response){
            if(this.response.check == true)
            {
              this.next_btn = true;
              this.quiz = false;
            }
            else{
              this.accept_btn = false;
              this.replay_btn = true;
              this.quiz = false;
            }
          }.bind(this)).catch(function (error){
            console.log(error.response);
            this.showError('Error')
          }.bind(this));
      },
      get_quiz_info(){
        axios.get('/api/user/user-quiz', {
            headers:{
              'Content-Type':'applicaton/json',
            }
          }).then(function(response){
            this.video_data = response.data.video_data;
            this.step_data = response.data.step_data;
            this.video_url = this.video_data.vimeo_url;
            console.log('this.video_data++++++++++++===',this.video_data);
            this.set_current_step();
          }.bind(this)).catch(function (error){
            console.log(error.response);
            this.showError('Error')
          }.bind(this));
      },
      set_current_step(){
        this.current_step = this.step_data.end_times[this.step_oder];
        this.questions = this.current_step.question_ids;
        axios.post('/api/user/user-quiz/get_questions_answers',{
          data:JSON.stringify(this.current_step.question_ids)
        },
        {
            headers:{
              'Content-Type':'applicaton/json',
            }
          }).then(function(response){
            this.current_step_quiz = response.data.questions

          }.bind(this)).catch(function (error){
            console.log(error.response);
            this.showError('Error')
          }.bind(this));
      },
      set_offset()
      {
        console.log('+++++++++++',this.player)
        // this.player.currentTime(10);
        this.player.offset({
            start: this.start_offset,
            end: this.end_offset,
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
        this.accept_btn = true;
        this.quiz = true;
      }
    },
  }
</script>