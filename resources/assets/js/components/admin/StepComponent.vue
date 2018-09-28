<template>
  <div>
    <v-toolbar flat color="white">
      <v-spacer></v-spacer>
      
      <v-dialog v-model="dialog" max-width="500px">
        <v-btn slot="activator" color="primary" dark class="mb-2" @click.native="new_question">New Question</v-btn>
        <v-form>
        <v-card>
          <v-card-title>
            <span class="headline">{{ formTitle }}</span>
          </v-card-title>

             
          <v-card-text>
            <v-container grid-list-md>
                
                <v-list>
                  <v-list-tile>
                  <v-text-field v-model="editedItem.question" label="Question"></v-text-field>
                </v-list-tile>
                  
                        <v-list-tile v-for="video in editedItem.count" :key="video" @click="editedItem.selected = video">
                          <v-list-tile-action>
                              
                          </v-list-tile-action>
                          <v-list-tile-content>
                            <v-text-field v-model="editedItem.answers[video-1]['answer']" label="Answer"></v-text-field>
                          </v-list-tile-content>

                          <v-list-tile-content>
                            <v-btn v-if="video == 1" small color="primary" flat-right @click="add">Add</v-btn>
                            <v-btn v-if="video != 1" small color="primary" flat-right @click.native="remove(video-1)">Close</v-btn>
                          </v-list-tile-content>

                        </v-list-tile>
              
                    </v-list>

            </v-container>
          </v-card-text>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="blue darken-1" flat @click.native="close">Cancel</v-btn>
            <v-btn color="blue darken-1" flat @click.native="save_qustions">Save</v-btn>
          </v-card-actions>
        </v-card>
        </v-form>
      </v-dialog>
      
    </v-toolbar>

    <v-flex xs8>
        <v-select :items="states" v-model="e1" label="Select or add Colors" ></v-select>
    </v-flex>


    <v-list>
          <template v-for="(step, index) in test">
            <v-container>
            <v-layout wrap>
            <v-flex xs10>
              <v-layout wrap>
                <v-flex xs6>
                  <v-text-field v-if="index == 0" v-model="init" label="start time"></v-text-field>
                  <v-text-field v-if="index != 0" v-model="test[index - 1]['point']" label="start time"></v-text-field>
                </v-flex>
                <v-flex xs6>
                  <v-text-field v-model="step.point" label="End time"></v-text-field>
                </v-flex>
              </v-layout>
              <v-layout>
                
                <v-select :items="states" v-model="e2" label="Select or add Colors"  multiple></v-select>
              </v-layout>
            </v-flex>
            <v-flex xs-2>
              <v-btn v-if="index == 0" small color="primary" flat-right @click="add">Add</v-btn>
              <v-btn v-if="index != 0" small color="primary" flat-right @click.native="remove(index)">Close</v-btn>

            </v-flex>
            </v-layout>
          </v-container>

            <v-divider></v-divider>
          </template>
        </v-list>
    
  </div>
</template>

<style scoped>
    .facebook {
        width: 20px;
    }
</style>

<script>

  import * as actions from '../../store/action-types'
  import withSnackbar from '../mixins/withSnackbar'
  export default {
    data: () => ({
        select_video:'',
        init:'00:00',
        count: 4,
        e1: 'Florida',
        e2: [],
        e3: null,
        e4: null,
        items: [
          { text: 'State 1' },
          { text: 'State 2' },
          { text: 'State 3' },
          { text: 'State 4' },
          { text: 'State 5' },
          { text: 'State 6' },
          { text: 'State 7' }
        ],
        states: [
          'Alabama', 'Alaska', 'American Samoa', 'Arizona',
          'Arkansas', 'California', 'Colorado', 'Connecticut',
          'Delaware', 'District of Columbia', 'Federated States of Micronesia',
          'Florida', 'Georgia', 'Guam', 'Hawaii', 'Idaho',
          'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky',
          'Louisiana', 'Maine', 'Marshall Islands', 'Maryland',
          'Massachusetts', 'Michigan', 'Minnesota', 'Mississippi',
          'Missouri', 'Montana', 'Nebraska', 'Nevada',
          'New Hampshire', 'New Jersey', 'New Mexico', 'New York',
          'North Carolina', 'North Dakota', 'Northern Mariana Islands', 'Ohio',
          'Oklahoma', 'Oregon', 'Palau', 'Pennsylvania', 'Puerto Rico',
          'Rhode Island', 'South Carolina', 'South Dakota', 'Tennessee',
          'Texas', 'Utah', 'Vermont', 'Virgin Island', 'Virginia',
          'Washington', 'West Virginia', 'Wisconsin', 'Wyoming'
        ],



      selected: 1,
      question:'',
      dialog: false,
      count: 1,
      answer:[],
      loginLoading: false,
      test: [
        { 'point': 'Answer Count'},
         { 'point': 'Answer1 Count'},
          { 'point': 'Answer2 Count'},
           { 'point': 'Answer3 Count'},
      ],
      // desserts: [],
      editedIndex: -1,
      editedItem: {
        question: '',
        count: 0,
        answers: [],
        selected: 0,
        _id: '',
      },
      defaultItem: {
        question: '',
        count: 0,
        selected: 0,
        answers: [],
        _id: '',
      }
    }),

    computed: {
      formTitle () {
        return this.editedIndex === -1 ? 'New Question' : 'Edit Question'
      }
    },

    watch: {
      dialog (val) {
        val || this.close()
      }
    },
    created () {
      this.initialize()
    },
    methods: {
      new_question: function()
      {
         this.editedItem.question = '';
         this.editedItem.count = 1;
         this.editedItem.answers = [];
         this.editedItem.selected = 1;
         this.editedItem._id = '';
         var answers = {'answer': '', 'valid' : 0};
         this.editedItem.answers.push(answers);
      },
      remove: function(index){
        this.test.splice(index, 1);
      },
      add: function(){
        var new_step = {'point':'',sort:this.test.length + 1};
        this.test.push(new_step);

    
      },
      save_steps: function(){
        // var form = document.querySelector('question-management');
        if(this.editedIndex > -1)
        {
            axios.post('/api/admin/question-management/update',{data: JSON.stringify(this.editedItem)}, {
            headers:{
              'Content-Type':'applicaton/json',
            }
          }).then(function(response){
            console.log(response.data)
            Object.assign(this.desserts[this.editedIndex], response.data);
          }.bind(this)).catch(function (error){
            console.log(error.response);
          }.bind(this));
        }
        else{
          console.log(this.editedItem);
          axios.post('/api/admin/question-management/create', {data: JSON.stringify(this.editedItem)}, {
            headers:{
              'Content-Type':'applicaton/json',
            }
          }).then(function(response){
            this.desserts = response.data.questions;
          }.bind(this)).catch(function (error){
            console.log(error.response);
          }.bind(this));
        }
        this.dialog = false
      },
      initialize () {
        var params = new URLSearchParams();
        console.log(params);
        this.loading = true
        axios.get('/api/admin/question-management/', {params, _token:'kkkkkkkkkkkk'}, {headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
        .then( function (response) {
          this.loading = false
          this.desserts = response.data.questions
        }.bind(this))
        .catch(function (error) {
          this.loading = false
        }.bind(this))
      },

      editItem (item) {
        this.editedIndex = this.desserts.indexOf(item);
        this.defaultItem = Object.assign({}, item);
        console.log(item);
        this.editedItem.selected = item['selected'];
        this.editedItem.question = item['question'];
        this.editedItem.count = item['count'];
        this.editedItem.correct_answer = item['correct_answer'];
        this.editedItem._id = item['_id'];
        this.editedItem.answers = [];
        for (var i=0;i<item['answers'].length;i++)
        {
             this.editedItem.answers.push({'answer':item['answers'][i]['answer'], 'valid':item['answers'][i]['valid']})
        }
        this.dialog = true
      },

      deleteItem (item) {
        const index = this.desserts.indexOf(item);
        let delete_item = Object.assign({}, item);
        if(confirm('Are you sure you want to delete this item?'))
        {
          axios.post('/api/admin/question-management/delete', {data:delete_item['_id']}, {headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
          .then( function (response) {
            this.loading = false
            this.desserts.splice(index, 1)
            // this.desserts = response.data.questions
          }.bind(this))
          .catch(function (error) {
            this.loading = false
          }.bind(this));

        }
        
      },

      close () {
        this.dialog = false
        this.editedIndex = -1;
      },

      save () {
        // if (this.editedIndex > -1) {
        //   Object.assign(this.desserts[this.editedIndex], this.editedItem)
        // } else {
        //   this.desserts.push(this.editedItem)
        // }
        // this.close()
      }
    }
  }
</script>