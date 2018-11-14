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
                </v-card-text>

                 <v-list v-if="quiz == true">
                    <template v-for="(question_item, index) in current_step_quiz">
                        <h3 class="text-left">{{question_item[0].question}}</h3>
                        <v-radio-group v-model="current_step_answer[index]" class="ml-3">
                                <v-radio v-for="(answer_item, answer_index) in question_item" :key="answer_index" :label="answer_item.answer" :value="answer_item.answer"></v-radio>
                        </v-radio-group>
                    </template>
                </v-list>


                <v-card-actions>

                    <v-btn flat-right v-if="accept_btn == true" color="orange" @click="accept">OK</v-btn>
                    <v-btn flat-right v-if="start_btn == true" color="orange" @click="start_video_step">Start</v-btn>
                    <v-btn flat-right v-if="next_btn == true" color="orange" @click="next_video_step">Next</v-btn>
                    <v-btn flat-right v-if="replay_btn == true" color="orange" @click="replay">Replay</v-btn>
                </v-card-actions>
                <div>
                    <v-list two-line v-if="review_system == true">
                        <template v-for="(step_review_data, p_index) in review_data">
                            <v-divider v-if="p_index == 0"></v-divider>
                            <v-list-tile :key="p_index - 1" v-if="p_index == 0" class="blue">
                                <v-list-tile-content >
                                    <h3>Step</h3>
                                </v-list-tile-content>
                                
                                <v-list-tile-content >
                                    <h3>Question</h3>
                                </v-list-tile-content>

                                <v-list-tile-content >
                                    <h3>Correct Answer</h3>
                                </v-list-tile-content>
                            </v-list-tile>
                            <v-divider v-if="p_index == 0"></v-divider>

                            <v-list-tile :key="p_index">
                                <v-list-tile-content >
                                    <p>step {{step_review_data.step}}</p>
                                </v-list-tile-content>
                                <v-list-tile-content >
                                    <p>{{step_review_data.question}}</p>
                                </v-list-tile-content>

                                <v-list-tile-content >
                                    <p>{{step_review_data.correct_answer}}</p>
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
  import Vimeo from '@vimeo/player'
  var player;
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
        step_data: {},
        start_offset: 0,
        end_offset: 0,
        current_step: {},
        step_count: 0,
        current_step_quiz:[],
        current_step_answer:[],
        quiz:false,
        accept_btn:false,
        next_btn:false,
        replay_btn:false,
        user_id:this.distinct,
        review_data:{},
        start_btn:true,
        review_system:false,
        init_data: false,
        playerOptions: {
          // videojs options
          sources: [{
            type: "video/vimeo",
            src: this.vimeourl,
          }],
          techOrder: ["vimeo"],
        },
          baseUrl:'https://player.vimeo.com/video/',
          // baseUrlParam:'?portrait=0&autoplay=0&background=1',
          baseUrlParam:'',
          passIndex: 0,
      }
    },
     mounted() {
        this.video_url = this.baseUrl +this.get_videoId(this.vimeourl) + this.baseUrlParam;
        console.log(this.video_url);
  },
    computed: {
      player() {
        return this.$refs.videoPlayer.player
      }
    },
    created () {
      this.init();
    },
    methods: {
        get_question_answer(){
            var vm =this;
            axios.post('/api/user/user-quiz/get_questions_answers',{
                    data:this.current_step.question_ids
                },
                {
                    headers:{
                        'Content-Type':'applicaton/json',
                    }
                }).then((response) => {
                this.current_step_quiz = response.data
            });
        },
      get_videoId(url){
          var match = url.substr(url.lastIndexOf('/') + 1);
          return match;
      },
      start_video_step(){
          player.play();
          var vm = this;
          this.start_btn = false;
          player.on('timeupdate', function(data){
              console.log('datasecond', data);
              console.log('this.endoffest', vm.end_offset)
              if(data.seconds > vm.end_offset && data.seconds < (vm.end_offset + 1))
              {
                  player.pause();
                  vm.quiz = true;
                  vm.accept_btn = true;
              }
          });
      },
      replay(){
          console.log(this.start_offset);
        player.setCurrentTime(this.start_offset).then(()=>{
            player.play();
        })
        this.replay_btn = false;
        this.accept_btn = false;
        this.quiz = false;
      },
      next_video_step(){
        this.passIndex = this.passIndex + 1;
        this.start_video_step();
        this.next_btn = false;
      },
      accept(){
        let sendData = [];
        this.current_step_answer.map((element, index) => {
            let _answer = { questionId:this.current_step_quiz[index][0].id, answer:element};
            sendData.push(_answer);
        });
        this.current_step_answer = [];
        axios.post('/api/user/user-quiz/accept',
        {
          data: JSON.stringify(sendData)
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
              if(this.passIndex == this.step_count)
              {
                this.show_review_result();
              }
              else{
                this.next_btn = true;
                this.passIndex = this.passIndex + 1;
                this.set_current_step();
                this.showMessage('you can skip next step')
              }
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
      show_review_result () {
         axios.post('/api/user/user-quiz/get_review_result',
        {
          data: this.user_id,
        },
        {
            headers:{
              'Content-Type':'applicaton/json',
            }
          }).then(function(response){
            this.review_data = response.data.result;
            this.review_system = true;
            this.start_btn = false;
          }.bind(this)).catch(function (error){
            this.showError('Error')
          }.bind(this));
      },
      init () {
        axios.get('/api/user/user-quiz/show',
        {
          params:{data: this.user_id},
        }
        ).then(function(response){
            var iframe = document.querySelector('iframe');
            player = new Vimeo(iframe);
            this.step_data = response.data.historyStep;
            let isPass = response.data.isPass;
            this.step_count = this.step_data.length;
            if(this.step_count){
                if(isPass == true)
                {
                    this.show_review_result();
                }
                else{
                    this.set_current_step('initStatus');
                }
            }
            else{
                this.init_data = true;
            }
            
          }.bind(this)).catch(function (error){
            this.init_data = true;
            this.start_btn = false;
            this.quiz = false;
            this.review_system = false;
          }.bind(this));
      },
      set_current_step(initStatus = null){
        if(this.step_data.length)
        {
          this.current_step = this.step_data[this.passIndex];
          this.questions = this.current_step.question_ids;

          var start = this.current_step.old_point;
          var end = this.current_step.point;
          if ( start != '0' )
          {
              this.start_offset = parseInt(parseInt(start.substring(0,2)) * 60) + parseInt(start.substring(2,4));
          }
          else{
              this.start_offset = 0;
          }
            this.end_offset = parseInt(parseInt(end.substring(0,2)) * 60) + parseInt(end.substring(2,4));
            var vm = this;
            if(initStatus != null)
            {
                player.setCurrentTime(this.start_offset);
            }
            this.get_question_answer();
        }
        else{
          if(!this.is_history.length)
          {
            this.show_review_result();
            return;
          }
        }
      },
    },
  }
</script>