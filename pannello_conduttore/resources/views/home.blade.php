@extends('layouts.app')

@section('content')

    @php
        $plainTextToken = \Illuminate\Support\Facades\Auth::user()->createToken('token-name', ['server:update'])->plainTextToken;
    @endphp

    <input type="hidden" id="plaintoken" value="{{$plainTextToken}}">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">

                    <div class="card-header">Area per il conduttore</div>

                    <div class="card-body">

                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if($errors->any())
                            @foreach ($errors->all() as $error)
                                <div class="alert alert-danger">{{ $error }}</div>
                            @endforeach
                        @endif

                        <div id="app">

                            <div v-cloak>

                                <div class="d-flex" >

                                    <form method="POST" v-if="game_id == false" @submit.prevent="newGame()">
                                        <button type="submit" class="btn btn-primary me-2">Avvia nuovo gioco</button>
                                    </form>

                                    <button class="btn btn-primary me-2" v-if="game_id !== false && session_id == false" @click="show_question_panel=true" >Fai nuova domanda</button>

                                    <form class="ms-auto" v-if="game_id !== false" @submit.prevent="abortGame()">
                                        <button type="submit" class="btn btn-danger">Annulla gioco</button>
                                    </form>
                                </div>

                                <hr>

                                <div class="row">

                                    <div class="col-8">

                                        <h2 class="fs-3">Domanda corrente: @{{ question }}</h2>
                                        <h3 class="fs-4">Tempo rimanente: @{{ remaining_time }}</h3>
                                        <h3 class="fs-4" v-if="volunteer_remaining_time !== null">Tempo rimasto da precedente prenotazione: @{{ volunteer_remaining_time }}</h3>

                                        <div class="card mt-4" v-if="show_question_panel">
                                            <div class="card-body" >
                                                <h4 class="card-title">Inserisci la nuova domanda</h4>
                                                <form @submit.prevent="doQuestion()">
                                                    <div class="form-group">
                                                        <label for="question">Inserisci la nuova domanda:</label>
                                                        <textarea minlength="5" class="form-control w-100 mt-1" id="question" v-model="new_question"
                                                                  placeholder="La tua domanda"></textarea>
                                                    </div>
                                                    <button class="btn btn-primary mt-3">Invia domanda</button>
                                                </form>
                                            </div>
                                        </div>

                                        <div class="card mt-4" v-if="volunteer_answer && volunteer_name">
                                            <div class="card-body">
                                                <h4 class="card-title">@{{volunteer_name}} ha risposto: </h4>
                                                <p class="card-text fs-5">@{{ volunteer_answer }}</p>

                                                <div class="d-flex">
                                                    <form method="post" @submit.prevent="markRight()">
                                                        <button class="btn btn-primary">Corretta</button>
                                                    </form>

                                                    <form method="post" class="ms-auto" @submit.prevent="markWrong()">
                                                        <button class="btn btn-danger">Sbagliata</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="col-4 border-start">
                                        <h3>Lista dei partecipanti:</h3>
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th scope="col"></th>
                                                <th scope="col">Nome</th>
                                                <th scope="col">Punteggio</th>
                                                <th scope="col">Eliminato dal turno</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr v-for="player in player_list">
                                                <td class="align-middle">
                                                    <div class="fs-3" v-if="player.volunteer">🤚 <span @click="disqualify()" class="disqualify">🚷</span></div>
                                                </td>
                                                <td class="align-middle">@{{ player.player_name }}</td>
                                                <td class="align-middle">@{{ player.score }}</td>
                                                <td class="align-middle">@{{ player.alreadyAnswered ? '❌' : '' }}</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection

