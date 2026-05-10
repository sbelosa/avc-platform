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

FTP deploy does not import MySQL. Import the prepared SQL once through cPanel/phpMyAdmin:

- `platform/storage/backups/avc_platform_ready_for_live_20260510_171151.sql`

After the first import, normal code and content template changes can be deployed through GitHub.

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
