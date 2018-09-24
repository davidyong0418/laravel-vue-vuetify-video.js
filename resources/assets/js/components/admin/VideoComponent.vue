<template>
  <v-layout row>
    <v-flex  sm5 offset-sm1>
      <v-card>
        <v-toolbar color="indigo" dark>
          <v-toolbar-side-icon></v-toolbar-side-icon>

          <v-toolbar-title>Inbox</v-toolbar-title>

          <v-spacer></v-spacer>

          <v-btn icon>
            <v-icon>search</v-icon>
          </v-btn>

          <v-btn icon>
            <v-icon>more_vert</v-icon>
          </v-btn>
        </v-toolbar>
        <v-list>
        <v-radio-group v-model="selected">
            <v-list-tile v-for="video in videos" :key="video.id" @click="selected = video.id">
            
              <v-list-tile-action>
                <v-radio 
                name="video"
                v-bind:value="video.id"
                @click.prevent=""
                /></v-radio>
              </v-list-tile-action>
              <!-- <img v-bind:src="track.image" class="img-md mr-3" /> -->
              <v-list-tile-content>
                <v-list-tile-title>{{video.alias}}</v-list-tile-title>
              </v-list-tile-content>

              <v-list-tile-content>
                <v-list-tile-title>{{video.vimeo_url}}</v-list-tile-title>
              </v-list-tile-content>

            </v-list-tile>
			    </v-radio-group>
        </v-list>
      </v-card>
    </v-flex>
    <v-flex  sm5 offset-sm1>
      <v-card>
        <v-toolbar color="indigo" dark>
          <v-toolbar-side-icon></v-toolbar-side-icon>

          <v-toolbar-title>Inbox</v-toolbar-title>

          <v-spacer></v-spacer>

          <v-btn icon>
            <v-icon>search</v-icon>
          </v-btn>

          <v-btn icon>
            <v-icon>more_vert</v-icon>
          </v-btn>
        </v-toolbar>
        <v-list>
          <v-flex sm7 offset-sm1>
            <v-text-field
                            label="Add Vimeo Url"
                            v-model="add_vimeo_url" 
                            required></v-text-field>
            <v-text-field
                            label="Add Vimeo Alias"
                            v-model="add_vimeo_alias"
                            required ></v-text-field>
            </v-text-field>
          </v-flex>
          <v-flex sm3>
            <v-btn @click="add_video">Add</v-btn>
            </v-text-field>
          </v-flex>
        </v-list>
      </v-card>
    </v-flex>
  </v-layout>
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
    mixins: [withSnackbar],
    data () {
      return {
        errors: [],
        internalAction: this.action,
        loginLoading: false,
        selected:'',
        videos:[],
        add_vimeo_url:'',
        add_vimeo_alias:''
      }
    },
    props: {
      action: {
        type: String,
        default: null
      },
      show: {
        type: Boolean,
        default: true
      }
    },
    computed: {
      showLogin: {
        get () {
          if (this.internalAction && this.internalAction === 'login') return true
          return false
        },
        set (value) {
          if (value) this.internalAction = 'login'
          else this.internalAction = null
        }
      }
    },
    methods: {
      getvideos: function() {
        alert('getvideo');
        var params = new URLSearchParams()
        this.loading = true
        axios.get('/api/admin/video-management/', params, {headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
        .then( function (response) {
          this.loading = false
          console.log(response)
          this.videos = response.data.videos
        }.bind(this))
        .catch(function (error) {
          this.loading = false
          console.log(error.response)
        }.bind(this))
      },
      add_video: function() {
        axios.post('/api/admin/video-management/create', {
          video_url:this.add_vimeo_url,
          video_alias: this.add_vimeo_alias,
          _token: 'asdffffffffffffff2342423'
        }, {headers: {'Content-Type': 'application/json', }})
        .then( function (response) {
          console.log(response)
          this.videos = response.data.videos
        }.bind(this))
        .catch(function (error) {
          console.log(error.response)
        }.bind(this));

        this.add_vimeo_url = '';
        this.add_vimeo_alias = '';
      },
     
    },
    mounted(){
        this.getvideos();
    }    
  }
</script>
