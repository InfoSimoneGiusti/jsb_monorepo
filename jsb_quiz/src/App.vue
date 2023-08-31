<script setup>
import HelloWorld from './components/HelloWorld.vue'
import {onMounted, ref} from "vue";

var pusher = new Pusher('71ed3d7e2ae1cf985ffd', {
  cluster: 'eu'
});

const question = ref(null)
const end_session = ref(0)
const server_time = ref(0)

const client_deadline = ref(0) //calcolo sul client quando è previsto lo scadere del tempo

const count_down = ref(0);

onMounted(() => {

  const channel = pusher.subscribe('my-channel');
  channel.bind('my-event', function(data) {
    question.value = data.question;
    end_session.value = data.end_session;
    server_time.value = data.server_time;

    const localUnixTime = Math.floor(Date.now() / 1000);
    const delta = localUnixTime - server_time.value; //tempo di propagazione del evento pusher + scarto orologio server / client

    client_deadline.value = localUnixTime - delta + (end_session.value - server_time.value);

    const clock = setInterval(() => {
      count_down.value = client_deadline.value - Math.floor(Date.now() / 1000);
    }, 1000)

  });

})


</script>

<template>
  <header>
    <img alt="Vue logo" class="logo" src="./assets/logo.svg" width="125" height="125" />

    <div class="wrapper">
      <HelloWorld msg="You did it!" />
    </div>
  </header>

  <main>
    {{count_down}}
  </main>
</template>

<style scoped>
header {
  line-height: 1.5;
}

.logo {
  display: block;
  margin: 0 auto 2rem;
}

@media (min-width: 1024px) {
  header {
    display: flex;
    place-items: center;
    padding-right: calc(var(--section-gap) / 2);
  }

  .logo {
    margin: 0 2rem 0 0;
  }

  header .wrapper {
    display: flex;
    place-items: flex-start;
    flex-wrap: wrap;
  }
}
</style>
