# Avvio del progetto

### Segui questa guida prima di seguire quella indicata nel file README.md di jsb_quiz

# Prerequisiti
- php8.2 accessibile da path e funzionante da CLI + dipendenze necessarie installate
- mysql
- composer 
- node versione >= 16

## Primi passi

- Crea un nuovo database MySQL
- Clona il monorepo da Github in una cartella locale del computer 
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

- Infine aggiungi queste chiavi nel file .env:

```
VITE_PUSHER_KEY=xxxxxxxxxxxxxx Riporta qua lo stesso valore di PUSHER_APP_KEY
VITE_BASE_URL=xxxxxxxxxxxxxx Riporta qua lo stesso valore di APP_URL, questo valore dovrà essere identico al dominio su cui gira l'app, per impostazione predefinita, seguendo la guida, dovrebbe essere http://localhost:8000 . Puoi verificare il valore da inserire lanciando "php artisan serve" come indicato di seguito nella guida. Se non sai cosa scrivere qua, per adesso lascia il campo vuoto, prosegui nella guida e, quando lancierai php artisan serve, copia il valore fornito e sostituiscilo nel file .env. Dovrai poi lanciare nel terminale di nuovo npm run build e poi proseguire normalmente riprendendo dalla guida nella sezione Avvio del pannello del conduttore.
```

Nella prima riga, riporta 

## Primo avvio del progetto

- In un terminale che punti alla root del progetto lancia i seguenti comandi

```
- composer install // in caso di ulteriori dipendenze necessarie, in questa fase sarà nesessario configurare il computer secondo necessità
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

