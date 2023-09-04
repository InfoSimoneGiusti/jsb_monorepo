# Avvio del progetto

### Segui questa guida prima di seguire quella indicata nel file README.md di jsb_quiz

- Crea un nuovo database MySQL
- Copia il file .env.example che trovi nella root di pannello_conduttore e rinominalo in .env

## Configura il file .env appena creato

- Nella sezione mostrata di seguito imposta username, password e nome del DB precedentemente creati

```
DB_DATABASE=jsb
DB_USERNAME=root
DB_PASSWORD=root
```
- Registrati su Pusher.com, crea un nuovo canale e ottieni le credenziali API necessarie, dopodichè compila anche questa sezione del file

```
PUSHER_APP_ID=xxxxxxxxxxxxxx
PUSHER_APP_KEY=xxxxxxxxxxxxxx
PUSHER_APP_SECRET=xxxxxxxxxxxxxx
PUSHER_APP_CLUSTER=eu
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
```

## Primo avvio del progetto

- In un terminale che punti alla root del progetto lancia i seguenti comandi

```
- composer install
- npm install
- php artisan key:generate
- php artisan migrate
- php artisan db:seed
- npm run build
```

## Avvio del pannello del conduttore

Adesso dovresti essere in grado di avviare il programma in Laravel. Per farlo, apri 2 distinti terminali che puntino alla root del progetto.
Questi comandi dovranno essere lanciati in seguito ogni volta che vuoi avviare il progetto:

Nel primo lancia:

```
php artisan short-schedule:run 
```

Nel secondo lancia

```
php artisan serve
```

Controlla l'output fornito dal comando appena inviato. Per connetterti al pannello di gioco dovrai aprire questo link. Questo link sarà necessario anche alla configurazione del pannello di gioco in front del quiz.

Per accedere al pannello, potrai usare le credenziali di default generate dal processo di installazione, che sono:

```
email: conduttore@jsb.local
password: jsb-quiz
```

Buon divertimento!!!

