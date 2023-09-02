import './bootstrap';

var pusher = new Pusher('71ed3d7e2ae1cf985ffd', {
    cluster: 'eu'
});

const { createApp } = Vue

createApp({
    data() {
        return {
            remaining_time: 0,
            volunteer_remaining_time: null,
            player_list: [],
            question: "",
            game_id: false,
            session_id: false,
            volunteer_answer: null,
            volunteer_name: null,
            show_question_panel: false,
            show_answer_panel: false,
        }
    },

    methods: {

    },

    mounted() {

        axios.get('/api/refresh');

        const channel = pusher.subscribe('jsb-quiz-game');

        channel.bind('tick', (data) => {
            console.log(data)
            this.remaining_time = data.remaining_time;
            this.volunteer_remaining_time = data.volunteer_remaining_time;
        });

        channel.bind('command', (data) => {

            switch (data.command) {
                case 'refresh-game':
                    this.question = data.question;
                    this.remaining_time = data.remaining_time;
                    this.volunteer_remaining_time = data.volunteer_remaining_time;
                    this.player_list = data.player_list;
                    this.volunteer_answer = data.volunteer_answer;
                    this.volunteer_name = data.volunteer_name;
                    this.game_id = data.game_id;
                    this.session_id = data.session_id;
                    break;
                case 'game-abort':
                    alert('Il gioco è stato annullato dal conduttore');
                    //TODO resetta interfaccia
                    break;
            }

        });

    }

}).mount('#app')
