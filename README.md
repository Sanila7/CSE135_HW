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

### Summary of Changes Observed in DevTools
After enabling Apache compression using mod_deflate, the behavior of the HTML file changed when inspected in Chrome DevTools. In the Network tab, the response headers for index.html now include:

Content-Encoding: gzip


This indicates that the HTML content is compressed before being sent to the client. Additionally, DevTools shows that the Transferred size of the HTML file is smaller than the original Resource size, confirming that compression is active and reducing the amount of data transmitted over the network. The same compression behavior is observed for CSS and JavaScript files.


### Server Header Obfuscation (Step 6)

To obscure the server identity, the site was placed behind an Nginx reverse proxy, with Apache running only on an internal port.

Apache was reconfigured to listen exclusively on 127.0.0.1:8080, preventing it from directly handling external HTTP/HTTPS traffic. Nginx was then configured to listen on ports 80 and 443, terminate SSL, and forward all requests to Apache internally.

At the Nginx layer, the default upstream Server header was explicitly removed and replaced with a custom value using the headers-more module. This ensures that Apache’s server signature is never exposed to the client.

As a result, all external HTTP responses now include:

Server: CSE135 Server


Verification was performed using both curl and Chrome DevTools by inspecting the response headers. A screenshot confirming the modified header is included as header-verify.jpg.
