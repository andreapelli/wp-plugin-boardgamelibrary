=== Board Game Library ===
Contributors: [Your Name or Username]
Tags: board games, game library, custom post type, csv import
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.2.0
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Board Game Library è un plugin per WordPress che permette di gestire e visualizzare una collezione di giochi da tavolo, con funzionalità di importazione CSV e integrazione con BoardGameGeek.

== Description ==

Board Game Library è un plugin che aggiunge un custom post type "Gioco da Tavolo" al tuo sito WordPress. Permette di catalogare e visualizzare una collezione di giochi da tavolo con dettagli specifici come numero di giocatori, età consigliata, durata della partita e livello di difficoltà.

Caratteristiche principali:
* Custom post type "Gioco da Tavolo"
* Campi personalizzati per dettagli specifici dei giochi
* Tassonomie per ambientazione, meccanica e stile di gioco
* Template personalizzati per la visualizzazione dei giochi
* Visualizzazione a griglia o lista nella pagina di archivio
* Importazione di giochi da file CSV
* Integrazione con BoardGameGeek per il recupero automatico delle immagini di copertina

== Installation ==

1. Carica la cartella `boardgamelibrary` nella directory `/wp-content/plugins/` del tuo sito WordPress.
2. Attiva il plugin dalla pagina 'Plugin' in WordPress.
3. Vai su 'Giochi da Tavolo' nel menu di amministrazione per iniziare ad aggiungere giochi alla tua libreria o per importare giochi da un file CSV.

== Frequently Asked Questions ==

= Come posso aggiungere un nuovo gioco? =

Vai su 'Giochi da Tavolo' nel menu di amministrazione e clicca su 'Aggiungi nuovo'. Compila i campi con i dettagli del gioco e pubblica.

= Posso importare più giochi contemporaneamente? =

Sì, puoi importare giochi da un file CSV. Vai su 'Giochi da Tavolo' > 'Importa CSV' nel menu di amministrazione e carica il tuo file CSV.

= Qual è il formato corretto per il file CSV di importazione? =

Il file CSV dovrebbe avere le seguenti colonne in questo ordine:
Titolo, Min Giocatori, Max Giocatori, Età Minima, Tempo di Gioco, Materiali, BGG ID, Numero Catalogo, Difficoltà, Abstract, Ambientazione, Meccanica, Stile di Gioco

= Il plugin recupera automaticamente le immagini di copertina? =

Sì, il plugin tenta di recuperare automaticamente l'immagine di copertina da BoardGameGeek utilizzando l'ID BGG fornito nel CSV.

== Screenshots ==

1. Pagina di archivio dei giochi da tavolo
2. Pagina di dettaglio di un singolo gioco
3. Interfaccia di amministrazione per l'aggiunta di un nuovo gioco
4. Pagina di importazione CSV

== Changelog ==

= 1.2.0 =
* Aggiunta un template personalizzato per la tassonomia "stile di gioco" per uniformare la visualizzazione con l'archivio dei giochi.
* Corretto il caricamento dei link alle tassonomie per puntare alle pagine corrette.
* Migliorata la logica di visualizzazione della difficoltà nel template del singolo gioco.
* Aggiornato il file README con le ultime modifiche.

= 1.1.0 =
* Aggiunta funzionalità di importazione CSV
* Integrazione con BoardGameGeek per il recupero automatico delle immagini di copertina
* Aggiunto campo per il numero di catalogo
* Migliorata la gestione delle tassonomie durante l'importazione

= 1.0.0 =
* Versione iniziale del plugin

== Upgrade Notice ==

= 1.2.0 =
Questa versione aggiunge un template personalizzato per la tassonomia "stile di gioco" e migliora la gestione dei link alle tassonomie. Si consiglia l'aggiornamento per tutti gli utenti.

== CSV Import Format ==

Per importare correttamente i giochi, il tuo file CSV dovrebbe avere le seguenti colonne nell'ordine specificato:

1. Titolo
2. Numero minimo di giocatori
3. Numero massimo di giocatori
4. Età minima
5. Tempo di gioco
6. Materiali
7. ID BoardGameGeek
8. Numero di catalogo
9. Difficoltà (1-5)
10. Abstract
11. Ambientazione (separati da virgola se più di uno)
12. Meccanica (separati da virgola se più di uno)
13. Stile di gioco (separati da virgola se più di uno)

Assicurati che il tuo file CSV sia formattato correttamente per garantire un'importazione senza errori.