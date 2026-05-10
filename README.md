# Aloevera-centar lokalni restore

Ovaj folder je pripremljen za siguran lokalni restore postojece WordPress stranice prije migracije na klasicni PHP site.

## Zasto ovako

Ne preporucujem da odmah rusimo WordPress na produkciji i gradimo novu stranicu "iz nule". Trenutna stranica vec ima:

- indeksirane blog clanke
- produkt URL-ove
- Yoast SEO meta podatke i sitemap
- visejezicne URL-ove
- postojece interne linkove i slike

Prvi siguran korak je:

1. Napraviti lokalnu kopiju sitea.
2. Popisati sve URL-ove, meta podatke i sadrzaj koji nose promet.
3. Tek onda graditi PHP verziju uz zadrzavanje URL strukture i 301 redirect pravila gdje je to potrebno.

## Sto mi trebas dati

Najbolja varijanta backupa je:

- baza: `.sql` ili `.sql.gz` export WordPress baze
- datoteke: kompletan WordPress root ili barem `wp-content` + `wp-config.php`

Ako imas backup iz plugina kao sto su Duplicator ili All-in-One WP Migration, i to mozemo iskoristiti, ali je najjednostavnije raditi s "raw" bazom i datotekama.

## Struktura foldera

- `backups/db/` - ovdje stavi SQL dump
- `backups/site/` - ovdje stavi zip/tar backup datoteka ako ga zelis prvo samo odloziti
- `local/wordpress/` - ovdje ide raspakirani WordPress site

## Brzi start

1. Kopiraj `.env.example` u `.env`
2. Ubaci backup baze u `backups/db/`
3. Raspakiraj WordPress datoteke u `local/wordpress/`
4. Pokreni:

```bash
docker compose up -d
```

5. Uvezi bazu:

```bash
./scripts/import-db.sh
```

6. Otvori:

- WordPress: <http://localhost:8080>
- phpMyAdmin: <http://localhost:8081>

## Vazno nakon importa

Nakon sto se lokalna kopija digne, obicno treba odraditi:

- provjeru `siteurl` i `home` vrijednosti u bazi
- search/replace produkcijskog domena prema lokalnom URL-u
- provjeru permalinka
- provjeru slika, blog postova, proizvoda i prijevoda
- po potrebi povecanje lokalnog PHP memory limita zbog WooCommerce, Yoast i WPML pluginova

To mogu odraditi ja cim ubacis backup.

## Ako imas Backuply `.tar` backup

Ako je backup jedan veliki `.tar` fajl koji sadrzi `public_html/` i `backuply-data/mysql/*.sql.gz`, koristi:

```bash
./scripts/prepare-backuply.sh
```

Skripta ce:

- izvuci WordPress datoteke u `local/wordpress/`
- preskociti produkcijski `wp-config.php`
- izvuci SQL dump u `backups/db/`

## Kako cemo migrirati na klasicni PHP bez gubitka SEO-a

Glavno pravilo je: novi PHP site mora zadrzati sve vrijedne URL-ove ili imati tocan 301 redirect za svaki URL koji se mijenja.

Ne smijemo izgubiti:

- slugove clanaka
- naslovne tagove i meta opise
- canonical oznake
- H1 i glavni tekst clanaka
- featured slike i alt tekstove
- kategorije i interne linkove
- schema oznake gdje imaju vrijednost
- sitemap i robots pravila

Tek nakon SEO inventure radimo:

1. novu informacijsku arhitekturu
2. predloske za blog i proizvode u PHP-u
3. migraciju sadrzaja
4. redirect mapu
5. staging test
6. produkcijsko prebacivanje

## Sljedeci korak

Kad ubacis backup, mogu:

- podici lokalnu kopiju
- analizirati strukturu sadrzaja
- izdvojiti najvaznije clanke i landing stranice
- predloziti novu PHP arhitekturu koja cuva SEO i bolje prodaje Forever proizvode
