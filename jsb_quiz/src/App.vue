<script setup>

import {computed, onMounted, ref} from "vue";
import axios from 'axios'

var pusher = new Pusher('71ed3d7e2ae1cf985ffd', {
  cluster: 'eu'
});

const question = ref(null);
const remaining_time = ref(0);
const player_name = ref(null);
const player_id = ref(null);
const plain_player_id = ref(null);
const player_list = ref([]);
const answer = ref("");
const answered = ref(false);

const volunteer_answer = ref("");
const volunteer_name = ref("");


onMounted(() => {

  const login = getCookie('login');

  if (login) {
    axios.get('https://jsb.local/api/bootstrap_new_connection')
        .then((response) => {
          console.log(response);
          const parsedLogin = JSON.parse(login);
          if (parsedLogin.game_id == response.data.game_id) {
            player_id.value = parsedLogin.player_id;
            plain_player_id.value = parsedLogin.plain_player_id;
            player_name.value = parsedLogin.player_name;
            remaining_time.value = response.data.remaining_time;
            question.value = response.data.question;
            player_list.value = response.data.player_list;
          }
        })
        .catch((error) => {
          console.log(error);
        });
  }

  const channel = pusher.subscribe('jsb-quiz-game');

  channel.bind('command', function (data) {

    switch (data.command) {
      case 'start-session':
        question.value = data.question;
        remaining_time.value = data.remaining_time;
        break;
      case 'timeout-session':
        remaining_time.value = data.remaining_time;
        //TODO resetta interfaccia
        break;
      case 'game-abort':
        alert('Il gioco è stato annullato dal conduttore');
        //TODO resetta interfaccia
        break;
      case 'answered':
        volunteer_answer.value = data.answer;
        volunteer_name.value = data.player_name;
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


const sendanswer = () => {

  axios.post('https://jsb.local/api/send_answer', {
    player_id: player_id.value,
    answer: answer.value
  }).then(function (response) {
    console.log(response);
  })
  .catch(function (error) {
    console.log(error);
    alert(error.response.data.message);
  });

  answered.value = true;

}

const setCookie =  (cname, cvalue, exdays) => {
  const d = new Date();
  d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
  let expires = "expires="+d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

const getCookie = (cname) => {
  let name = cname + "=";
  let ca = document.cookie.split(';');
  for(let i = 0; i < ca.length; i++) {
    let c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}


const subscribe = () => {
  if (player_name.value.length >= 3) {
    axios.post('https://jsb.local/api/subscribe_current_game', {
      name: player_name.value,
    })
        .then(function (response) {
          console.log(response);
          player_id.value = response.data.player_id;
          plain_player_id.value = response.data.plain_player_id;

          setCookie('login', JSON.stringify({
            player_id: response.data.player_id,
            plain_player_id: response.data.plain_player_id,
            player_name: player_name.value,
            game_id: response.data.game_id,
          }), 365);

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

          <h1 class="fs-4">Ciao {{ player_name }}</h1>
          <h2 class="fs-4">Tempo rimasto: {{ remaining_time }} secondi</h2>
          <h3 class="fs-5 pt-2">Domanda: {{ question }}</h3>
          <button v-if="isSomeoneVolonteer.length === 0" class="btn btn-outline-warning mt-5" @click="volunteer">Voglio
            prenotarmi 🤚
          </button>
          <div v-else class="mt-5">

            <div v-if="volunteer_answer && volunteer_name">
              <h4>{{volunteer_name}} ha risposto: {{volunteer_answer}}</h4>
            </div>

            <div v-else>
              <div v-if="isSomeoneVolonteer[0].plain_player_id == plain_player_id && !answered">
                <!-- Io mi sono prenotato -->
                <form @submit.prevent="sendanswer">
                  <div class="form-group">
                    <label for="answer">Inserisci la risposta che ritieni corretta:</label>
                    <textarea class="form-control w-100 mt-1" id="answer" v-model="answer"
                              placeholder="La tua risposta"></textarea>
                  </div>
                  <button class="btn btn-primary mt-3">Invia risposta</button>

                </form>
              </div>

              <div class="d-flex align-items-center " v-if="isSomeoneVolonteer[0].plain_player_id != plain_player_id">
                <!-- Altri si sono prenotati -->
                <div class="spinner-border text-warning me-3" role="status"></div>
                <div class="fs-5">{{ isSomeoneVolonteer[0].player_name }} sta rispondendo...</div>
              </div>
            </div>

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
              <td class="align-middle">
                <div class="my_raised_hand fs-3" v-if="player.volunteer">🤚</div>
              </td>
              <td class="align-middle">{{ player.player_name }}</td>
              <td class="align-middle">{{ player.score }}</td>
              <td class="align-middle">{{ player.alreadyAnswered ? '❌' : '' }}</td>
            </tr>
            </tbody>
          </table>

        </div>
      </div>

      <div v-else class="row justify-content-center">

        <div class="col-6 ">
          <h2 class="fs-5 text-center mb-5">Per partecipare al quiz, inserisci il tuo nome</h2>

          <form class="d-flex " @submit.prevent="subscribe">
            <input class="form-control" type="text" v-model="player_name" @keyup.enter="subscribe"
                   placeholder="Come ti chiami?" id="player_name"/>
            <button class="btn btn-primary">Partecipa</button>
          </form>

        </div>
      </div>


    </div>


  </main>
</template>

<style scoped>

</style>
