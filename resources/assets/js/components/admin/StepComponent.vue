<template>
  <div>
    
    <v-container>
    <v-flex xs8>
        <v-select :items="videos" v-model="video" item-text="alias" label="Select Video" @change="onChange" item-value="_id"></v-select>
    </v-flex>
    </v-container>
      <v-form v-model="valid" ref="form">
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
                  <v-text-field name="end_time" v-model="step.point" label="End time" mask="##:##" :rules="end_time_rule"></v-text-field>
                </v-flex>
              </v-layout>
              <v-layout>
          
                <v-select :items="questions" item-text="question" item-value="_id" v-model="step.question_ids" label="Select Question" multiple chips :rules="questions_rule"
      required></v-select>
              </v-layout>
            </v-flex>
            <v-flex xs-2>
              <v-btn v-if="index == 0" small color="primary" flat-right @click.native="add" :disabled="!valid">Add</v-btn>
              <v-btn v-if="index != 0" small color="primary" flat-right @click.native="remove(index)">Close</v-btn>

            </v-flex>
            </v-layout>
          </v-container>

            <v-divider></v-divider>
          </template>
          <v-list-tile>
        <v-list-tile-action v-if="loading_state == true"  flat-right>
          <v-btn color="primary" @click.native="save" :disabled="!valid">Save</v-btn>
        </v-list-tile-action>
      </v-list-tile>
      </v-form>
    
  </div>
</template>
<script>
  import * as actions from '../../store/action-types'
  import withSnackbar from '../mixins/withSnackbar'
  export default {
    mixins: [withSnackbar],
    data: () => ({
      valid: false,
      select_video:'',
      init:'0000',
      video:{'alias': '', '_id': ''},
      steps:[],
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
      end_time_rule: [
          (v) => !!v || 'Time is required',
          (v) => (v && v.length == 4) || 'Time must be 4 characters',
        ],
        questions_rule:[
          (v) => {
            if(v.length == 0)
            {
              return 'it should be selected';
            }
            else{
              return true;
            }
          }
        ]
    }),
    created () {
      this.initialize()
    },
    methods: {
      onChange: function(_id)
      {
        var param = {"_id":_id };
         axios.post('/api/admin/step-management/get_steps', {data:_id}, {headers: {'Content-Type': 'application/json'}})
        .then( function (response) {
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
        this.$refs.form.validate();
        console.log('this.valid', this.valid)
        if(this.valid != false)
        {
          var new_step = {'point':'','sort':this.steps.length + 1, 'question_ids':[]};
          this.steps.push(new_step);
        }
    
      },
      save: function(){
        this.$refs.form.validate();
        if(this.valid == false){
          return;
        }
          axios.post('/api/admin/step-management/create', {data: JSON.stringify(this.step_info)}, {
            headers:{
              'Content-Type':'applicaton/json',
            }
          }).then(function(response){
            this.showMessage(`Successfully Saved`)
          }.bind(this)).catch(function (error){
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
          this.steps = this.step_info.end_times;
          var new_step = {'point':'','sort':1, 'question_ids':[]};
          this.steps.push(new_step);

        }.bind(this))
        .catch(function (error) {
          this.loading = false
        }.bind(this))
      },
    }
  }
</script>