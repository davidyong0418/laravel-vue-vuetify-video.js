<template>
  <div>
    <v-toolbar flat color="white">
      <v-toolbar-title>My CRUD</v-toolbar-title>
      <v-divider
        class="mx-2"
        inset
        vertical
      ></v-divider>
      <v-spacer></v-spacer>
      <v-dialog v-model="dialog" max-width="500px"  id="question-management">
        <v-btn slot="activator" color="primary" dark class="mb-2">New Item</v-btn>
        <v-form>
        <v-card>
          <v-card-title>
            <span class="headline">{{ formTitle }}</span>
          </v-card-title>
  
          <v-card-text>
            <v-container grid-list-md>
                <!-- <v-layout wrap>
                <v-flex xs12 sm12>
                  <v-text-field v-model="editedItem.name" label="Questidddonddddd"></v-text-field>
                </v-flex>
                </v-layout> -->


                <v-list>
                  <v-list-tile>
                  <v-text-field v-model="question" label="Question"></v-text-field>
                </v-list-tile>
                    <v-radio-group v-model="selected">
                        <v-list-tile v-for="video in count" :key="video" @click="selected = video">
                        
                          <v-list-tile-action>
                            <v-radio 
                            name="video"
                            v-bind:value="video"
                            @click.prevent=""
                            /></v-radio>
                          </v-list-tile-action>
                          <v-list-tile-content>
                            <v-text-field v-model="answer[video - 1]" name="answer[]"></v-text-field>
                          </v-list-tile-content>

                          <v-list-tile-content>
                            <v-btn v-if="video == 1" small color="primary" flat-right @click.native="add">Add</v-btn>
                            <v-btn v-if="video != 1" small color="primary" flat-right @click.native="close">Close</v-btn>
                          </v-list-tile-content>

                        </v-list-tile>
                      </v-radio-group>
                    </v-list>

            </v-container>
          </v-card-text>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="blue darken-1" flat @click.native="cancel">Cancel</v-btn>
            <v-btn color="blue darken-1" flat @click.native="save_qustions">Save</v-btn>
          </v-card-actions>
        </v-card>
        </v-form>
      </v-dialog>
    </v-toolbar>
    <v-data-table
      :headers="headers"
      :items="desserts"
      hide-actions
      class="elevation-1"
    >
      <template slot="items" slot-scope="props">
        <td>{{ props.item.name }}</td>
        <td class="text-xs-right">{{ props.item.calories }}</td>
        <td class="text-xs-right">{{ props.item.fat }}</td>
        <td class="text-xs-right">{{ props.item.carbs }}</td>
        <td class="text-xs-right">{{ props.item.protein }}</td>
        <td class="justify-center layout px-0">
          <v-icon
            small
            class="mr-2"
            @click="editItem(props.item)"
          >
            edit
          </v-icon>
          <v-icon
            small
            @click="deleteItem(props.item)"
          >
            delete
          </v-icon>
        </td>
      </template>
      <template slot="no-data">
        <v-btn color="primary" @click="initialize">Reset</v-btn>
      </template>
    </v-data-table>
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
      selected: 1,
      question:'',
      dialog: false,
      count: 1,
      answer:[],
      headers: [
        {
          text: 'Questions',
          align: 'right',
          sortable: false,
          value: 'name'
        },
        { text: 'Calories', value: 'calories',  },
        { text: 'Fat (g)', value: 'fat' },
        { text: 'Carbs (g)', value: 'carbs' },
        { text: 'Protein (g)', value: 'protein' },
        { text: 'Actions', value: 'name', sortable: false }
      ],
      desserts: [],
      editedIndex: -1,
      editedItem: {
        name: '',
        calories: 0,
        fat: 0,
        carbs: 0,
        protein: 0
      },
      defaultItem: {
        name: '',
        calories: 0,
        fat: 0,
        carbs: 0,
        protein: 0
      }
    }),

    computed: {
      formTitle () {
        return this.editedIndex === -1 ? 'New Item' : 'Edit Item'
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
      close: function(){
        this.count = this.count - 1;
      },
      add: function(){
        this.count = this.count + 1;
        console.log(this.count);
      },
      save_qustions: function(){
        // var form = document.querySelector('question-management');
        var formData  = new FormData();
        console.log(this.selected);
        formData.append('file', this.answer);
        formData.append('selected', this.selected - 1);
        formData.append('question', this.question);
        axios.post('/api/admin/question-management/create', formData, {
          headers:{
            // 'Content-Type':'applicaton/json',
            'Content-Type': 'multipart/form-data'
          }
        }).then(function(response){
          console.log(response);
          this.quesitons = response.data.questions;
        }.bind()).catch(function (error){
          console.log(error.response);
        }.bind(this));
      },
      initialize () {
        this.desserts = [
          {
            name: 'Frozen Yogurt',
            calories: 159,
            fat: 6.0,
            carbs: 24,
            protein: 4.0
          },
          {
            name: 'Ice cream sandwich',
            calories: 237,
            fat: 9.0,
            carbs: 37,
            protein: 4.3
          },
          {
            name: 'Eclair',
            calories: 262,
            fat: 16.0,
            carbs: 23,
            protein: 6.0
          },
          {
            name: 'Cupcake',
            calories: 305,
            fat: 3.7,
            carbs: 67,
            protein: 4.3
          },
          {
            name: 'Gingerbread',
            calories: 356,
            fat: 16.0,
            carbs: 49,
            protein: 3.9
          },
          {
            name: 'Jelly bean',
            calories: 375,
            fat: 0.0,
            carbs: 94,
            protein: 0.0
          },
          {
            name: 'Lollipop',
            calories: 392,
            fat: 0.2,
            carbs: 98,
            protein: 0
          },
          {
            name: 'Honeycomb',
            calories: 408,
            fat: 3.2,
            carbs: 87,
            protein: 6.5
          },
          {
            name: 'Donut',
            calories: 452,
            fat: 25.0,
            carbs: 51,
            protein: 4.9
          },
          {
            name: 'KitKat',
            calories: 518,
            fat: 26.0,
            carbs: 65,
            protein: 7
          }
        ]
      },

      editItem (item) {
        this.editedIndex = this.desserts.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialog = true
      },

      deleteItem (item) {
        const index = this.desserts.indexOf(item)
        confirm('Are you sure you want to delete this item?') && this.desserts.splice(index, 1)
      },

      cancel () {
        this.dialog = false
        setTimeout(() => {
          this.editedItem = Object.assign({}, this.defaultItem)
          this.editedIndex = -1 
        }, 300)
      },

      save () {
        if (this.editedIndex > -1) {
          Object.assign(this.desserts[this.editedIndex], this.editedItem)
        } else {
          this.desserts.push(this.editedItem)
        }
        this.close()
      }
    }
  }
</script>