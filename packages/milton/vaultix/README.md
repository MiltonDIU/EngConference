# 🛡️ Vaultix: Enterprise Backup Engine for Laravel

Vaultix is a high-performance, admin-managed backup orchestration package for Laravel. It provides a stunning dashboard to manage dynamic backup jobs across multiple cloud providers like **AWS S3, Cloudflare R2, Google Drive, and SFTP**, all without touching a single line of code after installation.

---

## 🌟 Key Features

- **🚀 Dynamic Job Management:** Create, edit, and trigger backup jobs directly from the dashboard.
- **📊 Smart Storage Projection:** Predicts future storage needs based on your retention policy and current project size.
- **🛡️ Pre-backup Safety Validator:** Automatically aborts backups if server disk space is low (calculates 1.5x project size safety buffer).
- **☁️ Multi-Provider Support:** First-class support for Google Drive, S3-Compatible (AWS/R2), and SFTP.
- **📧 Premium HTML Notifications:** Beautiful, color-coded email alerts for success and failures with direct dashboard links.
- **📂 Selective Backups:** Choose between Database Only, Files Only, or Full Backups per job.
- **🔄 Export/Import Configs:** Quickly replicate your entire backup infrastructure across multiple projects via JSON.
- **📈 Real-time Monitoring:** Track Server Disk Space, Project Size (DB + Files), and Scheduler Health.
- **🔐 Role-Based Access:** Secure dashboard access via Super Admin email or dynamic authorized user list.

---

## 🛠️ Installation

### 1. Install via Composer
```bash
composer require milton/vaultix
```

### 2. Publish Assets & Migrations
```bash
php artisan vendor:publish --tag=vaultix-config
php artisan vendor:publish --tag=vaultix-emails
php artisan migrate
```

### 3. Configure .env
Add your super admin email to gain initial access to the dashboard:
```env
VAULTIX_SUPER_ADMIN=admin@example.com
VAULTIX_DISK_PATH=/ # Optional: path to monitor disk space (e.g. /mnt/c on WSL)
```

---

## ⚙️ Configuration

### Scheduler Setup
Vaultix automates your backups using the Laravel Scheduler. Ensure the following cron job is running on your server:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### Google Drive Setup
To use Google Drive, ensure you have a Refresh Token and Folder ID. Vaultix handles the runtime driver injection for you.

---

## 📤 Export & Import
Managing multiple projects? Configure one project's storage and jobs perfectly, then:
1. Click **Export** on the dashboard to download `vaultix_configs.json`.
2. Go to your other project and click **Import**.
3. All your S3/GDrive credentials and jobs are instantly recreated!

---

## 🛡️ Security & Authorization
By default, only the user defined in `VAULTIX_SUPER_ADMIN` can access the dashboard at `/vaultix`.
You can add more authorized users (by email) through the **Settings** page within the dashboard. All authorized users must be logged into your application.

---

## 📝 License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

---

## 🤝 Contribution
Developed with ❤️ by **Milton**. Contributions are welcome!
