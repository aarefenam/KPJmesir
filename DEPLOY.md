# Auto-Deploy KPJ Mesir

Rantai deploy: **push ke `main`** → GitHub Actions → SSH ke cPanel → `git pull` →
cPanel menjalankan `.cpanel.yml` → file mendarat di `public_html`.

cPanel tidak bisa menerima notifikasi dari GitHub. Yang menjembatani adalah
GitHub Actions ([.github/workflows/deploy.yml](.github/workflows/deploy.yml)),
yang login SSH ke server lalu menyuruhnya menarik commit terbaru.

---

## Yang perlu disiapkan sekali saja

Ada **dua pasang kunci SSH** yang arahnya berbeda. Sering tertukar, jadi perhatikan:

| Kunci | Arah | Privat disimpan di | Publik didaftarkan di |
|---|---|---|---|
| #1 Deploy key | cPanel → GitHub (menarik kode) | `~/.ssh/github_deploy` di server | GitHub → repo → Deploy keys |
| #2 Actions key | GitHub → cPanel (memicu deploy) | GitHub → repo → Secrets | cPanel → SSH Access |

### 1. Kunci #1 — supaya server bisa menarik dari repo privat

Repo ini **privat**, jadi server butuh kredensial sendiri untuk `git pull`.
Jalankan lewat cPanel → Terminal (atau SSH):

```bash
ssh-keygen -t ed25519 -C "cpanel-kpjmesir-pull" -f ~/.ssh/github_deploy -N ""
cat ~/.ssh/github_deploy.pub
```

Salin isi `.pub` itu ke **GitHub → repo KPJmesir → Settings → Deploy keys →
Add deploy key**. Beri nama `cPanel kpjmesir`. **Jangan** centang
"Allow write access" — server hanya perlu membaca.

Lalu arahkan SSH server agar memakai kunci itu untuk github.com:

```bash
cat >> ~/.ssh/config <<'EOF'
Host github.com
  HostName github.com
  User git
  IdentityFile ~/.ssh/github_deploy
  IdentitiesOnly yes
EOF
chmod 600 ~/.ssh/config
ssh-keyscan github.com >> ~/.ssh/known_hosts

ssh -T git@github.com     # harus muncul "successfully authenticated"
```

### 2. Daftarkan repo di cPanel

cPanel → **Git Version Control** → **Create**:

- Clone a Repository: **ON**
- Clone URL: `git@github.com:aarefenam/KPJmesir.git`
- Repository Path: `/home/kpjmesir/repositories/KPJmesir`
- Repository Name: `KPJmesir`

Path repo **tidak boleh** `public_html`. Repo adalah sumber; `public_html`
adalah tujuan. `.cpanel.yml` yang menyalin dari satu ke lainnya.

### 3. Kunci #2 — supaya GitHub Actions bisa masuk ke cPanel

Di komputer lokal:

```bash
ssh-keygen -t ed25519 -C "github-actions-deploy" -f ~/.ssh/kpjmesir_actions -N ""
cat ~/.ssh/kpjmesir_actions.pub     # -> publik
cat ~/.ssh/kpjmesir_actions         # -> privat
```

- **Publik** → cPanel → SSH Access → Manage SSH Keys → Import Key → paste →
  simpan → klik **Manage** → **Authorize**. (Kalau belum di-Authorize, kunci
  terdaftar tapi ditolak saat login.)
- **Privat** → GitHub Secret `CPANEL_SSH_KEY` di langkah berikutnya. Salin
  utuh, termasuk baris `-----BEGIN...-----` dan `-----END...-----`.

### 4. Isi GitHub Secrets

GitHub → repo → Settings → Secrets and variables → **Actions** → New repository secret:

| Secret | Isi |
|---|---|
| `CPANEL_HOST` | `kpjmesir.org` (atau hostname server dari cPanel) |
| `CPANEL_USER` | `kpjmesir` |
| `CPANEL_PORT` | `22` — banyak hosting memakai `2222`, cek di menu SSH Access |
| `CPANEL_SSH_KEY` | isi lengkap kunci privat `kpjmesir_actions` |
| `CPANEL_REPO_PATH` | `/home/kpjmesir/repositories/KPJmesir` |

### 5. Uji coba

GitHub → tab **Actions** → "Deploy ke kpjmesir.org" → **Run workflow**.
Jalankan manual dulu sebelum mengandalkan push otomatis.

Setelah ini, setiap `git push origin main` men-deploy sendiri (~1 menit).

---

## Apa yang di-deploy dan apa yang tidak

`.cpanel.yml` sengaja tidak menyalin semuanya:

| Item | Perlakuan | Alasan |
|---|---|---|
| `sitepad-data/themes/` | disalin penuh, `--delete` | ini kode; file yang dihapus di Git harus ikut hilang di live |
| `sitepad-data/uploads/` | hanya menambah | media yang di-upload lewat admin tidak ada di Git — `--delete` akan menghapusnya |
| `underconstructions.html`, config cache | disalin | file statis biasa |
| `sitepad-data/cache/` | dikosongkan | cache lama menyembunyikan perubahan tema |
| **`index.php`** | **tidak disentuh** | berisi kredensial DB + AUTH_KEY milik server. Sekali ditimpa dari Git setelah password DB diganti, seluruh situs mati |

### Yang tidak bisa dilakukan Git di sini

**Isi halaman SitePad tersimpan di MySQL, bukan di file.** Teks, susunan
halaman, menu, dan pengaturan yang diedit lewat admin SitePad **tidak ikut**
ke Git dan **tidak ikut** ter-deploy. Git hanya membawa tema dan media.

Artinya: alur ini cocok untuk perubahan tema/CSS/aset. Untuk isi halaman,
sumber kebenarannya tetap database live — dan itu perlu backup terpisah
(cPanel → phpMyAdmin → Export, database `kpjmesir_spd4571`).

---

## Peringatan sebelum deploy pertama

Commit `8af03eb` adalah snapshot **September 2025**. Sekarang sudah hampir
setahun berlalu. Kalau tema di live pernah diubah sejak itu, deploy pertama
akan **mengembalikannya ke versi lama** — karena tema disalin dengan `--delete`.

Sebelum menjalankan workflow pertama kali:

1. Backup `public_html` lewat cPanel → File Manager → Compress, atau:
   `tar czf ~/backup-prod-$(date +%F).tar.gz -C ~/public_html .`
2. Export database `kpjmesir_spd4571` lewat phpMyAdmin.
3. Bandingkan tema live dengan yang di Git. Kalau live lebih baru,
   tarik dulu versi live ke Git dan commit — jangan sebaliknya.

---

## Kalau gagal

| Gejala | Penyebab tersering |
|---|---|
| `Permission denied (publickey)` di Actions | kunci #2 belum di-**Authorize** di cPanel, atau `CPANEL_PORT` salah |
| `Repository not found` saat cPanel clone | deploy key #1 belum terdaftar, atau `~/.ssh/config` belum dibuat |
| `GAGAL: .cpanel.yml tidak ada di branch ini` | repo di cPanel meng-checkout branch selain `main` |
| Workflow hijau tapi situs tidak berubah | cache browser, atau SpeedyCache menulis ulang — hard refresh dulu |
| `commit ... tidak muncul di deploy.log` | `.cpanel.yml` berhenti di tengah. Cek cPanel → Git Version Control → Manage → Deployment History |

Log deploy di server: `~/deploy.log` (di luar `public_html`, tidak bisa diakses publik).
