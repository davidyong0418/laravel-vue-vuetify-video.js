@extends('layouts.app')

@section('content')

    <v-container fluid grid-list-md text-xs-center>
        <v-layout row wrap>
            <v-flex xs12>
                <v-card>
                    <v-card-title class="blue darken-3 white--text"><h2>Home</h2></v-card-title>
                    <v-fade-transition mode="out-in">
                        <router-view></router-view>
                    </v-fade-transition>
                    
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>

@endsection
