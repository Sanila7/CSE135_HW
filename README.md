## GitHub Auto-Deploy to DigitalOcean

This site auto-deploys to my DigitalOcean droplet whenever I push to the `main` branch.

**How it works**
- GitHub Actions runs on every push to `main`
- The workflow uses SSH + rsync to copy the repo contents to:
  `/var/www/sanisilva.site/public_html`
- `rsync --delete` keeps the server folder synchronized with the GitHub repo

**Workflow**
- `.github/workflows/deploy.yml`

**Demo**
- See `Github-Deploy.gif` (or `Github-Deploy.mp4`) for a short screen recording showing: edit → push → Actions deploy → live update.

## Password Protection (Team Site)

The team site is protected using **Apache Basic Authentication** and served over **HTTPS (SSL)**.

### Access
- **URL:** https://sanisilva.site
- **Authentication Type:** Basic Auth (Apache)
- **Username:** sanila
- **Password:** DSanila@0124
- **Username:** grader
- **Password:** Grader123

### Implementation Details
- SSL was configured using Let's Encrypt
- Authentication is enforced at the Apache virtual host level
- Protected directory:
