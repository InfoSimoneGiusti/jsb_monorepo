<script setup>

import {onMounted, ref} from "vue";
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

</script>

<template>
  <main>
    <input type="text" v-model="player_name" @keyup.enter="subscribe" placeholder="Nome del giocatore"/>
    <button @click="subscribe">Partecipa</button>

    <div>Domanda: {{ question }}</div>
    <div>Tempo rimasto: {{ remaining_time }}</div>

    <section>
      <h3>Lista dei partecipanti</h3>
      <ul>
        <li v-for="player in player_list">{{ player.player_name }} - {{ player.score }}</li>
      </ul>
    </section>

  </main>
</template>

<style scoped>

</style>
