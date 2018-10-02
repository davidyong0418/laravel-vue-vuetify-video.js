<template>
  <div>
    
    <v-container>
    <v-flex xs8>
        <v-select :items="videos" v-model="video" item-text="alias" label="Select Video" @change="onChange" item-value="_id"></v-select>
    </v-flex>
    </v-container>

    <v-list>
       <v-card-text v-if="loading_state == false" class=""><h2 class="text-sm-left">Please select video</h2></v-card-text>
          <template v-for="(step, index) in steps">
            <v-container>
            <v-layout wrap>
            <v-flex xs10>
              <v-layout wrap>
                <v-flex xs6>
                  <v-text-field v-if="index == 0" v-model="init" label="start time" disabled mask="##:##"></v-text-field>
                  <v-text-field v-if="index != 0" v-model="steps[index - 1]['point']" label="start time" disabled mask="##:##"></v-text-field>
                </v-flex>
                <v-flex xs6>
                  <v-text-field v-model="step.point" label="End time" mask="##:##"></v-text-field>
                </v-flex>
              </v-layout>
              <v-layout>
          
                <v-select :items="questions" item-text="question" item-value="_id" v-model="step.question_ids" label="Select Question" multiple chips ></v-select>
              </v-layout>
            </v-flex>
            <v-flex xs-2>
              <v-btn v-if="index == 0" small color="primary" flat-right @click.native="add">Add</v-btn>
              <v-btn v-if="index != 0" small color="primary" flat-right @click.native="remove(index)">Close</v-btn>

            </v-flex>
            </v-layout>
          </v-container>

            <v-divider></v-divider>
          </template>
          <v-list-tile>
        <v-list-tile-action v-if="loading_state == true"  flat-right>
          <v-btn color="primary" @click.native="save" >Save</v-btn>
        </v-list-tile-action>
      </v-list-tile>
        </v-list>
    
  </div>
</template>

<script>

  import * as actions from '../../store/action-types'
  import withSnackbar from '../mixins/withSnackbar'
  export default {
    mixins: [withSnackbar],
    data: () => ({
        select_video:'',
        init:'00:00',
        video:{'alias': '', '_id': ''},
        steps:[
        ],
        videos:[],
        questions:[],
        question:[],
       select_questions:[],
      question:'',
      step_info:[],
      loginLoading: false,
      defaultItem: {
        video_id:'',
        end_times:[],
      },
      loading_state: false,
    }),
    created () {
      this.initialize()
    },
    methods: {
      onChange: function(_id)
      {
        console.log(_id);
        
        var param = {"_id":_id };
         axios.post('/api/admin/step-management/get_steps', {data:_id}, {headers: {'Content-Type': 'application/json'}})
        .then( function (response) {
          console.log(response)
          this.loading = false
          this.loading_state = true
          if(response.data.action == 'true')
          {
             this.step_info = response.data.steps;
             this.steps = this.step_info.end_times;
          }
          else
          {
            this.step_info = [];
              this.steps = [];
            this.step_info ={
                video_id:'',
                end_times:[],
              };
           
            this.step_info.video_id = response.data.steps;
            this.step_info.end_times = [];
            this.steps = this.step_info.end_times;
            var new_step = {'point':'','sort': 1,'question_ids':[]};
            this.steps.push(new_step);
 console.log('this.step_info ==============',this.step_info);
            console.log('this.defaultItem ++++++++====',this.defaultItem)
            console.log('this.steps ++++++++====',this.steps)
          }
        }.bind(this))
        .catch(function (error) {
          this.loading = false
        }.bind(this))
      },
      remove: function(index){
        this.steps.splice(index, 1);
      },
      add: function(){
        console.log('step_info++++++++++++++++++++', this.steps);
        var new_step = {'point':'','sort':this.steps.length + 1, 'question_ids':[]};
        this.steps.push(new_step);
    
      },
      save: function(){
          console.log('save step_info+++++++++++',this.step_info);
          axios.post('/api/admin/step-management/create', {data: JSON.stringify(this.step_info)}, {
            headers:{
              'Content-Type':'applicaton/json',
            }
          }).then(function(response){
            this.showMessage(`Successfully Saved`)
          }.bind(this)).catch(function (error){
            console.log(error.response);
            this.showError('Error')
          }.bind(this));
      },
      initialize () {
        this.loading = true
         this.loading_state = false
        axios.get('/api/admin/step-management/get_init_data', {data:'ddd',_token:'kkkkkkkkkkkk'}, {headers: {'Content-Type':'applicaton/json',}})
        .then( function (response) {
          this.loading = false
          this.videos = response.data.videos;
          this.questions = response.data.questions;
          var flag = response.data.action;
          if(flag == 'false')
          {
            this.steps = this.step_info.end_times;
            var new_step = {'point':'','sort':1, 'question_ids':[]};
              this.steps.push(new_step);
              console.log('new_step++++++++++++++++',this.steps)
          }
          else{
            this.step_info = response.data.init_steps;
            this.steps = this.step_info.end_times;
            console.log('end_times++++++++++++++++',this.steps)
          }
          console.log('step_info++++++++++++++++',this.step_info)
        }.bind(this))
        .catch(function (error) {
          this.loading = false
          console.log(error)
        }.bind(this))
      },
    }
  }
</script>