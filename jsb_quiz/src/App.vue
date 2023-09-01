<script setup>

import {computed, onMounted, ref} from "vue";
import axios from 'axios'

var pusher = new Pusher('71ed3d7e2ae1cf985ffd', {
  cluster: 'eu'
});

const question = ref(null)
const remaining_time = ref(0)
const player_name = ref(null)
const player_id = ref(null)
const player_list = ref([])

onMounted(() => {

  const channel = pusher.subscribe('jsb-quiz-game');

  channel.bind('command', function (data) {

    switch (data.command) {
      case 'start-session':
        question.value = data.question;
        remaining_time.value = data.remaining_time;
        break;
      case 'timeout-session':
        remaining_time.value = data.remaining_time;
        //resetta interfaccia
        break;
    }

  });

  channel.bind('tick', function (data) {
    remaining_time.value = data.remaining_time;
  });

  channel.bind('players-info', function (data) {
    player_list.value = data.player_list;
    question.value = data.question;
  });

});


const subscribe = () => {
  if (player_name.value.length >= 3) {
    axios.post('https://jsb.local/api/subscribe_current_game', {
      name: player_name.value,
    })
        .then(function (response) {
          console.log(response);
          player_id.value = response.data.player_id;
        })
        .catch(function (error) {
          console.log(error);
          alert(error.response.data.message);
        });
  } else {
    alert('La lunghezza minima del nome è di 3 caratteri');
  }
};

const volunteer = () => {
  axios.post('https://jsb.local/api/volunteer', {
    player_id: player_id.value,
  })
      .then(function (response) {
        console.log(response);
      })
      .catch(function (error) {
        console.log(error);
        alert(error.response.data.message);
      });
}

const isSomeoneVolonteer = computed(() => {
  return player_list.value.filter((player) => player.volunteer)
})

</script>

<template>
  <header>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
      <div class="container-fluid justify-content-center">
        <h1 class="navbar-brand">JSB Quiz</h1>
      </div>
    </nav>
  </header>
  <main>

    <div class="container mt-5">

      <div v-if="player_id" class="row align-items-start">
        <div class="col-8">
          <h3 class="fs-4">Tempo rimasto: {{ remaining_time }} secondi</h3>
          <h1 class="fs-5 pt-2">Domanda: {{ question }}</h1>
          <button v-if="isSomeoneVolonteer.length === 0"  class="btn btn-outline-warning mt-5" @click="volunteer">Voglio prenotarmi 🤚</button>
          <div v-else class="d-flex align-items-center mt-5">
            <div class="spinner-border text-warning me-3" role="status"></div>
            <div class="fs-5">{{isSomeoneVolonteer[0].player_name}} sta rispondendo...</div>
          </div>
        </div>

        <div class="col-4">
          <h2 class="fs-5 mb-3">Lista dei partecipanti:</h2>

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
                <td class="align-middle"><div class="my_raised_hand fs-3" v-if="player.volunteer">🤚</div></td>
                <td class="align-middle">{{ player.player_name }}</td>
                <td class="align-middle">{{ player.score }}</td>
                <td class="align-middle">{{ player.alreadyAnswered?'❌':'' }}</td>
              </tr>
            </tbody>
          </table>

        </div>
      </div>

      <div v-else class="row justify-content-center">

        <div class="col-6 ">
          <h2 class="fs-5 text-center mb-5">Per partecipare al quiz, inserisci il tuo nome</h2>

          <div class="d-flex ">
            <input class="form-control" type="text" v-model="player_name" @keyup.enter="subscribe"
                   placeholder="Nome del giocatore"/>
            <button class="btn btn-primary" @click="subscribe">Partecipa</button>
          </div>

        </div>
      </div>


    </div>


  </main>
</template>

<style scoped>

</style>
