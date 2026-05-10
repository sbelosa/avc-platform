# Plan migracije WordPress -> klasicni PHP

## Faza 1: Sigurnosna kopija i lokalni restore

Prvo trebamo imati potpunu lokalnu kopiju postojece stranice:

- WordPress datoteke
- MySQL dump
- pregled pluginova i teme

Cilj ove faze je da sve testiramo lokalno, bez rizika za produkciju.

## Faza 2: SEO inventura prije bilo kakve promjene

Prije migracije trebamo popisati:

- sve postojece URL-ove clanaka, kategorija, proizvoda i stranica
- title i meta description
- canonical tagove
- H1 i glavne blokove sadrzaja
- interne linkove
- indeksirane slike
- prijevode i jezik URL-ova
- top landing stranice koje donose organski promet

Bez ovog koraka je lako izgubiti ranking i promet.

## Faza 3: Nova PHP arhitektura

Za klasicni PHP site preporuka je sljedeca struktura:

- `public/` za ulazne datoteke
- `app/` za logiku
- `templates/` za prikaz
- `storage/` za cache i logove
- `content/` ili baza za clanke i proizvode

Bitno je da blog clanci i produkt stranice imaju:

- ciste URL-ove
- dobar HTML heading raspored
- strukturirane podatke
- brze stranice
- dobar interni linking prema proizvodima i blogu

## Faza 4: Sadrzaj i prodaja

Za Forever proizvode bih slozio 3 tipa stranica:

- produkt stranice s jasnim benefitima, CTA-om i FAQ sekcijom
- category landing stranice po problemu ili cilju korisnika
- edukativne blog clanke koji vode prema relevantnim proizvodima

Primjer:

- clanak o probavi -> link na odgovarajuce aloe proizvode
- clanak o kozi -> link na njegu koze
- clanak o imunitetu -> link na dodatke prehrani

## Faza 5: Migracija bez gubitka SEO-a

Pravila:

1. Zadrzati iste URL-ove kad god je moguce.
2. Ako se URL promijeni, postaviti 301 redirect.
3. Zadrzati ili unaprijediti title, meta description i glavne headinge.
4. Ne micati dobar sadrzaj samo zato sto prelazimo na novi sustav.
5. Ostaviti sitemap i robots u urednom stanju.
6. Provjeriti Search Console nakon pustanja.

## Faza 6: Objavljivanje u etapama

Najsigurniji put je:

1. lokalni restore
2. staging verzija
3. migracija jednog dijela stranice ili cijelog sitea uz redirect mapu
4. pracenje crawl gresaka, indeksacije i pozicija

## Preporuceni start

Najprije mi dostavi:

- backup baze
- backup datoteka
- ako mozes, listu pluginova koje koristis za SEO, prijevode i forme

Kad to ubacis u ovaj workspace, mogu odmah podici lokalnu kopiju i pripremiti bazu za daljnju izradu.
