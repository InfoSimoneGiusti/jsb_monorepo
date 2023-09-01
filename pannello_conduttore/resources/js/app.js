import './bootstrap';

var pusher = new Pusher('71ed3d7e2ae1cf985ffd', {
    cluster: 'eu'
});

const { createApp } = Vue

createApp({
    data() {
        return {
            remaining_time: null,
            player_list: [],
            question: ""
        }
    },

    mounted() {

        const channel = pusher.subscribe('jsb-quiz-game');

        channel.bind('tick', (data) => {
            console.log(data)
            this.remaining_time = data.remaining_time;
        });

        channel.bind('players-info', (data) => {
            console.log(data)
            this.player_list = data.player_list;
            this.question = data.question;
        });
    }

}).mount('#app')
