#!/bin/bash
# Regression: header fallback menu must not contain Hulp/Help/Aide (Balans theme)
WP="php /root/wptest/wp-cli.phar --allow-root --path=/tmp/wpf/wordpress"
B=http://localhost:8099
PASS=0; FAIL=0
ok(){ echo "PASS: $1"; PASS=$((PASS+1)); }
ko(){ echo "FAIL: $1"; FAIL=$((FAIL+1)); }
nav(){ curl -sL "$1" | tr '\n' ' ' | grep -o '<nav class="primary-nav" id="primary-navigation".*</nav>' | sed 's#</nav>.*#</nav>#'; }

php -l /app/balans-theme/header.php >/dev/null 2>&1 && ok "php -l header.php" || ko "php -l header.php"

for L in fr en; do
  N=$(nav "$B/$L/")
  echo "--- $L nav links:"; echo "$N" | grep -o '<a [^>]*>[^<]*' | sed 's/<a [^>]*href="\([^"]*\)"[^>]*>\s*/\1 => /'
  [ -n "$N" ] && ok "$L nav present" || ko "$L nav present"
  echo "$N" | grep -qiE '>\s*(Hulp|Help|Aide)\s*<' && ko "$L no Hulp/Help/Aide" || ok "$L no Hulp/Help/Aide"
  echo "$N" | grep -q 'header-cta-afspraak' && ok "$L CTA present" || ko "$L CTA present"
  C=$(echo "$N" | grep -o '<li>' | wc -l); [ "$C" -eq 5 ] && ok "$L 5 fallback items" || ko "$L 5 fallback items (got $C)"
done

# NL with menu assigned
N=$(nav "$B/"); echo "--- nl (menu assigned) links:"; echo "$N" | grep -o '<a [^>]*>[^<]*' | sed 's/<a [^>]*href="\([^"]*\)"[^>]*>\s*/\1 => /'
echo "$N" | grep -q 'menu-item' && ok "nl renders wp_nav_menu" || ko "nl renders wp_nav_menu"

# NL fallback
ORIG=$($WP eval 'echo json_encode(get_theme_mod("nav_menu_locations"));' 2>/dev/null | tail -1)
echo "orig locations: $ORIG"
$WP eval '$l=get_theme_mod("nav_menu_locations"); unset($l["primary"]); set_theme_mod("nav_menu_locations",$l);' >/dev/null 2>&1
N=$(nav "$B/"); echo "--- nl fallback links:"; echo "$N" | grep -o '<a [^>]*>[^<]*' | sed 's/<a [^>]*href="\([^"]*\)"[^>]*>\s*/\1 => /'
echo "$N" | grep -q 'menu-item' && ko "nl fallback active" || ok "nl fallback active"
echo "$N" | grep -qiE '>\s*(Hulp|Help|Aide)\s*<' && ko "nl fallback no Hulp" || ok "nl fallback no Hulp"
echo "$N" | grep -q 'header-cta-afspraak' && ok "nl CTA present" || ko "nl CTA present"
$WP eval '$l=get_theme_mod("nav_menu_locations"); if(!is_array($l))$l=array(); $l["primary"]=2; set_theme_mod("nav_menu_locations",$l);' >/dev/null 2>&1
NEW=$($WP eval 'echo json_encode(get_theme_mod("nav_menu_locations"));' 2>/dev/null | tail -1)
echo "restored locations: $NEW"
[ "$(echo $NEW | grep -c '"primary":2')" -eq 1 ] && ok "restored primary=>2" || ko "restored primary=>2"

for P in / /events/ /voor-jou/ /afspraak-voor-hulp/ /fr/ /en/; do
  S=$(curl -sL -o /tmp/pg.html -w "%{http_code}" "$B$P")
  [ "$S" = 200 ] && ok "$P 200" || ko "$P status $S"
  grep -qiE 'Fatal error|Parse error' /tmp/pg.html && ko "$P fatal in body" || true
done

Z=$(unzip -p /app/balans-theme.zip balans-theme/header.php | grep -c "esc_html__( 'Hulp'")
[ "$Z" -eq 0 ] && ok "zip header.php no Hulp" || ko "zip still has Hulp"
diff <(unzip -p /app/balans-theme.zip balans-theme/header.php) /app/balans-theme/header.php >/dev/null && ok "zip header.php identical" || ko "zip header.php differs"

echo "RESULT: $PASS passed, $FAIL failed"
