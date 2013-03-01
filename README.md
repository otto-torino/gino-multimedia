multimedia library for gino CMS by Otto Srl, MIT license                  {#mainpage}
========================================================
Release 0.1 - Requires gino >= 1.3

Libreria per la gestione di contenuti multimediali geolocalizzati associati a gallerie.   
La documentazione per lo sviluppatore della versione 0.1 (generata con doxygen) è contenuta all'interno della directory doc.   
La documentazione dell'ultima versione disponibile la si trova qui:    
http://otto-torino.github.com/gino-multimedia

CARATTERISTICHE
------------------------------
Collezione di media (audio, video, immagini) geolocalizzabili, suddivisi in gallerie ed etichettati con tag. I video sono inseriti come codici che rimandano ad uno streaming (youtube, vimeo). Gli audio sono gestiti secondo le specifiche html5. La visualizzazione dei media fa uso del plugin moogallery (http://mootools.net/forge/p/moogallery).

OPZIONI CONFIGURABILI
------------------------------
- titolo vista gallerie
- titolo vista mappa con media geolocalizzati
- larghezza massima immagini
- larghezza lato lungo thumb
- template della vista lista gallerie (2 scelte disponibili: 1 galleria per riga, n gallerie per riga)
- numero massimo di gallerie per pagina
- numero massimo di media per pagina
- codice della riga di tabella template 1, con possibilità di personalizzare la visualizzazione dei campi disponibili ed applicaer filtri
- numero di gallerie per riga (template 2)
- codice della cella di tabella template 2, con possibilità di personalizzare la visualizzazione dei campi disponibili ed applicare filtri
- opzioni di configurazione dei pesi utlizzati per stabilire la priorità dei risultati a seguito di una ricerca
- gallerie promosse in home page

OUTPUTS
------------------------------
- box ultime gallerie modificate e promosse
- lista gallerie
- mappa geolocalizzazione media
- vista galleria (richiede slug galleria da GET)
- vista media (richiede id media da GET)

REQUISITI
------------------------------
- gino version >=1.3

DIPENDENZE
------------------------------
La libreria ha diverse dipendenze javascript, tutte contenute all'interno del pacchetto e licenziate come MIT:   
- markerclusterer.js
- MooComplete.js e relativo css
- moogallery.js e relativo css

INSTALLAZIONE
------------------------------
Per installare questa libreria seguire la seguente procedura:

- creare un pacchetto zip di nome "multimedia_pkg.zip" con tutti i file e le cartelle eccetto README.md e la directory doc
- loggarsi nell'area amministrativa e entrare nella sezione "moduli di sistema"
- seguire il link (+) "installa nuovo modulo" e caricare il pacchetto creato al punto 1
- creare nuove istanze del modulo nella sezione "moduli" dell'area amministrativa.
