# AVC PHP Platform Blueprint

## Osnovna odluka

Preporuka je:

- **ne ostati na WordPressu**
- **ne raditi "copy paste" postojece teme u cisti PHP**
- **ne raditi SPA / JS-first frontend**
- **raditi novu server-rendered PHP platformu**
- **zadrzati sav vrijedan sadrzaj, URL-ove, slike, SEO signale i jezicne varijante**
- **preslikati provjerene FCC mehanizme za referral, geo routing, AI pomocnika i analytics**

To znaci:

- **kod baze i aplikacije radimo novu platformu**
- **sadrzaj i SEO kapital migriramo iz postojeceg WordPressa**

To je najbolji omjer izmedu:

- potpune kontrole
- brzine
- indeksacije
- buduceg razvoja
- cuvanja postojeceg rankinga

## Sto vec znamo iz lokalnog restorea

Trenutno stanje aloevera-centar.com:

- oko **904** objavljena blog clanka
- oko **171** objavljen proizvod
- oko **35** objavljenih stranica
- jezici: **hr**, **en**, **sl**
- aktivni pluginovi ukljucuju **WooCommerce**, **Yoast SEO Premium**, **WPML**, **Permalink Manager**
- permalink struktura nije potpuno default, jer postoji opcija **`permalink-manager-uris`**
- Yoast meta podaci postoje i u **postmeta** i u **Yoast indexable** tablicama

To znaci da novi sustav mora cuvati:

- postojece slugove i custom URI-jeve
- visejezicne varijante
- canonical i hreflang logiku
- title i meta description podatke
- interne linkove i slike

## Sto se moze iskoristiti iz WordPressa

Iz WordPressa zadrzavamo i migriramo:

- clanke
- stranice
- proizvode kao informativne / recommendation stranice
- slike i medije
- kategorije i hijerarhiju
- Yoast meta podatke
- postojece canonical i OG signale
- WPML jezicne veze
- custom URL mapu iz `permalink-manager-uris`

Iz WordPressa **ne zadrzavamo**:

- temu
- plugin runtime
- WooCommerce checkout
- Avada builder strukturu kao finalni render sloj

## Sto se koristi iz FCC projekta

Iz `forevercard.club` treba preuzeti logiku, ali ne cijeli projekt 1:1.

Kljucevi za reuse:

- **geo detekcija zemlje**
- **mapiranje drzava na Forever market**
- **generiranje pravog Forever webshop URL-a**
- **dodavanje referral parametra / Forever ID-a**
- **discount query parametri ako postoje**
- **blog -> tracked outbound redirect**
- **country / city / source analytics**
- **AI savjetnik za proizvode**
- **blog + shop kontekst i recommendation engine**

Relevantni FCC dijelovi koje trebamo preslikati kao engine:

- `/Users/stjepanbelosa/Documents/product/app/controllers/BlogClick.php`
- `/Users/stjepanbelosa/Documents/product/app/helpers/Link.php`
- `/Users/stjepanbelosa/Documents/product/app/helpers/automations.php`
- `/Users/stjepanbelosa/Documents/product/app/helpers/fcc_ai.php`
- `/Users/stjepanbelosa/Documents/product/app/includes/GeoLite2-Country.mmdb`
- `/Users/stjepanbelosa/Documents/product/docs/vip-funnel-product-recommendation-block-strategy-2026-04-20.md`

## Vazna poslovna odluka

Nova stranica ne smije glumiti da je glavni merchant checkout.

Kupnja ostaje na sluzbenom Forever shopu.

Zato AVC treba biti:

- edukativna platforma
- recommendation platforma
- AI assisted advisory platforma
- trust platforma
- multilingual SEO content platforma
- referral bridge prema sluzbenom Forever checkoutu

Drugim rijecima:

- **AVC prodaje kroz edukaciju, povjerenje, personalizaciju i referral**
- **Forever odraduje checkout**

## SEO i AI discoverability pravila

### Google

Google trazi minimum:

- da Googlebot nije blokiran
- da stranica vraca HTTP `200`
- da stranica ima indexable content

Glavni fokus mora biti:

- people-first sadrzaj
- crawlable interni linkovi
- title i H1 koji odgovaraju stvarnom intentu
- server-rendered HTML
- stabilni canonical i hreflang signali
- article i breadcrumb schema

### ChatGPT / OpenAI

Nema nacina da se garantira top placement u ChatGPT Search.

Ali za ukljucenje i discoverability treba:

- dopustiti **OAI-SearchBot**
- ne blokirati OpenAI IP-e gdje je potrebno
- imati jasan, citljiv, dobro strukturiran HTML
- imati dobar source content koji se moze lako citirati i linkati

Vazna razlika:

- **OAI-SearchBot** je za Search discoverability
- **GPTBot** je odvojen i odnosi se na treniranje modela

Mozemo dopustiti OAI-SearchBot, a ne dopustiti GPTBot ako to zelis.

## Vazna schema napomena za produkt stranice

Zato sto kupnja ne zavrsava na AVC domeni, **ne trebamo graditi merchant listing logiku kao da smo checkout merchant**.

Pravila:

- produktne stranice na AVC-u trebaju biti **recommendation / review / guide pages**
- trebaju imati **Product**, **FAQ**, **Breadcrumb**, po potrebi **Article**
- ne trebamo ih pozicionirati kao stranice gdje korisnik kupuje direktno na nasoj domeni

To je vazno i zbog Google compliance strane.

## Preporucena ciljna arhitektura

### Tehnologija

Preporuka:

- PHP 8.3+
- MariaDB
- server-side rendered templates
- mali custom MVC ili lagani modularni framework pristup
- minimalan JS, bez ovisnosti o frontend SPA modelu
- image optimization pipeline
- Redis ili file cache po potrebi

### Predlozena struktura

- `public/`
- `app/Controllers/`
- `app/Models/`
- `app/Services/`
- `app/Seo/`
- `app/Referral/`
- `app/Geo/`
- `app/AI/`
- `app/Content/`
- `app/Import/`
- `templates/`
- `storage/`
- `database/migrations/`

### Kljuvni moduli

1. **Routing module**
2. **Content module**
3. **SEO module**
4. **Multilingual module**
5. **Referral engine**
6. **Geo-market resolver**
7. **AI advisor module**
8. **Analytics module**
9. **Admin/editor module**
10. **Import/migration module**

## URL strategija

Ovo je neupitno pravilo:

- **postojeci URL-ovi clanaka i proizvoda ostaju isti**

Kako to izvedemo:

1. Izvucemo sve URL-ove iz:
   - sitemapova
   - `permalink-manager-uris`
   - WordPress tablica
2. Napravimo centralnu `routes` tablicu u novom sustavu.
3. Svaki URL mapiramo na:
   - content type
   - language
   - content id
   - canonical url
4. Ako neki URL mora promijeniti oblik:
   - radimo tocni `301`
   - nikad genericki redirect pattern bez validacije

## Multilingual strategija

Novi sustav mora imati:

- hrvatski
- engleski
- slovenski

Po stranici trebamo cuvati:

- language code
- translation group id
- localized slug
- localized title
- localized meta description
- localized structured data
- hreflang mapu

Preporuka:

- svaki jezik ima svoju URL varijantu
- svaka varijanta u `<head>` ima kompletnu `hreflang` grupu
- ukljuciti i `x-default` za globalnu discovery logiku ako bude imalo smisla

## Sadrzajni model

### Tipovi sadrzaja

1. **Blog article**
2. **Product guide**
3. **Category / intent landing page**
4. **Comparison page**
5. **Country / market guide**
6. **Brand / trust pages**
7. **AI advisor entry pages**
8. **Support / FAQ pages**

### Novi "money pages" model

Za Forever proizvode preporuka je da ne postoji samo jedan tip produkt stranice.

Treba imati:

- **product guide page**
  - sto je proizvod
  - kome moze biti relevantan
  - kako se razlikuje
  - FAQ
  - CTA prema sluzbenom shopu

- **problem-intent landing page**
  - npr. probava, koza, energija, imunitet, njega, oral care
  - vodi na vise relevantnih proizvoda i clanke

- **comparison page**
  - npr. `aloe-vera-gel-vs-berry-nectar`
  - vrlo dobro radi i za Google i za AI chatove

- **country-specific trust page**
  - kako kupiti u odredenoj zemlji
  - koji popust korisnik dobiva
  - kako radi preporuka i podrska

## Interni linking model

Interni linking mora biti sustavan, ne rucni kaos.

Svaki clanak treba imati:

- 2-4 relevantna linka prema drugim clancima
- 1-3 linka prema product guide stranicama
- 1 CTA prema odgovarajucem Forever shop redirectu
- 1 link prema AI savjetniku

Svaka product guide stranica treba imati:

- link prema kategoriji / intent hubu
- link prema 2-3 srodna proizvoda
- link prema 2-3 blog clanka
- FAQ anchor linking
- country-aware CTA

## AI-savjetnik za proizvode

AI savjetnik treba biti integriran kao core feature, ne kao dodatak sa strane.

Predlozeni model:

- ulaz s product i category stranica
- zna jezik stranice
- zna zemlju korisnika
- zna kontekst stranice s koje je otvoren
- daje informativne preporuke
- ne daje medicinske tvrdnje
- vodi prema:
  - product guide
  - usporedbi proizvoda
  - official Forever shop redirectu
  - kontaktu / savjetovanju

AI savjetnik treba koristiti:

- product knowledge base
- category-intent mapu
- contraindication / claim guardrails
- lokalizirane odgovore

## Geo + referral engine

Ovo je jedna od glavnih prednosti koju treba graditi odmah.

Flow:

1. Posjetitelj dode na AVC URL.
2. Sustav procijeni jezik i drzavu.
3. Sustav odredi najbolji Forever market.
4. Ako postoji referral owner, ukljuci referral parametar.
5. Ako postoji discount logika, ukljuci discount parametre.
6. Klik prema shopu ide preko kontroliranog redirect controllera.
7. Biljeze se analytics:
   - drzava
   - grad
   - browser language
   - source
   - utm
   - product id
   - language
   - referral owner

Ovo treba preslikati iz FCC-a u zaseban AVC engine.

## Analytics koje treba imati od prvog dana

Minimalni dashboard:

- unique page visits
- unique outbound shop clicks
- CTR po stranici
- CTR po proizvodu
- CTR po drzavi
- CTR po jeziku
- top landing pages
- top articles
- top products
- top countries
- top cities
- source / medium / campaign

Dodatno:

- heatmap CTA performansi
- internal link click data
- AI advisor starts
- AI advisor -> shop click conversion
- article -> product -> shop conversion path

## SEO / AEO content pravila

Svaki clanak treba imati:

- jedan jasan H1
- uvod koji brzo odgovara na intent
- sadrzaj podijeljen u smislene H2/H3 cjeline
- FAQ gdje ima smisla
- sazetak / key takeaways
- related products
- related articles
- author / editorial trust signal
- datum azuriranja

Svaki clanak mora biti napisan tako da:

- Google lako razumije temu
- AI crawler lako razumije i citira odgovor
- korisnik brzo dobije konkretan zakljucak

To znaci:

- manje builder buke
- manje tankog teksta
- manje generickog SEO punjenja
- vise jasnih, direktnih, strukturiranih odgovora

## Sto ne preporucujem

- ne preporucujem rebuild u React SPA
- ne preporucujem headless WordPress kao trajno rjesenje
- ne preporucujem ostaviti WooCommerce samo za "showroom"
- ne preporucujem rucno prenositi sadrzaj bez import pipelinea
- ne preporucujem masovni redizajn URL-ova
- ne preporucujem prikazivati AVC kao merchant checkout ako checkout ostaje na Foreveru

## Preporuceni delivery plan

### Faza 1: Discovery i mapping

- izvesti puni URL inventory
- izvuci permalink mapu
- izvuci Yoast meta podatke
- izvuci WPML translation grupe
- kategorizirati clanke po intentu
- kategorizirati proizvode po problemima / ciljevima
- oznaciti top organic landing pages

### Faza 2: Nova arhitektura i baza

- dizajn nove baze
- route table
- content table
- translation table
- seo table
- product recommendation table
- analytics table
- outbound click tracking tablice

### Faza 3: FCC engine extraction

- izdvojiti geo market resolver
- izdvojiti referral parameter builder
- izdvojiti official shop redirect controller
- izdvojiti click analytics
- izdvojiti AI advisor integracijski sloj

### Faza 4: Frontend design system

- premium vizualni smjer
- trust-first dizajn
- product card sistem
- article template
- product guide template
- category landing template
- comparison template

### Faza 5: Import pipeline

- import clanaka
- import proizvoda
- import slika
- import Yoast meta
- import hreflang odnosa
- import URL mape
- import internal link signala gdje je moguce

### Faza 6: SEO / schema sloj

- canonical
- hreflang
- article schema
- product schema
- breadcrumb schema
- FAQ schema
- sitemap generation
- robots.txt
- Open Graph i Twitter metadata

### Faza 7: QA i staging

- diff URL-ova
- diff meta podataka
- diff hreflang odnosa
- crawl staginga
- provjera structured data
- provjera Core Web Vitals
- provjera redirect fallbackova

### Faza 8: Soft launch i cutover

- deploy na staging
- final crawl
- Search Console setup
- analytics setup
- produkcijsko prebacivanje
- pracenje 404 / 301 / indeksacije

## Sto radimo od nule, a sto ne

### Od nule radimo

- novu PHP aplikaciju
- novu bazu podataka
- novi template system
- novi admin/editor sustav
- novu analytics arhitekturu
- novu AI advisor integraciju
- novu referral / redirect implementaciju za AVC

### Ne radimo od nule

- sadrzaj
- SEO kapital
- prijevode koji vec postoje
- URL strukturu
- logiku geo + referral + outbound click enginea jer je to vec dokazano u FCC-u

## Preporuka za prvi MVP koji vec moze donositi rezultat

Najbrzi jaki MVP:

1. blog clanci ostaju na istim URL-ovima
2. product guide stranice ostaju na istim URL-ovima
3. category / intent hubovi se grade prioritetno
4. shop CTA ide preko AVC redirect controllera
5. AI advisor radi na top landing i product stranicama
6. analytics biljezi shop klikove i zemlje

To daje:

- zadrzavanje SEO-a
- bolji UX
- referral engine
- globalni routing
- brzi put do mjerenja rezultata

## Najveci rizici

1. Ako promijenimo URL-ove bez potpune mape, izgubit cemo dio indexacije.
2. Ako izgubimo hreflang veze, past ce international discoverability.
3. Ako produkt stranice semanticki gradimo kao merchant checkout, riskiramo krivi structured data model.
4. Ako AI-savjetnik nema guardrails, riskiramo lose medicinske tvrdnje.
5. Ako referral engine nije centraliziran, nastat ce kaos u tracking-u.

## Sto trebamo napraviti odmah

1. Napraviti detaljan content export iz WordPress baze.
2. Izvuci `permalink-manager-uris`.
3. Izvuci Yoast SEO metadata mapu.
4. Izvuci WPML translation relationships.
5. Pregledati FCC engine i odluciti sto ide u shared modul.
6. Odabrati finalnu arhitekturu nove PHP aplikacije.

## Preporucena izvedbena odluka

**Najbolji smjer je: nova custom PHP platforma + migracija sadrzaja i SEO signala + preslikani FCC referral/geo/AI engine.**

To nije "sve iz pocetka" u destruktivnom smislu.

To je:

- **novi proizvod**
- izgraden na
- **postojecem content kapitalu**
- i
- **postojecim provjerenim FCC mehanizmima**

## Otvorena pitanja prije build faze

1. Zelis li da AVC ima vlastiti admin za pisanje clanaka ili da sadrzaj inicijalno punimo import skriptama i uredjujemo kroz jednostavniji interni editor?
2. Zelis li da AI savjetnik bude isti engine kao na FCC-u ili prilagodena varijanta samo za proizvode i clanke?
3. Zelis li da referral bude vezan samo uz tvoj Forever ID ili da AVC kasnije podrzava vise partner profila kao i FCC?
4. Zelis li da English bude glavni globalni layer, a HR/SL regionalni, ili da HR ostane primarni jezik domene?
5. Zelis li samo informativne product guide stranice ili i dodatne comparison / quiz / recommendation landing pageove od starta?
