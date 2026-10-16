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
- (juni 2026) E-mailhandtekening: /app/drukwerk/email-handtekening (plain-text voor /e/OS Mail — K-9-fork, geen HTML; HTML-versie met logo voor webmail/Thunderbird). Logo-PNG gehost via thema: assets/images/email-logo-eq5.png (thema v1.1.1). Geen e-mailadres in handtekening (wens gebruiker).
- (juni 2026) Logobestanden teal (los + met slogan 'van vicieuze cirkel naar balans') SVG/PNG/PDF in /app/drukwerk/logo (script make_logo_files.py).

## Update — thema v1.2.0: EQ5-huisstijl + meertalig NL/EN/FR (juni 2026)
- Huisstijl: primaire kleur teal #3C6E71 (knoppen, links, hero-visual, placeholders), footer diep teal #1F3B3D, footer-logo met slogan per taal, contactgegevens (Remco Sas, Balansbrenger, +31 6 23 988 488, www.eq5.nl) in footer.
- Meertalig via Polylang (gratis): inc/i18n.php — balans_url() (taalbewuste links o.b.v. NL-slug), balans_lang(), taalkeuze NL·EN·FR in header, CPT's/taxonomie vertaalbaar, admin-melding + knop die EN/FR-pagina's aanmaakt/koppelt, browser-detectie en redirect_lang aanzetten, Polylang taalcache legen.
- Themastrings (229) vertaald: languages/{en_US,en_GB,fr_FR,fr_BE}.po/.mo + balans.pot (scripts: drukwerk/scripts/extract_strings.py, translations.py, build_translations.py). Textdomain herladen op pll_language_defined. JS-teksten via BalansData.i18n; AJAX-formulieren sturen lang mee.
- Logo's met slogan in 3 talen: drukwerk/logo/EQ5-logo-teal-met-slogan-{NL,EN,FR}.{svg,png,pdf}; theme assets/images/logo-eq5-slogan-{nl,en,fr}.svg.
- Getest in lokale WordPress (SQLite) + Polylang op /root/wptest (php -S 127.0.0.1:8099, wp-cli: php /root/wptest/wp-cli.phar --allow-root): alle pagina's 200 in 3 talen zonder PHP-waarschuwingen, browserdetectie (fr/en → 302), formulierfoutmeldingen per taal, thema werkt ook zonder Polylang. Niet visueel (geen screenshots op verzoek gebruiker).
- Backlog: WooCommerce-producten vertalen (Polylang for WooCommerce, betaald), juridische pagina's per taal, gebruiker controleert vertalingen (o.a. "Balansbrenger" → "Balance bringer"/"Porteur d'équilibre").
- (juni 2026) Thema v1.3.0: juridische pagina's (privacyverklaring, cookies, algemene-voorwaarden, webshopvoorwaarden, verzenden-retour, disclaimer) als conceptteksten in NL/EN/FR in inc/legal/{nl,en,fr}/*.html; worden eenmalig aangemaakt op admin_init (balans_install_legal, option balans_legal_installed), EN/FR via balans_install_translations met inhoud; nooit overschrijven; privacy-pagina WP + WooCommerce terms-pagina ingesteld. Placeholders "[in te vullen]" (ook in EN/FR, één zoekterm). Diensten: formulieren, WooCommerce, Polylang-cookie, betaalprovider (bijv. Mollie), podcastdienst. Getest lokaal: 18 pagina's 200, footer-links per taal correct. Concept — juridische controle aanbevolen.
- (juni 2026) Thema v1.4.0: Podcast. Plugins (gratis): Seriously Simple Podcasting + Paid Memberships Pro + PMPro Member RSS; later uitbreidbaar met Castos. inc/podcast.php: balans_is_member() (elk actief PMPro-niveau; filter balans_is_member), meta box "Alleen voor abonnees" (_eq5_members_only) op podcast + balans_meditatie, slot op single (the_content 999), openbare SSP-feed sluit abonnee-afleveringen uit (ssp_episode_query_args), feed per taal via ?lang= (default nl), feed-URL zonder taalprefix (/en/feed/podcast/<series>/ geeft 404), redirect_canonical uit voor ?lang/?memberkey, Customizer EQ5 Podcast (Spotify/Apple per taal), Polylang voor podcast/series. Template page-templates/page-podcast.php + template-parts/podcast-{episode,subscribe,membership}.php. Pagina slug 'podcasts' (NL; 'podcast' is gereserveerd door SSP-feed), EN 'listen', FR 'ecouter'; aangemaakt via balans_install_extra_pages (option balans_pages_version=2). Vertalingen nu dict-gebaseerd (translations.py: NL -> (EN, FR)), 251 strings. Getest lokaal met SSP; PMPro niet te activeren op SQLite -> lidmaatschap gesimuleerd via mu-plugin filter. LEESMIJ-PODCAST.txt.
- (juni 2026) Thema v1.5.0: Website-kleuren TERUG naar bruin/brons (gebruiker wilde nooit teal op de site); --balans-gold #B08D57 voor "naar balans" (hero em) en accenten. ALLEEN het logo blijft teal (#3C6E71; footer lichter #7FAAAD op donkere achtergrond). Header-logo = EQ5 + slogan in huidige taal (logo-eq5-slogan-{lang}.svg). www.eq5.nl uit footer. Diensten: teksten eigenaar (typo's verbeterd, zorgverzekeraar/medicijn-zinnen weggelaten, "genezend" -> "ondersteunend") in inc/content/services-{nl,en,fr}.php; inc/services.php (balans_services, balans_appointment_services, balans_service_block, balans_price_note). Abonnementen-pagina: Maandelijks, Wekelijks A/B/C. Nieuwe pagina 'afspraak-voor-hulp' (EN appointment-for-help, FR rendez-vous-aide; template page-hulp.php): Afstemming, Consult, Dubbel consult, Consult+drukpuntmassage, Maatwerk (-> contact). Prijs: [in te vullen] + prijsnotitie (pay it forward). Afspraakformulier: diensten uit services, voorselectie via ?dienst=. Menu "Hulp" + footer. BALANS_PAGES_VERSION=3. 247 strings vertaald.
- (juni 2026) Thema v1.6.0: Diensten bewerkbaar via CPT balans_dienst (menu "Diensten"; meta _dienst_group abonnement|hulp, _dienst_when, _dienst_price, _dienst_key, _dienst_level (PMPro), _dienst_cta afspraak|contact; volgorde = menu_order; Polylang vertaalbaar; prijs/niveau sync naar vertalingen). Eenmalige seed van 9 diensten x 3 talen op admin_init (option balans_diensten_seed; wacht op nl/en/fr als Polylang actief). Paginakoppen blijven in inc/content/services-{lang}.php; fallback naar die teksten zolang er geen berichten zijn. Prijsnotitie per taal via Customizer "EQ5 Prijsnotitie". Abonnementen online via PMPro: knop "Abonneer" -> pmpro_url('checkout') + pmpro_level; ingelogde houder ziet "Je hebt dit abonnement". balans_is_member() telt dienst-niveaus NIET mee (podcast/meditatie-toegang alleen via apart EQ5-niveau). Stripe (iDEAL/Bancontact + SEPA) handmatig in PMPro; level group "Energetische abonnementen" met meerdere niveaus toestaan — instructies in LEESMIJ-DIENSTEN-ABONNEMENTEN.txt. PMPro-checkout niet lokaal testbaar (SQLite), knop-URL getest met stub.
- (juni 2026) Thema v1.7.0 (inc/subscriptions.php): PMPro-pagina's meertalig (admin-melding "Abonnementspagina's vertalen" maakt EN/FR-kopieën met zelfde shortcode/blok-inhoud, gekoppeld via Polylang; $pmpro_pages/$pmpro_core_pages vertaald op init/pll_language_defined/wp). Welkomstmail (pmpro_after_change_membership_level, alleen dienst-niveaus; taal uit user meta eq5_lang gezet bij pmpro_after_checkout) met tijdsaanduiding + dienstekst. Herinneringen via WP-Cron 'balans_daily_reminders' dagelijks 08:00: zo=wekelijks-a, wo=wekelijks-b, vr=wekelijks-c (actieve leden uit pmpro_memberships_users); afmeldlink met HMAC-token (user meta eq5_no_reminders). Mailteksten NL/EN/FR in balans_mail_texts(). Getest lokaal met gesimuleerde PMPro-tabel/opties; echte PMPro/Stripe niet getest.

## Update juni 2026 — E-mailteksten, maandelijks rustmoment, reflectievraag (DONE)
- `inc/mail-settings.php`: admin-pagina "Balans → E-mailteksten" met tabbladen NL/EN/FR (option `balans_mail_texts`), plaatshouders {naam}/{abonnement}/{moment}, reset naar standaard.
- `inc/subscriptions.php`: welkomst-/herinneringsmails gebruiken bewerkbare teksten; maandelijks-abonnees krijgen op de 1e zaterdag van de maand een rustmoment-mail; elke herinnering bevat een wisselende reflectievraag (alleen in de mail, geen opslag).
- Getest via wp-cli (PMPro actief): 1e-zaterdag detectie, taal per abonnee, opgeslagen vs standaardteksten, welkomstmail.

## Backlog
- P1: Validatie WooCommerce-flow met Polylang + PMPro
- P1: Live-gang begeleiden (plugins installeren/instellen)
- P2: Losse verkoop meditaties/trainingen via WooCommerce + PMPro

## Update juni 2026 — Installatie-assistent plugins (DONE)
- `inc/plugins-setup.php`: "Balans → Installatie & plugins": installeert/activeert Polylang, SSP, WooCommerce (WordPress.org) en PMPro (officiële download https://license.paidmembershipspro.com/downloads/free/paid-memberships-pro.zip, PMPro is van WP.org verwijderd) + optioneel Simply Schedule Appointments (AVG-vriendelijke agenda).
- Knop "Alles instellen": talen nl/en/fr, thema-pagina's + vertalingen, diensten, PMPro-pagina's (3 talen), WooCommerce-pagina's (EUR). Idempotent.
- `page-afspraak.php`: toont [ssa_booking] als SSA actief is, anders eigen formulier.
- Getest op verse WP-install (/tmp/wpf) via wp-cli: alle 5 plugins echt gedownload en geactiveerd; setup gaf 95 pagina's, 27 diensten.
- Bekend: WooCommerce-pagina's alleen NL (vertalen vereist betaalde Polylang for WooCommerce).

## Update juni 2026 — Logo, menu's, Events, telefoonnummer (DONE)
- Logo: slogan in 2 regels onder EQ5 ("van vicieuze cirkel" / "naar balans", EN/FR idem), teal lichter #3F7F83 (site + /app/drukwerk/logo + favicon). Header 96px. Visitekaartje ongewijzigd (heeft nog telefoonnummer, wens gebruiker).
- Voor jou/home: Consult → "Hulp" (naar afspraak-voor-hulp), Drukpuntmassage + consult → "Event" (naar /events). Oude /consult en /drukpuntmassage-consult redirecten naar Hulp. Abonnementen-tekst: "Regelmatig afvoeren van verbruikte en vastgelopen emoties en energie uit je lichaam".
- Podcast uit header-fallback en footer verborgen (pagina bestaat nog).
- Telefoonnummer weg uit footer, welkomstmails, juridische pagina's (bestanden + DB-upgrade inc/upgrade.php, BALANS_CONTENT_VERSION=2). Nieuw: afspraakbevestiging naar klant met groot telefoonnummer (option balans_phone, teksten appt_* in Balans → E-mailteksten).
- Diensten: Maatwerk → Familie-pakket (nieuwe tekst NL/EN/FR), Dubbel consult "Maaike en Remco". DB-upgrade past bestaande diensten aan als ze niet zelf gewijzigd zijn.
- Events: CPT balans_event (inc/events.php, single-balans_event.php, page-templates/page-events.php), pagina "Events" met bewerkbare intro + thema's (inc/content/pages/{lang}/events.html), komende events, aanmelden via link of contactformulier (prefill).
- Getest op verse WP in /tmp/wpf (php -S + curl, wp-cli).

## Update juni 2026 — Event-tickets, event-herinnering, agenda-huisstijl (DONE)
- `inc/event-tickets.php`: per event (prijs + max plaatsen) automatisch verborgen virtueel WooCommerce-product (voorraad = plaatsen, gedeeld door vertalingen, capaciteitswijziging past voorraad aan). Max 4 tickets per bestelling (validatie + clamp + Store API), add-to-cart → direct naar checkout. Taal koper in order-meta `_balans_lang`.
- `template-parts/event-tickets.php`: aantal (1-4) + "Ticket kopen", "Nog X plaatsen" (≤10), "Uitverkocht"; overzicht toont "· Uitverkocht".
- Herinnering dag van tevoren via bestaande cron `balans_daily_reminders`: datum/tijd/locatie + OpenStreetMap-link + tickets + voorbereiding; teksten event_* per taal in Balans → E-mailteksten; 1x per order (`_balans_reminded_{pid}`).
- Agenda: `ssa/booking-app/custom.css` (lokale fonts, teal/crème, ronde hoeken), shortcode-params + setup-stap "Agenda in huisstijl" (geen Google Fonts).
- WooCommerce prijsnotatie € 45,00. Upgrade v3 zet oude vrije prijstekst om naar getal.
- Getest op /tmp/wpf (wp-cli + php -S/curl).
- Later (gebruiker): eerstvolgend event op de homepage.
