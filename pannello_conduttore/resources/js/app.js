import './bootstrap';

var pusher = new Pusher('71ed3d7e2ae1cf985ffd', {
    cluster: 'eu'
});

const {createApp} = Vue

const plain_token = document.getElementById('plaintoken');

const instance = axios.create({
    baseURL: 'https://jsb.local/api/',
    headers: {'Authorization': 'Bearer ' + plain_token.value}
});

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

            new_question: null
        }
    },

    methods: {

        resetGame() {
            this.remaining_time = 0;
            this.volunteer_remaining_time = null;
            this.player_list = [];
            this.question = "";
            this.game_id = false;
            this.session_id = false;
            this.volunteer_answer = null;
            this.volunteer_name = null;
            this.show_question_panel = false;
            this.show_answer_panel = false;
        },

        doQuestion() {
            instance.post('new_question',
                {
                    'new_question': this.new_question
                }
            )
                .then((response) => {
                    console.log(response.data);
                    this.new_question = "";
                    this.show_question_panel = false;
                }).catch(function (error) {
                console.log(error);
                alert(error.response.data.message);
            });
        },

        abortGame() {
            const response = confirm('Sei sicuro di voler annullare il gioco?');
            if (response) {
                instance.post('abort_game')
                    .then((response) => {
                        console.log(response.data);
                        this.resetGame();
                    })
                    .catch(function (error) {
                        console.log(error);
                        alert(error.response.data.message);
                    });
            }
        },

        newGame() {
            instance.post('new_game')
                .then((response) => {
                    console.log(response.data);
                })
                .catch(function (error) {
                    console.log(error);
                    alert(error.response.data.message);
                });
        },

        markRight() {
            instance.post('mark_right')
                .then((response) => {
                    console.log(response.data);
                })
                .catch(function (error) {
                    console.log(error);
                    alert(error.response.data.message);
                });
        },

        markWrong() {
            instance.post('mark_wrong')
                .then((response) => {
                    console.log(response.data);
                })
                .catch(function (error) {
                    console.log(error);
                    alert(error.response.data.message);
                });
        },

        disqualify() {
            const response = confirm('Sei sicuro di voler squalificare il giocatore da questo turno?');
            if (response) {
                instance.post('disqualify')
                    .then((response) => {
                        console.log(response.data);
                    })
                    .catch(function (error) {
                        console.log(error);
                        alert(error.response.data.message);
                    });
            }
        }
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
                    this.resetGame();
                    break;
            }

        });

    }

}).mount('#app')
