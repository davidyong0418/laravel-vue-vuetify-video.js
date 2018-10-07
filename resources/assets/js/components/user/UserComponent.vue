<template>

  <v-layout>
    <v-flex xs12 sm6 offset-sm3 offset-sm2>
      <v-card sm6>
        <v-card-text v-if="init_data == true"><h3 class="headline mb-0 text-md-center">Video and Question, Step data aren't exsited</h3></v-card-text>
        <div class="video-player-content">
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
        </div>
        <v-card-text>
            <h3 class="headline mb-0 text-md-center">This is Answer and Question system</h3>
            <p class="text-md-center">{{video_data.description}}</p>
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
          <v-btn flat-right v-if="start_btn == true" color="orange" @click="start_video_step">Start</v-btn>
          <v-btn flat-right v-if="next_btn == true" color="orange" @click="next_video_step">Next</v-btn>
          <v-btn flat-right v-if="replay_btn == true" color="orange" @click="replay_video">Replay</v-btn>
        </v-card-actions>
        <div>
            <!-- <v-img :src="video.thumbnail"></v-img> -->
        <v-list two-line v-if="review_system == true">
          <template v-for="(step_review_data, p_index) in review_data">
            <v-divider v-if="p_index == 0"></v-divider>
            <v-list-tile :key="p_index * 10 + c_index" v-if="p_index == 0" class="purple">
               <v-list-tile-content >
                 <h3>Question</h3>
              </v-list-tile-content>

              <v-list-tile-content >
                  <h3>Correct Answer</h3>
              </v-list-tile-content>
            </v-list-tile>
            <v-divider v-if="p_index == 0"></v-divider>


            <v-list-tile v-for="(quiz_data, c_index) in step_review_data" :key="p_index * 10 + c_index">
               <v-list-tile-content >
                 <p>{{quiz_data.question}}</p>
              </v-list-tile-content>

              <v-list-tile-content >
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
  import './videojs-offset.js'
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
        step_order:0,
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
        pause_state:false,
        start_btn:true,
        review_system:false,
        init_data:false,
        playerOptions: {
          // videojs options
          sources: [{
          type: "video/vimeo",
          src: this.vimeourl,
          }],
          techOrder: ["vimeo"],
        },
        change_value:20,
      }
    },
    watch:{
      // step_order: function(val, oldval)
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
      },
    },
    created () {
      this.get_quiz_info();
    },
    methods: {
      set_offset()
      {
        console.log('+++++++++++',this.player)
      },
      cut_step(){
        this.player.currentTime(this.start_offset);
        this.set_offset()
      },
      start_video_step(){
        this.start_btn = false;
        this.player.play();
      },
      replay_video(){
        this.player.currentTime(this.start_offset);
        this.replay_btn = false;
        this.accept_btn = false;
        this.quiz = false;
        this.player.play();
        var self = this;
         setTimeout(function(){
                self.pause_state = false;
            }, 200);
      },
      next_video_step(){
        this.player.play();
        this.next_btn = false;
         var self = this;
         setTimeout(function(){
                self.pause_state = false;
            }, 200);
      },
      accept(){
        var send_data = {};
        send_data['user_id'] = this.user_id;
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
            if(response.data.check == true)
            {
              
              this.quiz = false;
              this.accept_btn = false;
              if((this.step_order + 1) == this.step_count)
              {
                this.show_review_result();
                
              }
              else{
                this.next_btn = true;
               this.step_order = this.step_order + 1;
               this.set_current_step();
               this.cut_step();
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
            this.showError('Error')
          }.bind(this));
      },
      show_review_result(){
         axios.post('/api/user/user-quiz/get_review_result',
        {
          data: this.user_id,
        },
        {
            headers:{
              'Content-Type':'applicaton/json',
            }
          }).then(function(response){
            this.review_data = response.data.review_data;
            this.player_loading = true;
            this.review_system = true;
            this.start_btn = false;
          }.bind(this)).catch(function (error){
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
            this.step_order = response.data.step_order;
            this.player_loading = true;
            this.set_current_step();
          }.bind(this)).catch(function (error){
            this.init_data = true;
            // this.init_data = false;
            this.start_btn = false;
            this.quiz = false;
            this.review_system = false;
            // this.showError('Error')
          }.bind(this));
      },
      set_current_step(){
        this.step_count = this.step_data.end_times.length;
        if(this.step_count == this.step_order)
        {
          this.show_review_result();
          return;
        }
        this.current_step = this.step_data.end_times[this.step_order];
        this.questions = this.current_step.question_ids;
        var start = this.current_step.s_point;
        var end = this.current_step.point;
        this.start_offset = parseInt(parseInt(start.substring(0,2)) * 60) + parseInt(start.substring(2,4));
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
            this.showError('Error')
          }.bind(this));
      },
      reload(){
        this.change_value = 10;
      },
      // listen event
      onPlayerPlay(player) {
        console.log('player play!', player)
      },
      onPlayerPause(player) {
        console.log('player pause!', player)
      },
      playerStateChanged(playerCurrentState) {
        // console.log('player current update state', playerCurrentState)
      },
      playerReadied(player) {
        console.log('the player is readied', player)
        this.player.currentTime(this.start_offset);
        // this.set_offset()
      },
      onPlayerTimeupdate(player)
      {
        if(player.currentTime() > this.end_offset)
        {
          if(this.pause_state == false)
          {
            this.accept_btn = true;
            this.quiz = true;
            this.player.pause();
            this.pause_state = true;
          }
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