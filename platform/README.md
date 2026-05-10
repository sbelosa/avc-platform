# AVC Platform

Ovo je pocetni skeleton nove custom PHP platforme za `aloevera-centar.com`.

## Cilj ovog foldera

- nova server-rendered PHP aplikacija
- routing koji cuva postojece AVC URL-ove
- content + SEO + multilingual baza
- referral i geo redirect engine
- AI lead inbox u adminu
- temelji za AI savjetnika i outbound analytics

## Trenutni scope

U ovoj fazi su postavljeni:

- osnovna struktura aplikacije
- jednostavan router i response sloj
- pocetne javne i admin rute
- pocetni servis za aktivni Forever ID
- pocetni market resolver
- inicijalna SQL schema

## Source podataka za migraciju

WordPress export datoteke nalaze se u:

- `/Users/stjepanbelosa/Documents/AVC - LOCAL/exports/wordpress/`

To je ulaz za sljedeci korak:

- import URL mape
- import SEO mape
- import translation grupa
- import clanaka i product guide sadrzaja
