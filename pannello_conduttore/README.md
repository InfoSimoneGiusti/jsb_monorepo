# Avvio del progetto

- Crea un nuovo database MySQL
- Dopo aver clonato il repo in locale, copia il file .env.example che trovi nella root del progetto e rinominalo in .env

## Configura il file .env appena creato

- Nella sezione mostrata di seguito imposta username, password e nome del DB precedentemente creati

```
DB_DATABASE=jsb
DB_USERNAME=root
DB_PASSWORD=root
```

## Avvio del progetto

- In un terminale che punti alla root del progetto lancia i seguenti comandi

```
- composer install
- npm install
- php artisan migrate
- php artisan db:seed
- npm run build
- php artisan serve
```
