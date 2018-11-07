<template>
  <div>
    <v-toolbar flat color="white">
      <v-spacer></v-spacer>
      <v-dialog v-model="dialog" max-width="500px">
        <v-btn slot="activator" color="primary" dark class="mb-2" @click.native="new_question">New Question</v-btn>
        <v-form v-model="valid" ref="form">
        <v-card>
          <v-card-title>
            <span class="headline">{{ formTitle }}</span>
          </v-card-title>
          <v-divider></v-divider>
          <v-card-text>
            <v-container grid-list-md>
                <v-list>
                  <v-text-field v-model="question" label="Question" required :rules="[() => question.length > 0 || 'Required field']"></v-text-field>
                    <v-radio-group v-model="selection">
                        
                        <v-list-tile v-for="(item, key) in editedItem" :key="item.id" @click="selection = item.id" class="mt-2">
                          <v-list-tile-action>
                            <v-radio name="video" v-bind:value="item.id" @click.prevent=""/></v-radio>
                          </v-list-tile-action>
                          
                          <v-list-tile-content>
                            <v-text-field v-model="item.answer" label="Answer" required :rules="[() => item.answer.length > 0 || 'Required field']"></v-text-field>
                          </v-list-tile-content>

                          <v-list-tile-content>
                            <v-btn v-if="key == 0" small color="primary" flat-right @click="add">Add</v-btn>
                            <v-btn v-if="key != 0" small color="primary" flat-right @click="remove(item.id)">Close</v-btn>
                          </v-list-tile-content>
                        
                        </v-list-tile>
                        
                      </v-radio-group>
                    </v-list>
            </v-container>
          </v-card-text>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="orange" dark flat @click.native="close">Cancel<v-icon dark left>remove_circle</v-icon></v-btn>
            <v-btn color="orange"  flat @click.native="save_qustions">Save<v-icon dark right>check_circle</v-icon></v-btn>
          </v-card-actions>
        </v-card>
        </v-form>
      </v-dialog>
      
    </v-toolbar>

    <v-data-table :headers="headers" :items="desserts" hide-actions class="elevation-1">
      <template slot="items" slot-scope="props">
        <td>{{ props.item.question }}</td>
        <td class="text-xs-center">2018.10.16</td>
        <td class="text-xs-center">{{ props.item.count }}</td>
        <td class="justify-center layout px-0">
          <v-icon small class="mr-2" @click="editItem(props.item)">
            edit
          </v-icon>
          <v-icon small @click="deleteItem(props.item)">
            delete
          </v-icon>
        </td>
      </template>
    </v-data-table>

  </div>
</template>

<script>
  import withSnackbar from '../mixins/withSnackbar'
  export default {
    mixins: [withSnackbar],
    data: () => ({
      selection: 1,
      question:'',
      dialog: false,
      count: 1,
      loginLoading: false,
      valid: false,
      headers: [
        { text: 'Questions',align: 'center',sortable: false, value: 'question'},
        { text: 'Correct', value: 'correct', align: 'center', },
        { text: 'Answer Count', value: 'count',align: 'center'},
        // { text: 'Create Date', value: 'created_at',align: 'center' },
        { text: 'Actions', value: 'name', sortable: false, align: 'center' }
      ],
      desserts: [],
      editedIndex: -1,
      editedItem: [],
      initQuestion: {
        'answer':'',
        'id':0
      },

    }),
    computed: {
      formTitle () {
        return this.editedIndex === -1 ? 'New Question' : 'Edit Question'
      }
    },
    created () {
      this.init()
    },
    methods: {
      new_question: function()
      {
        while(this.editedItem.length > 0) {this.editedItem.pop();}
        this.initQuestion.id = parseInt(Math.random()*1000);
        this.selection = this.initQuestion.id;
        let clone = {...this.initQuestion};
        this.editedItem.push(clone);
        this.question = '';
        this.editedIndex = -1;
      },
      remove: function(delete_id){
        this.editedItem.forEach((element, index) => {
          if(element.id == delete_id)
          {
            this.editedItem.splice(index, 1); 
          }
        });
        var self = this;
        setTimeout(function(){
          self.selection = self.editedItem[0].id;
        },100);
      },
      
      add: function(){
        this.initQuestion.id = parseInt(Math.random()*1000);
        let clone = {...this.initQuestion};
        this.editedItem.push(clone);
      },
      save_qustions: function(){
        this.$refs.form.validate();
        if(this.valid == false){
          return;
        }
        let action = 'create';
        
        if(this.editedIndex > -1)
        {
          action = 'update';
        }

        axios.get('/api/admin/question-management/update', {params:{data: JSON.stringify(this.editedItem), question:this.question, selection: this.selection, action:action}}, {
          headers:{
            'Content-Type':'applicaton/json',
          }
        }).then(function(response){
          this.desserts = response.data.questions;
          this.showMessage(`Successfully Saved`);
        }.bind(this)).catch(function (error){
          console.log(error.response);
        }.bind(this));

        this.dialog = false

      },
      init () {
        this.loading = true
        axios.get('/api/admin/question-management/show', {headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
        .then( function (response) {
          this.loading = false
          this.desserts = response.data.questions;
        }.bind(this))
        .catch(function (error) {
          this.loading = false
        }.bind(this))
      },

      editItem (item) {
        this.editedIndex = this.desserts.indexOf(item);
        var questionId = item.id;
        var self = this;
        axios.get('/api/admin/question-management/edit', {params:{questionId : questionId}})
              .then((response) => {
                this.editedItem = response.data.answers;
                this.question = response.data.question;
                this.editedItem.forEach(element => {
                  if(element.answer === element.correct_answer)
                  {
                    this.selection = element.id;
                  }
                });
              })
              .catch((error) => {
                console.log('error', error);
              });
              
        this.dialog = true
      },

      deleteItem (item) {
        const index = this.desserts.indexOf(item);
        let deleteQuestion = item.id;
        if(confirm('Are you sure you want to delete this item?'))
        {
          axios.get('/api/admin/question-management/delete', {params:{deleteQuestion:deleteQuestion}}, {headers: {'Content-Type': 'application/json'}})
          .then( function (response) {
            this.loading = false
            this.desserts.splice(index, 1);
            this.showMessage(`Successfully Deleted`);
          }.bind(this))
          .catch(function (error) {
            this.loading = false
          }.bind(this));
        }
      },
      close () {
        this.dialog = false
      },
    }
  }
</script>