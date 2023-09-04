# Avvio del progetto 

### Prima di seguire questa guida, segui quella indicata nel file README.md del pannello_conduttore!

## Primo avvio del progetto

- Copia il file .env.example che trovi nella root di jsb_quiz e rinominalo in .env
- Compila il file .env in accordo alla tua configurazione

```
VITE_PUSHER_KEY=Il valore key che trovi in App Keys di Pusher
VITE_BASE_URL=Il valore indicato da php artisan serve, di default potrebbe essere http://127.0.0.1:8000

- Apri un terminale e vai nella root di jsb_quiz, quindi lancia i seguenti comandi:

```
- npm install
- npm run build
```

## Avvio del quiz come partecipante

Ogni volta in cui vorrai partecipare al gioco, apri un terminale che punti alla root di jsb_quiz e lancia:

```
- npm run preview
```

Apri il link indicato dal comando appena lanciato (di default potrebbe essere http://127.0.0.1:4173) e divertiti!