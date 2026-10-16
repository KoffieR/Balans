#!/bin/bash
# Regression: Balans editable page texts (hero meta, body blocks, migration, homepage customizer, logo, no placeholders)
WP="php /root/wptest/wp-cli.phar --allow-root --path=/root/wpf/wordpress --user=admin"
B=http://localhost:8099
PASS=0; FAIL=0
ok(){ echo "PASS: $1"; PASS=$((PASS+1)); }
ko(){ echo "FAIL: $1"; FAIL=$((FAIL+1)); }
wpe(){ $WP eval "$1" 2>/dev/null | grep -v -i 'CONVERT_TZ\|WordPress database error' | tail -1; }
h1(){ curl -sL "$1" | tr '\n' ' ' | grep -o '<h1[^>]*>.*</h1>' | head -1 | sed 's#</h1>.*#</h1>#'; }
LOGSTART=$(wc -c < /tmp/phps.log)

# --- php lint
E=0; for f in $(find /app/balans-theme -name '*.php'); do php -l "$f" >/dev/null 2>&1 || { E=1; echo "lint error $f"; }; done
[ $E -eq 0 ] && ok "php -l all theme files" || ko "php -l"

# --- zip contents
unzip -l /app/balans-theme.zip | grep -q 'inc/page-texts.php' && ok "zip has page-texts.php" || ko "zip page-texts.php"
unzip -l /app/balans-theme.zip | grep -q 'LEESMIJ-TEKSTEN-AANPASSEN.txt' && ok "zip has LEESMIJ" || ko "zip LEESMIJ"
# zip in sync with source
TMPZ=$(mktemp -d); unzip -q /app/balans-theme.zip -d $TMPZ
ZR=$(find $TMPZ -name page-texts.php | head -1)
diff -q "$ZR" /app/balans-theme/inc/page-texts.php >/dev/null && ok "zip page-texts.php in sync" || ko "zip page-texts.php differs from source"
ZD=$(dirname $(dirname "$ZR")); D=$(diff -rq "$ZD" /app/balans-theme 2>&1 | grep -v '\.git' | head -5); [ -z "$D" ] && ok "zip fully in sync" || { echo "$D"; ko "zip not fully in sync"; }
rm -rf $TMPZ

# --- hero meta per language
IDS=$(wpe '$p=get_page_by_path("over"); echo $p->ID.",".pll_get_post($p->ID,"en").",".pll_get_post($p->ID,"fr");')
NL=${IDS%%,*}; R=${IDS#*,}; EN=${R%%,*}; FR=${R#*,}
echo "over ids nl=$NL en=$EN fr=$FR"
URL_EN=$(wpe "echo get_permalink($EN);"); URL_FR=$(wpe "echo get_permalink($FR);")
echo "en=$URL_EN fr=$URL_FR"
ORIGT=$(wpe "echo get_post_meta($NL,'_balans_hero_title',true);")
echo "orig NL title: $ORIGT"
h1 "$B/over/" | grep -q "Over ons, de oprichter en onze aanpak" && ok "NL over h1 default" || ko "NL over h1 default: $(h1 $B/over/)"
wpe "update_post_meta($NL,'_balans_hero_title','TEST_Kop Over');" >/dev/null
h1 "$B/over/" | grep -q "TEST_Kop Over" && ok "NL h1 follows meta" || ko "NL h1 follows meta: $(h1 $B/over/)"
h1 "$URL_EN" | grep -q "About us, the founder and our approach." && ok "EN h1 unchanged" || ko "EN h1: $(h1 $URL_EN)"
h1 "$URL_FR" | grep -q "propos de nous, du fondateur et de notre approche." && ok "FR h1 unchanged" || ko "FR h1: $(h1 $URL_FR)"
wpe "update_post_meta($NL,'_balans_hero_title',wp_slash('$ORIGT'));" >/dev/null
h1 "$B/over/" | grep -q "Over ons, de oprichter" && ok "NL h1 restored" || ko "NL h1 restored"
# eyebrow/intro
curl -sL "$B/over/" | grep -q 'page-hero__intro' && ok "NL intro rendered" || ko "NL intro rendered"

# --- body content
OV=$(curl -sL "$B/over/")
for t in "De organisatie" "De oprichter" "Onze aanpak &amp; empathisch energiewerk" "Partners &amp; samenwerking"; do
  echo "$OV" | grep -q "$t" && ok "over body: $t" || ko "over body: $t"; done
echo "$OV" | grep -q 'data-testid="over-content"' && ok "over-content wrapper" || ko "over-content wrapper"
C=$(echo "$OV" | grep -o 'De oprichter</h2>' | wc -l); [ "$C" -eq 1 ] && ok "no duplicate 'De oprichter' heading" || ko "dup oprichter count=$C"
EOV=$(curl -sL "$URL_EN"); echo "$EOV" | grep -q "wp-block-columns" && ! echo "$EOV" | grep -q "De organisatie" && ok "EN body translated" || ko "EN body translated"
echo "$EOV" | grep -o '<h2 class="wp-block-heading">[^<]*' | sed 's/.*>/  EN h2: /'
FOV=$(curl -sL "$URL_FR"); echo "$FOV" | grep -q "wp-block-columns" && ! echo "$FOV" | grep -q "De organisatie" && ok "FR body translated" || ko "FR body translated"
echo "$FOV" | grep -o '<h2 class="wp-block-heading">[^<]*' | sed 's/.*>/  FR h2: /'
# edit content
wpe "\$p=get_post($NL); update_post_meta($NL,'_test_backup',wp_slash(\$p->post_content)); wp_update_post(array('ID'=>$NL,'post_content'=>wp_slash(str_replace('De organisatie','TEST_Organisatie',\$p->post_content))));" >/dev/null
curl -sL "$B/over/" | grep -q "TEST_Organisatie" && ok "content edit rendered" || ko "content edit rendered"
curl -sL "$B/over/" | grep -q "De organisatie" && ko "hard-coded 'De organisatie' still present" || ok "no hard-coded duplicate text"
wpe "wp_update_post(array('ID'=>$NL,'post_content'=>wp_slash(get_post_meta($NL,'_test_backup',true)))); delete_post_meta($NL,'_test_backup');" >/dev/null
curl -sL "$B/over/" | grep -q "De organisatie" && ok "content restored" || ko "content restored"

CT=$(curl -sL "$B/contact/"); echo "$CT" | grep -q 'data-testid="contact-content"' && echo "$CT" | grep -q 'Adres:' && ok "contact-content with address example" || ko "contact-content"
ZK=$(curl -sL "$B/voor-bedrijven/"); echo "$ZK" | grep -q 'data-testid="zakelijk-content"' && echo "$ZK" | grep -q 'Twee routes' && ok "zakelijk-content Twee routes" || ko "zakelijk-content"
C=$(echo "$ZK" | grep -o 'Twee routes' | wc -l); [ "$C" -eq 1 ] && ok "Twee routes once" || ko "Twee routes count=$C"

# --- migration idempotent
B1=$(wpe "echo substr_count(get_post($NL)->post_content,'<!-- wp:columns');")
wpe 'balans_migrate_page_texts(); balans_migrate_page_texts();' >/dev/null
B2=$(wpe "echo substr_count(get_post($NL)->post_content,'<!-- wp:columns');")
[ "$B1" = "$B2" ] && [ "$B1" = "1" ] && ok "migration idempotent (columns=$B2)" || ko "migration idempotent ($B1 -> $B2)"
M=$(wpe '$n=0;$t=0;foreach(get_posts(array("post_type"=>"page","numberposts"=>-1,"lang"=>"","meta_key"=>"_wp_page_template","meta_value"=>"page-templates/","meta_compare"=>"LIKE")) as $p){$t++; if(get_post_meta($p->ID,"_balans_texts_migrated",true))$n++;} echo "$n/$t";')
[ "${M%/*}" = "${M#*/}" ] && ok "all template pages migrated ($M)" || ko "migrated $M"

# --- no placeholders for anonymous
for p in / /over/ /voor-jou/ /voor-bedrijven/ /contact/ /afspraak-maken/ /afspraak-voor-hulp/ /abonnementen/ /meditatie/ /trainingen-workshops/ /artikelen-bronnen/ /events/ /podcasts/ /en/ /fr/; do
  R=$(curl -sL -w '\n%{http_code}' "$B$p"); CODE=$(echo "$R" | tail -1)
  if echo "$R" | grep -qi 'aan te leveren\|balans-placeholder-notice'; then ko "$p placeholder ($CODE)"; else ok "$p clean ($CODE)"; fi
  [ "$CODE" = "200" ] || ko "$p status $CODE"
done

# --- homepage customizer
wpe 'set_theme_mod("eq5_hp_hero_title_nl","Mijn eigen kop");' >/dev/null
h1 "$B/" | grep -q "Mijn eigen kop" && ok "NL hp h1 from mod" || ko "NL hp h1: $(h1 $B/)"
wpe 'remove_theme_mod("eq5_hp_hero_title_nl");' >/dev/null
h1 "$B/" | grep -q "Van vicieuze cirkel <em>naar balans.</em>" && ok "NL hp h1 default after removing mod" || ko "NL hp default: $(h1 $B/)"
E1=$(h1 "$B/en/"); echo "$E1" | grep -q "From vicious cycle" && echo "$E1" | grep -q "<em>to balance.</em>" && ok "EN hp h1 default" || ko "EN hp h1: $E1"
wpe 'set_theme_mod("eq5_hp_routes_title_fr","TEST_Routes FR");' >/dev/null
curl -sL "$B/fr/" | grep -q "<h2>TEST_Routes FR</h2>" && ok "FR routes title from mod" || ko "FR routes title"
curl -sL "$B/en/" | grep -q "TEST_Routes FR" && ko "FR mod leaks into EN" || ok "FR mod not on EN"
wpe 'remove_theme_mod("eq5_hp_routes_title_fr");' >/dev/null
CR=$(wpe 'require_once ABSPATH."wp-includes/class-wp-customize-manager.php"; $m=new WP_Customize_Manager(); $GLOBALS["wp_customize"]=$m; do_action("customize_register",$m); $o=array(); $o[]=$m->get_panel("eq5_homepage")?"panel":"nopanel"; foreach(array("nl","en","fr","image") as $s) $o[]=$m->get_section("eq5_hp_".$s)?$s:"no-".$s; $c=$m->get_control("eq5_hp_hero_title_en"); $o[]=$c?$c->input_attrs["placeholder"]:"nocontrol"; echo implode("|",$o);')
echo "customizer: $CR"
echo "$CR" | grep -q '^panel|nl|en|fr|image|' && ok "customizer panel+sections" || ko "customizer: $CR"

# --- header logo
HD=$(curl -sL "$B/")
wpe 'echo "custom_logo=".get_theme_mod("custom_logo");'
echo "$HD" | grep -q '<a class="site-logo"' && ok "inline svg logo link" || ko "site-logo link"
echo "$HD" | tr '\n' ' ' | grep -o '<a class="site-logo".\{0,600\}' | grep -q '<svg' && ok "logo is svg" || ko "logo svg"
echo "$HD" | grep -q 'custom-logo' && ko "img.custom-logo present" || ok "no custom-logo img"
CSS=/app/balans-theme/assets/css/main.css
grep -q -- '--balans-logo: *#3F7F83' $CSS && ok "--balans-logo teal" || ko "--balans-logo"
grep -n 'site-branding\|site-logo__svg' $CSS | head
VER=$(echo "$HD" | grep -o 'main\.css?ver=[^"'"'"']*' | head -1); echo "$VER"
echo "$VER" | grep -qE 'ver=2\.0\.0\.[0-9]{9,}' && ok "cache-busting ver" || ko "ver $VER"

# --- php log
NEW=$(tail -c +$((LOGSTART+1)) /tmp/phps.log | grep -iE 'PHP (Warning|Fatal|Notice|Deprecated|Parse)' | grep -v CONVERT_TZ)
[ -z "$NEW" ] && ok "no PHP warnings in server log" || { echo "$NEW" | head; ko "PHP warnings in log"; }

echo "TOTAL PASS=$PASS FAIL=$FAIL"
