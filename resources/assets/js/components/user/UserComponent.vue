<template>

  <v-layout>
    <v-flex xs12 sm6 offset-sm3 offset-sm2>
      <v-card sm6>
        <video-player  class="video-player-box" v-if="player_loading == true"
                 ref="videoPlayer"
                 :options="playerOptions"
                 :playsinline="true"
                 customEventName="customstatechangedeventname"
                  @play="onPlayerPlay($event)"
                 @pause="onPlayerPause($event)"
                 @ended="onPlayerEnded($event)"
                 @waiting="onPlayerWaiting($event)"
                 @playing="onPlayerPlaying($event)"
                 @loadeddata="onPlayerLoadeddata($event)"
                 @timeupdate="onPlayerTimeupdate($event)"
                 @canplay="onPlayerCanplay($event)"
                 @canplaythrough="onPlayerCanplaythrough($event)"
                  @statechanged="playerStateChanged($event)"
                 @ready="playerReadied">
        </video-player>
        <v-card-text>
            <span class="grey--text text-md-center">Number 10</span><br>
            <h3 class="headline mb-0 text-md-center">Kangaroo Valley Safari</h3>
            <p class="text-md-center">Whitehaven Beach</p>
            <p class="text-md-center">Whitsunday Island, Whitsunday Islands</p>
        </v-card-text>
       
          <v-list v-if="quiz == true">
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
        <div>
            <!-- <v-img :src="video.thumbnail"></v-img> -->
             <v-list two-line>
          <template v-for="(step_review_data, p_index) in review_data">
            <v-list-tile v-for="(quiz_data, c_index) in step_review_data" :key="p_index">
              
               <v-list-tile-content>
                 <p>{{quiz_data.question}}</p>
              </v-list-tile-content>

              <v-list-tile-content>
                  <p>{{quiz_data.correct_answer}}</p>
              </v-list-tile-content>

            </v-list-tile>
          </template>
        </v-list>
          </div>
      </v-card>
    </v-flex>
  </v-layout>
</template>

<script>
  import * as actions from '../../store/action-types'
  import withSnackbar from '../mixins/withSnackbar'
  import 'videojs-vimeo'
  import './videojs-offset.js'
  // console.log('vimeo_url', vimeo_url);
  export default {
    mixins: [withSnackbar],
     props:{
            vimeourl: {
              type: String,
              default: null
            },
            distinct: {
              type: String,
              default: null
            },
        },
    data() {
      return {
        video_url:this.vimeourl,
        video_data:{},
        step_data:{},
        radioGroup:'',
        start_offset:0,
        end_offset:0,
        current_step:{},
        step_oder:0,
        step_count:0,
        current_step_quiz:[],
        current_step_answer:[],
        quiz:false,
        accept_btn:false,
        next_btn:false,
        replay_btn:false,
        player_loading:false,
        user_id:this.distinct,
        wrong_answer: false,
        review_data:{},
        playerOptions: {
          // videojs options
          sources: [{
          type: "video/vimeo",
          src: this.vimeourl,
          vimeo: { "ytControls": 0 }

          }],
          techOrder: ["vimeo"],
          vimeo: { "iv_load_policy": 1 }
        },
        change_value:20,
      }
    },
    watch:{
      // step_oder: function(val, oldval)
      // {
      //   this.player.currentTime(200);
      //   this.set_offset()
      // }
    },
    mounted() {
      console.log('this is current player instance object', this.vimeourl);
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
      show_review_result(){
        this.player_loading = false;
         axios.post('/api/user/user-quiz',
        {
          data: this.user_id,
        },
        {
            headers:{
              'Content-Type':'applicaton/json',
            }
          }).then(function(response){
            this.review_data = response.review_data;
            this.player_loading = true;
            this.set_current_step();
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
      replay_video(){
        this.player.currentTime(200);
        this.set_offset()
        // this.player.load();
      },
      next_video_step(){
        // this.start_offset = ;
        // this.end_offset = ;
        this.set_offset();
      },
      accept(){
        var send_data = {};
        send_data['user_id'] = this.user_id;
        send_data['selected_ids'] = this.current_step_answer;
        send_data['current_quiz'] = this.current_step_quiz;
        console.log(send_data)
        axios.post('/api/user/user-quiz/accept', 
        {
          data: JSON.stringify(send_data)
        },
        {
            headers:{
              'Content-Type':'applicaton/json',
            }
          }).then(function(response){
            console.log(response)
            if(response.check == true)
            {
              
              this.quiz = false;
              this.accept_btn = false;
              if(this.step_oder == this.step_count)
              {
                this.show_review_result();
                console.log('review user info')
                
              }
              else{
                this.next_btn = true;
                console.log('current step_oder+++++++++', this.step_oder)
               this.step_oder = this.step_oder + 1;
               console.log('increated step_oder---------', this.step_oder)
              }
              this.showMessage('you can skip next step')
            }
            else{
              this.accept_btn = false;
              this.replay_btn = true;
              this.quiz = false;
              this.showError("Your answer isn't correct")
            }
          }.bind(this)).catch(function (error){
            console.log(error.response);
            this.showError('Error')
          }.bind(this));
      },
      get_quiz_info(){
        axios.post('/api/user/user-quiz',
        {
          data: this.user_id,
        },
        {
            headers:{
              'Content-Type':'applicaton/json',
            }
          }).then(function(response){
            this.video_data = response.data.video_data;
            this.step_data = response.data.step_data;
            this.step_oder = response.data.step_oder;
            console.log('this is step order ++++++++', this.step_oder);
            console.log('this.step_data++++++++++++===',this.step_data);

            // this.video_url = this.video_data.vimeo_url;
            this.player_loading = true;
            this.set_current_step();
          }.bind(this)).catch(function (error){
            console.log(error.response);
            this.showError('Error')
          }.bind(this));
      },
      set_current_step(){
        this.current_step = this.step_data.end_times[this.step_oder];
        this.questions = this.current_step.question_ids;
        console.log('this.current_step++++++++++++++++++', this.current_step)
        var start = this.current_step.s_point;
        var end = this.current_step.point;
        this.step_count = this.step_data.end_times.length;
        this.start_offset = 200;
        
        // this.start_offset = parseInt(parseInt(start.substring(0,2)) * 60) + parseInt(start.substring(2,4));
        this.end_offset = parseInt(parseInt(end.substring(0,2)) * 60) + parseInt(end.substring(2,4));

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
      reload(){
        this.change_value = 10;
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
        this.player.currentTime(200);
        this.set_offset()
        // you can use it to do something...
        // player.[methods]
      },
      onPlayerTimeupdate(player)
      {
        var timeoffset = this.end_offset - this.start_offset;
        if(player.currentTime() > timeoffset)
        {
          this.accept_btn = true;
          this.quiz = true;
          player.pause();
          //  player.trigger('pause');
        }
      },
      onPlayerEnded(player)
      {
        this.accept_btn = true;
        this.quiz = true;
      }
    },
  }
</script>