# AVC Automated Deployment

This setup follows the FCC model: GitHub stores code, GitHub Actions deploys to cPanel by FTP, and production secrets stay out of Git.

## GitHub Secrets

Add these repository secrets:

- `FTP_SERVER`
- `FTP_USERNAME`
- `FTP_PASSWORD`
- `FTP_PORT`
- `AVC_BASE_URL` with `https://aloevera-centar.com`
- `AVC_PRODUCTION_ENV_B64`
- `AVC_OPS_READONLY_KEY`

Create `AVC_PRODUCTION_ENV_B64` locally from the real production env file:

```bash
base64 -i platform/.env.production | pbcopy
```

Paste the clipboard value into the GitHub secret.

## Production Layout

The workflow deploys:

- `platform/` to `/avc-platform/`
- `deploy/public_html/` to `/public_html/`
- `platform/public/media/` to `/public_html/media/`

Keep the old WordPress uploads folder:

- `/public_html/wp-content/uploads`

The new AVC pages still use those legacy image URLs.

## Initial Database

The repository includes a one-time initial install workflow that can import the AVC database through a protected installer:

- `.github/workflows/initial-production-install.yml`

Run it manually with:

```text
confirm = IMPORT_AVC_DATABASE
force_database_import = false
```

The workflow:

- uploads `platform/`
- uploads `deploy/database/avc_platform_initial.sql.gz`
- uploads a one-time installer to `public_html`
- imports the database using the production `.env.production`
- preserves old `public_html/index.php` and `.htaccess` with backup names
- switches `public_html` to the AVC bridge
- runs live smoke checks

Use this only for the first production switch, or with `force_database_import = true` only when you intentionally want to overwrite the production AVC database again.

The database dump intentionally clears local test lead/click tables at the end of import.

## Readonly Live Check

Enable in `platform/.env.production`:

```env
AVC_OPS_READONLY_ENABLED=1
AVC_OPS_READONLY_KEY=your-long-random-secret
```

Then locally:

```bash
cp scripts/live_ops.env.example scripts/live_ops.env
scripts/prod_ops_fetch.sh health
scripts/prod_ops_fetch.sh overview
```
