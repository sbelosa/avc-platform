# Laptop Sync

Ovo su helper skripte za prebacivanje trenutnog rada s desktop racunala na laptop.

## 1. Na desktopu napravi export

Iz AVC workspacea pokreni:

```bash
cd "/Users/stjepanbelosa/Documents/AVC - LOCAL"
./scripts/export-laptop-state.sh --product-root "/Users/stjepanbelosa/Documents/product"
```

Skripta ce:

- napraviti svjeze SQL dumpove za sve korisnicke baze u AVC Docker stacku
- napraviti svjeze SQL dumpove za sve korisnicke baze u `product` stacku
- zapisati manifest u `exports/laptop-transfer-*.txt`

## 2. Prekopiraj foldere na laptop

Prebaci cijele foldere:

- `/Users/stjepanbelosa/Documents/AVC - LOCAL`
- `/Users/stjepanbelosa/Documents/product`

Najsigurnije je da na laptopu ostanu na istim putanjama.

## 3. Na laptopu instaliraj osnovno

- Docker Desktop
- Codex desktop app
- Git

Za Codex se samo prijavi istim accountom. Ako zelis iste lokalne preference, dodatno prebaci:

- `~/.codex/config.toml`
- `~/.codex/skills/`
- `~/.codex/plugins/`
- `~/.codex/memories/`

Nemoj syncati cijeli `~/.codex` folder preko clouda dok je Codex otvoren.

## 4. Na laptopu pokreni restore

Iz AVC workspacea pokreni:

```bash
cd "/Users/stjepanbelosa/Documents/AVC - LOCAL"
./scripts/setup-laptop-workspaces.sh --product-root "/Users/stjepanbelosa/Documents/product"
```

Skripta ce:

- podici AVC Docker stack
- vratiti zadnji `*-laptop-*.sql.gz` dump za svaku AVC bazu
- postaviti WordPress `siteurl` i `home` na lokalni URL
- podici product stack
- vratiti zadnji `*-laptop-*.sql.gz` dump za svaku product bazu

## 5. Lokalni URL-ovi nakon restorea

- AVC WordPress: `http://localhost:8080`
- AVC phpMyAdmin: `http://localhost:8081`
- AVC platform: `http://localhost:8082`
- product: `http://localhost:8091`

## Korisne opcije

Samo AVC:

```bash
./scripts/export-laptop-state.sh --skip-product
./scripts/setup-laptop-workspaces.sh --skip-product
```

Ako je `product` na drugoj putanji:

```bash
./scripts/export-laptop-state.sh --product-root "/drugi/put/do/product"
./scripts/setup-laptop-workspaces.sh --product-root "/drugi/put/do/product"
```
