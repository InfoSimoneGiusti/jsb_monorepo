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
            question: "",
            current_game: null,
            current_session: null,
            show_question_panel: false,
            show_answer_panel: false,
        }
    },

    methods: {

    },

    mounted() {

        axios.get('/api/bootstrap_new_connection')
            .then((response) => {
                console.log(response);
                this.remaining_time = response.data.remaining_time;
                this.current_game = response.data.current_game;
                this.question = response.data.question;
                this.player_list = response.data.player_list;
                this.current_session = response.data.current_session;
            })
            .catch((error) => {
                console.log(error);
            });

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
