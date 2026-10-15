# Balans — WordPress-thema (PRD)

## Oorspronkelijk problem statement
Gebruiker vroeg om een WordPress-website rond "Van vicieuze cirkel naar balans".
Omdat Emergent niet in WordPress uitlevert, is in overleg gekozen voor een compleet,
uploadbaar WordPress-thema (.zip) dat de gebruiker op zijn eigen WordPress-installatie plaatst.

## Keuzes gebruiker
- Formaat: Compleet WordPress-thema (PHP + style.css + functions.php), .zip-upload.
- Shop: WooCommerce. Afspraken: eigen custom afsprakenpagina met form handler (AJAX).
- Eerste scope: Homepage, Shop, Afspraken.
- Betalingen: mock/placeholder (afhankelijk van WooCommerce + gateway van gebruiker).
- Afsprakenkalender: ingebouwd (datum + tijdslot, opslaan in DB als CPT).
- Meditatie-audio: placeholders, duidelijk gelabeld.
- Voorbeeldcontent: placeholder teksten met "nog aan te leveren"-notices.

## Architectuur
Thema `balans-theme/` met:
- Hoofdbestanden: style.css, functions.php, header.php, footer.php, front-page.php,
  page.php, single.php, index.php, archive.php, search.php, searchform.php, 404.php.
- /inc/: theme-setup.php, enqueue.php, cpt.php, form-handler.php, appointments.php,
  woocommerce.php, template-helpers.php, placeholder-content.php, install-pages.php.
- /page-templates/: voor-jou, voor-bedrijven, meditatie, consult, drukpuntmassage,
  abonnementen, trainingen, offerte, artikelen, over, afspraak, contact.
- /assets/css/main.css, /assets/js/main.js, /assets/images/.
- /woocommerce/ (voor eventuele template overrides).
- readme.txt met installatie-instructies.

## Features gereed
- Mobile-first responsive layout, Fraunces (serif) + Instrument Sans (sans) typografie.
- Warme crème/klei kleurpalet met goede contrast en focusringen.
- Custom post types: Meditaties, Trainingen, Zakelijke trainingen, Eigen blogs,
  Afspraken (intern), Offerteaanvragen (intern).
- Automatische pagina-installatie bij thema-activering (incl. hoofdmenu).
- AJAX-formulieren: afspraak, contact, offerte — opslag als CPT + e-mail naar admin.
- WooCommerce-ondersteuning + aangepaste shop-hero "Kristallen & (half)edelstenen".
- Placeholder-notices op elke pagina met uit te leveren content.
- Toegankelijkheid: skip-link, focus-visible, prefers-reduced-motion.
- Homepage met hero + "Waar ben je naar op zoek?" (5 routes) + zakelijk + shop + aanpak.

## Mocked / nog aan te vullen door eigenaar
- Alle echte teksten, afbeeldingen, audiobestanden.
- Instagram URL, contactgegevens, logo.
- Juridische teksten (privacy, cookies, voorwaarden, disclaimer).
- Werkelijke tijdslot-beschikbaarheid (nu statische voorbeelden).
- Betaalmethoden via WooCommerce (bijv. Mollie) — klant installeert zelf.
- Terugkerende betalingen (WooCommerce Subscriptions) — nog niet geactiveerd.

## Oplevering
- `/app/balans-theme.zip` — uploadbaar via Weergave → Thema's → Thema uploaden.

## Backlog / volgende fases
- P1: Trainingdetail-template met prijs + "In winkelmand" via WooCommerce.
- P1: Meditatie-detail template met beveiligde audio-URL (member-only).
- P2: WooCommerce Subscriptions-integratie voor meditatieabonnementen.
- P2: Beveiligde audio (afgeschermde media, niet alleen hidden page).
- P3: Externe kalender-koppeling (bijv. Google Calendar/ical) voor afspraken.
- P3: SEO-metabeschrijvingen en schema.org markup per CPT.

## Update — lettertypes lokaal (juni 2026)
- Google Fonts CDN verwijderd; Fraunces + Instrument Sans lokaal (woff2) in balans-theme/assets/fonts, @font-face in assets/css/fonts.css, enqueued via inc/enqueue.php.
- Desktop-lettertypes (statische TTF Regular/Medium/SemiBold/Bold + cursief, en variabele TTF) in /app/lettertypes + /app/balans-lettertypes.zip (met LEESMIJ.txt en OFL-licentie) voor briefpapier/visitekaartjes.
- 404-probleem bevestigd opgelost door gebruiker (permalinks opnieuw opslaan).
- Backlog: Meditaties fase 2/3 (betaalde programma's, abonnementen), WooCommerce-inrichting begeleiden.

## Update — logo & visitekaartje (juni 2026)
- Logo gekozen: EQ5-woordmerk in teal #3C6E71 (Fraunces SemiBold, Q = O met verticale staart midden onder). Q mag terugkomen als beeldaccent in teksten/website.
- Logo-opties (SVG + overzicht.png) in /app/drukwerk/logo-opties (script: drukwerk/scripts/make_logos.py).
- Visitekaartje 85x55, dubbelzijdig, CMYK, 3 mm afloop, snijtekens, fonts ingesloten: /app/drukwerk/visitekaartje (script: make_business_card.py). Gegevens: Remco Sas, Balansbrenger, +31 6 23 988 488, www.eq5.nl.
- Bundel: /app/balans-drukwerk.zip
- Backlog: EQ5-logo + Q-accent in WordPress-thema verwerken; briefpapier-sjabloon; meditaties fase 2/3.
- (juni 2026) Q-staart herzien: vloeiende uitloop uit de ring (geen insnoering), gedeeld via tail_commands() in make_logos.py. Achterkant visitekaartje: diensten "consulten · workshops / cursussen · ondersteuning" toegevoegd.
- (juni 2026) Thema v1.1.0: EQ5-logo (inline SVG, teal via --balans-logo) in header en footer (crème), grote lichte Q als accent in home-hero, kleine Q in footer-bottom, Q-favicon (SVG/PNG, alleen als geen Site-pictogram ingesteld). Assets via drukwerk/scripts/export_theme_assets.py -> balans-theme/assets/images. Helper balans_svg() in inc/template-helpers.php.
