<template>

  <v-layout>
    <v-flex xs12 sm6 offset-sm3 offset-sm2>
      <v-card sm6>
        <v-card-text v-if="init_data == true"><h3 class="headline mb-0 text-md-center">Video and Question, Step data aren't existed</h3></v-card-text>
        <div class="video-player-content">
            <iframe v-bind:src="video_url" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen allow="autoplay; encrypted-media"></iframe>
        </div>
        <v-card-text>
            <h3 class="headline mb-0 text-md-center">This is Answer and Question system</h3>
            <p class="text-md-center">{{video_data.description}}</p>
        </v-card-text>
       
          <v-list v-if="quiz == true">
            <template v-for="(question_item, index) in current_step_quiz">
              
              <template v-if="current_step_quiz[index].question != current_step_quiz[index - 1].question">
                <h3 class="text-left">{{question_item.question}}</h3>
              </template>

              <template v-if="current_step_quiz[index].question != current_step_quiz[index - 1].question">
                <v-radio-group v-model="current_step_answer[index]" class="ml-3">
                    <v-radio v-for="(answer_item, answer_index) in question_item.answers" :key="answer_index" :label="answer_item.answer" :value="answer_index"></v-radio>
                </v-radio-group>
              </template>
              
          </template>
        </v-list>

        <v-card-actions>

          <v-btn flat-right v-if="accept_btn == true" color="orange" @click="accept">OK</v-btn>
          <v-btn flat-right v-if="start_btn == true" color="orange" @click="start_video_step">Start</v-btn>
          <v-btn flat-right v-if="next_btn == true" color="orange" @click="next_video_step">Next</v-btn>
          <v-btn flat-right v-if="replay_btn == true" color="orange" @click="replay_video">Replay</v-btn>
        </v-card-actions>
        <div>
        <v-list two-line v-if="review_system == true">
          <template v-for="(step_review_data, p_index) in review_data">
            <v-divider v-if="p_index == 0"></v-divider>
            <v-list-tile :key="p_index - 1" v-if="p_index == 0" class="purple">
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
          <div id="made-in-ny"></div>
      </v-card>
    </v-flex>
  </v-layout>
</template>
<script>
  import * as actions from '../../store/action-types'
  import withSnackbar from '../mixins/withSnackbar'
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
        video_url:'https://player.vimeo.com/video/' + this.get_videoId(this.vimeourl) +'?portrait=0&autoplay=0&background=1',
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
          baseUrl:'https://player.vimeo.com/video/',
          baseUrlParam:'?portrait=0&autoplay=0&background=1'
      }
    },
     mounted() {
        this.video_url = this.baseUrl +this.get_videoId(this.vimeourl) + this.baseUrlParam;
        console.log(this.video_url);
        let recaptchaScript = document.createElement('script')
        recaptchaScript.setAttribute('src', 'https://player.vimeo.com/api/player.js')
        document.head.appendChild(recaptchaScript)
  },
    computed: {
      player() {
        return this.$refs.videoPlayer.player
      },
    },
    created () {
      this.init();
    },
    methods: {
      get_videoId(url){
          var match = url.substr(url.lastIndexOf('/') + 1);
          return match;
      },
      cut_step(){
        this.player.currentTime(this.start_offset);
      },
      start_video_step(){
         var iframe = document.querySelector('iframe');
      var player = new Vimeo.Player(iframe);
      player.play();

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
      init (){
        console.log('user_id', this.user_id);
        axios.get('/api/user/user-quiz/show',
        {
          params:{data: this.user_id},
        }
        ).then(function(response){
          this.step_data = response.data.historySteps;
          // this.is_history = response.data.is_history;
            
            // this.step_data = response.data.step_data;
            // if(this.video_data == '' || this.step_data == '')
            // {
            //   this.init_data = true;
            //   this.start_btn = false;
            //   this.quiz = false;
            //   this.review_system = false;
            // }
            // else{
            //   this.step_order = response.data.step_order;
            //   this.player_loading = true;
              this.set_current_step();
            // }
          }.bind(this)).catch(function (error){
            this.init_data = true;
            this.start_btn = false;
            this.quiz = false;
            this.review_system = false;
          }.bind(this));
      },
      set_current_step(){
        if(this.step_data.length)
        {
          this.current_step = this.step_data[0];
          this.questions = this.current_step.question_ids;

          var start = this.current_step.old_point;
          var end = this.current_step.point;
          // this.start_offset = parseInt(parseInt(start.substring(0,2)) * 60) + parseInt(start.substring(2,4));

          this.end_offset = parseInt(parseInt(end.substring(0,2)) * 60) + parseInt(end.substring(2,4));
      console.log(this.current_step.question_ids)
          axios.post('/api/user/user-quiz/get_questions_answers',{
            data:this.current_step.question_ids
          },
          {
              headers:{
                'Content-Type':'applicaton/json',
              }
            }).then((response) => {
              this.current_step_quiz = response.data.questionAnswers
            }).catch(function (error){
              this.showError('Error')
            }.bind(this));
        }
        else{
          if(!this.is_history.length)
          {
            this.show_review_result();
            return;
          }
        }

      },
      reload(){
        this.change_value = 10;
      },
      // listen event
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