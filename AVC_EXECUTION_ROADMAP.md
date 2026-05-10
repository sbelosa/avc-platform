# AVC Execution Roadmap

## Zakljucane odluke

Na temelju tvojih odgovora, AVC ide u ovom smjeru:

1. **Referral model**
   - sustav je primarno tvoj
   - u adminu mora postojati **globalni aktivni Forever ID**
   - promjena tog ID-a mora se **live reflektirati** na sve referral preporuke na stranici
   - kasnije mozemo dodati:
     - per-market override
     - campaign override
     - vremenski zakazane promjene ID-a

2. **Jezici**
   - **hrvatski je primarni jezik domene**
   - engleski i slovenski moraju imati **punu indeksacijsku vrijednost**
   - to znaci:
     - zasebne jezicne URL-ove
     - potpune hreflang veze
     - zasebne sitemapove po jeziku
     - localized SEO metadata

3. **Admin sustav**
   - AVC mora imati vlastiti admin
   - admin mora upravljati:
     - clancima
     - proizvodnim recommendation stranicama
     - SEO naslovima i opisima
     - canonical i hreflang signalima
     - internim linkingom
     - referral postavkama
     - statistikama i outbound klikovima
     - AI leadovima
   - Google Ads / Search Console / rank tracking integracije mogu ici u **Fazu 2**

4. **AI savjetnik**
   - engine treba biti isti kao na FCC-u
   - ali odgovorima i promptovima mora biti prilagoden AVC cilju:
     - preporuka Forever proizvoda
     - podrska kupcima
     - HR / EN / SL
     - prikupljanje leadova
     - vodjenje korisnika prema:
       - clancima
       - product guide stranicama
       - official Forever shop linku s referralom

5. **Sadrzaj**
   - postojeci clanci se ne smiju samo preseliti
   - trebaju se **preurediti i unaprijediti**
   - cilj je:
     - bolja indeksacija
     - bolja AI citljivost
     - bolji interni linking
     - bolja konverzija prema proizvodima i outbound shop klikovima

6. **Glavna poslovna poruka**
   - AVC mora postati:
     - centralna platforma za podrsku korisnicima Forever proizvoda
     - centralna recommendation platforma
     - mjesto gdje kupci saznaju kako ostvariti **15% popusta**
   - to mora biti jasno kroz:
     - home page
     - product guide stranice
     - AI savjetnika
     - FAQ / trust sekcije
     - country-aware CTA-ove

## Finalna preporuka: sto gradimo

Gradimo:

- **novu custom PHP platformu**
- **novi admin**
- **novi analytics sloj**
- **novi AI support / recommendation sloj**
- **preslikani FCC referral + geo + redirect engine**
- **migrirani i unaprijedeni AVC sadrzaj**

Ne gradimo:

- novi WordPress
- headless WordPress
- WooCommerce checkout
- frontend koji ovisi o teskom JS SPA renderingu

## Kljuvni proizvodni stupovi

### 1. Trust + Support platform

AVC mora na svakoj vaznoj stranici jasno pokazivati:

- da korisnik dobiva pomoc pri odabiru
- da postoji AI savjetnik
- da postoji podrska i edukacija
- da kupnja ide preko official Forever shopa
- da kroz preporuku moze ostvariti 15% popusta

### 2. SEO + AEO content engine

AVC mora imati:

- jake informational clanke
- jake product guide stranice
- intent hubove
- comparison stranice
- FAQ sekcije
- lako citljiv HTML za Google i AI crawlere

### 3. Referral + Geo routing engine

Klik mora ici ovim putem:

1. korisnik je na AVC clanku ili proizvodu
2. AVC zna jezik i zemlju
3. AVC uzima aktivni Forever ID iz admin postavke
4. AVC odreduje lokalni Forever market
5. AVC gradi official Forever link s referralom
6. AVC biljezi klik i zemlju
7. korisnik ide na odgovarajuci official shop

### 4. AI Advisor engine

AI savjetnik mora biti:

- multilingual
- context-aware
- product-aware
- country-aware
- lead-aware

Ne smije biti samo chat widget.

Mora imati ulogu:

- vodi korisnika
- preporucuje sljedeci clanak
- preporucuje proizvodni vodič
- vodi prema official shopu
- prikuplja kontakt kad korisnik zeli dodatnu pomoc

### 5. Analytics + Growth engine

AVC mora imati:

- sto ljudi citaju
- sto klikaju
- koje zemlje donose interes
- koji clanci vode do shop klikova
- koji proizvodi najbolje rade
- sto AI savjetnik preporucuje
- gdje ljudi odustaju

## Sto ide u Fazu 1

Faza 1 mora dovesti AVC do toga da moze:

- zadrzati postojece URL-ove
- prikazati migrirani sadrzaj u novom PHP sustavu
- imati product guide stranice
- imati tracked outbound shop click
- imati globalni aktivni Forever ID
- imati HR / EN / SL support
- imati osnovni admin
- imati osnovni AI savjetnik
- imati osnovni analytics dashboard

### Faza 1 scope

1. **Core platform**
   - custom PHP app
   - custom router
   - content rendering
   - translation model
   - SEO rendering

2. **Content migration**
   - import postojecih clanaka
   - import proizvoda
   - import slika
   - import SEO metadata
   - import custom URL mapa

3. **Referral settings**
   - admin screen za `Active Forever ID`
   - fallback / validation logika
   - audit log promjena

4. **Geo + redirect**
   - country detection
   - market resolution
   - official Forever redirect controller
   - click tracking

5. **Frontend**
   - home page
   - blog article template
   - product guide template
   - category / intent hub template
   - AI advisor entry points

6. **Admin MVP**
   - content list
   - edit article
   - edit product guide
   - SEO fields
   - translation relation editor
   - internal linking suggestions
   - active Forever ID settings

7. **Analytics MVP**
   - page visits
   - outbound shop clicks
   - clicks by country
   - clicks by article
   - clicks by product
   - AI-starts i AI-click-through

## Sto ide u Fazu 2

Faza 2 je "growth operating system" za AVC.

### Faza 2 scope

1. **Napredni admin**
   - bulk SEO updates
   - content score
   - AI rewriting suggestions
   - content freshness reminders
   - schema preview
   - redirect manager

2. **SEO / rank ops**
   - Search Console povezivanje
   - Google Ads povezivanje
   - page performance panel
   - query / impression / CTR reporting
   - rank tracking integracija

3. **AI lead ops**
   - lead inbox
   - kontakt statusi
   - source attribution
   - AI handoff notes

4. **Advanced recommendation engine**
   - comparison page generator
   - quiz / recommendation flows
   - problem -> product -> article journey builder

5. **Country growth ops**
   - country dashboards
   - localized CTA testing
   - market-specific trust pages

## Sto ide u Fazu 3

Faza 3 je skala i automatizacija.

### Faza 3 scope

1. multilingual content operations
2. automated internal linking suggestions
3. AI assisted content refresh
4. campaign-level referral switching
5. per-country conversion intelligence
6. advanced CRO experiments

## Admin model koji preporucujem

### Glavni moduli admina

1. **Dashboard**
   - top pages
   - top products
   - top countries
   - outbound click trend
   - AI lead trend

2. **Content**
   - articles
   - products
   - categories / intent hubs
   - comparisons

3. **SEO**
   - title
   - meta description
   - canonical
   - hreflang
   - schema flags
   - index / noindex

4. **Referral**
   - active Forever ID
   - fallback rules
   - active discount logic
   - referral test tool

5. **AI Advisor**
   - prompts / behavior
   - localized assistant variants
   - lead capture forms
   - recommendation paths

6. **Analytics**
   - traffic
   - clicks
   - countries
   - sources
   - conversion pathways

7. **Integrations**
   - GA4
   - Search Console
   - Google Ads
   - future CRM / email tools

## Content strategy koju preporucujem

Postojeci sadrzaj dijelimo u 4 skupine:

1. **Keep and polish**
   - vec dobri clanci koji samo trebaju bolju strukturu i CTA

2. **Heavy rewrite**
   - clanci s dobrim potencijalom, ali slabom funkcijom za SEO/AEO i shop flow

3. **Merge / consolidate**
   - clanci koji kanibaliziraju slican intent

4. **Reposition**
   - clanci koji nisu dovoljno povezani s Forever buyer journeyem, ali mogu dovoditi discovery promet

### Novi standard za clanke

Svaki clanak treba dobiti:

- jasniji uvod
- bolji H2/H3 raspored
- FAQ
- related product guide blok
- related article blok
- AI advisor CTA
- country-aware shop CTA gdje ima smisla
- editor note / updated date

### Novi standard za product guide stranice

Svaki proizvod treba imati:

- sto je proizvod
- kome je namijenjen
- kljucne koristi bez pretjerivanja
- FAQ
- related comparisons
- related articles
- shop CTA
- AI advisor CTA

## Kako koristimo tvrdnju o 15% popustu

Ovo mora biti pazljivo i jasno komunicirano.

Preporuceni model poruke:

- korisnici preko tvoje preporuke mogu ostvariti **do 15% / 15% popusta**, ovisno o pravilima i dostupnosti odgovarajuceg Forever marketa

Prije finalnog copyja moramo potvrditi:

- je li 15% zaista univerzalno isto na svim trzistima koje ciljas
- ili moramo koristiti poruke tipa:
  - `ostvarite dodatni popust`
  - `u podrzanim trzistima`
  - `ovisno o pravilima lokalnog Forever shopa`

To je vazno radi:

- pravne tocnosti
- SEO trusta
- AI advisor safety copyja

## Najvazniji UX cilj

Posjetitelj na AVC-u mora u roku od nekoliko sekundi razumjeti:

1. gdje je dosao
2. da ovdje moze dobiti pomoc i preporuku
3. da moze ostvariti popust
4. da moze pricati s AI savjetnikom
5. da moze otvoriti sluzbeni shop za svoju zemlju

## Kljuvni MVP user flow

### Flow A: Blog -> Product -> Shop

1. korisnik dolazi na clanak iz Googlea ili AI searcha
2. clanak objasni problem / temu
3. clanak preporuci odgovarajuci proizvod
4. klik vodi na product guide ili direktni redirect
5. AVC gradi official shop link s aktivnim Forever ID-om
6. klik se biljezi

### Flow B: AI Advisor -> Product -> Shop

1. korisnik otvara AI savjetnika
2. pita za problem / cilj
3. AI vodi prema odgovarajucem clanku ili proizvodu
4. AI nudi shop CTA ili lead capture
5. outbound link ide kroz AVC tracked redirect

### Flow C: Country Trust Page -> Shop

1. korisnik dolazi na zemljom relevantnu landing stranicu
2. vidi kako kupnja funkcionira za njegovu zemlju
3. vidi poruku o podrsci i popustu
4. klik ide na odgovarajuci Forever market

## Kljuvni razvojni prioriteti

Prioritet 1:

- URL preservation
- content import
- referral engine
- active Forever ID setting
- product + article templates

Prioritet 2:

- AI advisor integration
- analytics dashboard
- HR/EN/SL parity
- content rewriting workflow

Prioritet 3:

- Search Console / Google Ads / rank layer
- advanced CRO
- recommendation automations

## Sto radimo sljedece

Sljedeci konkretni korak je:

1. izvesti AVC content inventory
2. izvesti AVC permalink mapu
3. izvesti AVC Yoast SEO mapu
4. izvesti AVC WPML translation mapu
5. definirati novu bazu i route model
6. definirati admin model za:
   - active Forever ID
   - content editor
   - SEO editor
   - analytics

## Dvije otvorene stvari koje nisu blocker za start

1. **Lead destination**
   - kamo idu leadovi iz AI savjetnika:
     - email
     - CRM
     - WhatsApp
     - poseban admin inbox

2. **Discount copy compliance**
   - treba potvrditi kako tocno formulirati `15% popusta` po marketima da komunikacija bude potpuno tocna
