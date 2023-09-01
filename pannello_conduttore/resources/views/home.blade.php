@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Area per il conduttore</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                </div>

                <div id="app">
                    Tempo rimanente: @{{ remaining_time }}
                    Lista dei partecipanti: @{{ player_list }}
                </div>

            </div>
        </div>
    </div>
</div>

@endsection

