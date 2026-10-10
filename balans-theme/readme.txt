=== Balans — WordPress-thema ===

Doel
----
Een moderne, warme en professionele WordPress-website rond het thema "Van vicieuze cirkel naar balans".
Compleet thema met herbruikbare templates, WooCommerce-ondersteuning voor de shop, een ingebouwde
afsprakenpagina (consult / drukpuntmassage + consult) en custom post types voor meditaties,
trainingen en zakelijke trainingen.

Installatie
-----------
1. Zip de map "balans-theme" (of gebruik het meegeleverde .zip-bestand).
2. In je WordPress-beheer: Weergave → Thema's → Nieuw thema toevoegen → Thema uploaden.
3. Kies het .zip-bestand → Nu installeren → Activeren.
4. Tijdens activering worden automatisch:
   - alle paginas aangemaakt (Voor jou, Shop, Afspraak maken, Contact, enz.);
   - de homepage ingesteld;
   - een hoofdmenu "Balans hoofdnavigatie" gevuld;
   - custom post types geregistreerd (Meditaties, Trainingen, Zakelijk aanbod, Eigen blogs,
     Afspraken, Offerteaanvragen).
5. (Optioneel) Installeer WooCommerce voor de shop (menu-item Shop) en Nederlandse betaalmethoden
   zoals Mollie of een vergelijkbare plugin.
6. Ga naar Instellingen → Permalinks en klik op Opslaan om de rewrite rules te verversen.

Belangrijkste features
----------------------
- Mobile-first, toegankelijk, met zichtbare focusringen en respect voor prefers-reduced-motion.
- Herbruikbare templates voor diensten, trainingen, producten en artikelen.
- Ingebouwde afsprakenkalender met tijdslots (AJAX, slaat op als CPT "Afspraken").
- Offerteformulier voor zakelijke groepsaanvragen (slaat op als CPT "Offerteaanvragen").
- Contactformulier (verstuurt naar admin e-mail).
- WooCommerce-compatibele shop-templates (gebruikt WooCommerce indien geactiveerd).
- Meditatie-overzicht: gratis audio direct afspeelbaar zonder registratie; faseplan voor
  betaalde programma's en abonnementen via de shop.
- Custom post type "Meditaties" met velden voor audio-URL en duur
  (gebruik ACF of handmatige custom fields: _balans_audio_url, _balans_duration).

Wat de eigenaar nog moet aanvullen
----------------------------------
- Echte teksten op alle paginas met placeholder-blokken.
- Audiobestanden voor meditaties.
- Afbeeldingen voor diensten, trainingen en producten.
- Instagram-URL in de footer.
- Juridische teksten: Privacyverklaring, Cookies, Algemene voorwaarden, Webshopvoorwaarden,
  Verzend- & retourinformatie, Disclaimer.
- Logo via Weergave → Aanpassen → Site-identiteit.

Shop (WooCommerce)
------------------
De shop verkoopt "Kristallen & (half)edelstenen". Activeer WooCommerce om de shop volledig
te laten werken. Voeg producten toe en koppel categorieën. Winkelmandje en checkout zijn
onderdeel van WooCommerce zelf.

Betaalde meditaties en trainingen
---------------------------------
- Faseplan voor meditaties staat beschreven in /inc/* commentaren.
- Voor terugkerende betalingen (abonnementen) is WooCommerce Subscriptions
  of een gelijkwaardige plugin nodig; dit is nog niet geactiveerd en moet door de eigenaar
  met de ontwikkelaar afgestemd worden.

Mocked onderdelen (nog niet live)
---------------------------------
- De afspraken-flow slaat verzoeken op; er is nog geen externe kalender-integratie.
- Betalingen: alleen aanwezig via WooCommerce; geen eigen betaalkoppeling.
- Audio-bestanden voor meditaties zijn placeholders.

Licentie
--------
GPL v2 of later.
