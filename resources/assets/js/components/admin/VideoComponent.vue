<template>
  <v-layout row>
    <v-flex sm8>
      <v-card>
        <v-toolbar>
          <v-toolbar-title>Video list</v-toolbar-title>
        </v-toolbar>
        <v-card-title>
            <span v-if="exist_video == true">No Selected Video</span>
          </v-card-title>
        <v-list>
        <v-radio-group v-model="selected">
          <div v-for="video in videos">
            <v-list-tile :key="video.id" @click="selected = video.id">
            
              <v-list-tile-action>
                <v-radio 
                name="video"
                v-bind:value="video.id"
                @click.prevent=""
                /></v-radio>
              </v-list-tile-action>
              <v-list-tile-content>
                <v-list-tile-title>{{video.alias}}</v-list-tile-title>
              </v-list-tile-content>

              <v-list-tile-content>
                <v-list-tile-title>{{video.vimeo_url}}</v-list-tile-title>
              </v-list-tile-content>
            </v-list-tile>
            <v-slide-y-transition>
                <v-layout v-show="transition[video.id]" row wrap ml-5>
                  <v-flex sm3>
                    <v-img :src="video.thumbnail"></v-img>
                  </v-flex>
                  <v-flex sm2></v-flex>
                  <v-flex mr-5 sm3>
                    {{video.description}}
                  </v-flex>
                  <v-flex sm10 class="text-xs-right">
                    <v-btn color="success" small @click="selectVideo(video.id)">Select</v-btn>
                  </v-flex>
                </v-layout>
              </v-slide-y-transition>
          </div>
			    </v-radio-group>
        </v-list>
      </v-card>
    </v-flex>
    <v-flex  sm4>
      <v-card>
          <v-toolbar>
          <v-toolbar-title>Add Video</v-toolbar-title>
          <v-spacer></v-spacer>
        </v-toolbar>
        <v-list>
          <v-flex sm7 offset-sm1>
            <v-text-field
                            label="Vimeo Url"
                            v-model="add_vimeo_url" 
                            required class="mb-3"></v-text-field>
                            <v-spacer></v-spacer>
            <v-text-field
                            label="Vimeo Alias"
                            v-model="add_vimeo_alias"
                            required class="mb-3"></v-text-field>
            </v-text-field>

            <v-text-field
                            label="Vimeo Description"
                            v-model="add_vimeo_description"
                            required class="mb-3"></v-text-field>
            </v-text-field>
          </v-flex>
          <v-flex sm3 offset-sm6>
            <v-btn color="primary" flat-right @click="addVideo">Add</v-btn>
            </v-text-field>
          </v-flex>
        </v-list>
      </v-card>
    </v-flex>
  </v-layout>
</template>


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
        selected:0,
        videos:[],
        transition: [],
        prior_val:'',
        add_vimeo_url:'',
        add_vimeo_alias:'',
        add_vimeo_description:'',
        exist_video:false,
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
    watch: {
      selected(val){
        this.transition[this.prior_val] = false;
        this.transition[val] = true;
        this.prior_val = val;
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
      mounted(){
          this.getVideos();
      },
      methods: {

      selectVideo: function(selected_id){
        axios.post('/api/admin/video-management/select_video', {data:selected_id}, {headers: {'Content-Type': 'application/json'}})
        .then( function (response) {
          this.showMessage(`Successfully Selected`);
        }.bind(this))
        .catch(function (error) {
          this.showError('Not selected');
        }.bind(this))
      },
      getVideos: function() {
        var params = new URLSearchParams()
        this.loading = true
        axios.get('/api/admin/video-management/', params, {headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
        .then( function (response) {
          this.loading = false
          this.videos = response.data.videos
          this.selected = response.data.selected_video
          this.exist_video = false
          if(this.selected == '')
          {
            this.exist_video = true
          }
        }.bind(this))
        .catch(function (error) {
          this.loading = false
        }.bind(this))
      },
      addVideo: function() {
        var send_info = {
          'video_url' : this.add_vimeo_url,
          'video_alias' : this.add_vimeo_alias,
          'video_description' : this.add_vimeo_description
        };

        axios.post('/api/admin/video-management/create', {
          data:JSON.stringify(send_info)
        }, {headers: {'Content-Type': 'application/json', }})
        .then( function (response) {
          if(response.data.action == true)
          {
              this.showMessage(`Successfully Saved`);
              this.videos = response.data.result;
          }
          else
          {
            this.showError(response.data.result);
          }
        }.bind(this))
        .catch(function (error) {
          console.log(error.response)
          this.showError("Invalid vimeo url");
        }.bind(this));
        this.add_vimeo_url = '';
        this.add_vimeo_alias = '';
        this.add_vimeo_description = '';
      },
    },

  }
</script>
