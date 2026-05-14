# Vaultix - Enterprise-Grade Backup & Audit System for Laravel

Vaultix is a sophisticated, secure, and professional backup management package for Laravel (v10 - v13). It provides a full-featured administrative dashboard to manage multiple storage providers, track backup health, and maintain a detailed audit trail of all administrative activities.

## 🚀 Key Features

- **Multi-Provider Support:** Seamlessly integrate with Google Drive, AWS S3, Cloudflare R2, and SFTP.
- **Smart Activity Logging:** Full audit trail with "Line-by-Line" diff highlighting (Git-style) for all configuration changes.
- **Enterprise Security:** 
  - Access restricted to Super Admin and authorized emails.
  - Signed URL protection for secure streamed downloads.
  - No direct storage links exposed.
- **Smart Projection:** Automatically calculates estimated storage usage and file counts based on your retention policies.
- **Automated Maintenance:** 
  - Integrated scheduler for automatic backups.
  - Self-pruning logs (automatically deletes old activity logs based on your retention settings).
- **Data Mobility:** Export/Import entire configurations and download activity logs in CSV/JSON formats.
- **Real-time Monitoring:** Disk usage alerts, scheduler health checks, and queue worker status.

## 📋 Requirements & Dependencies

Vaultix leverages several industry-standard packages to ensure reliability and performance:

- **PHP:** `^8.2 | ^8.3 | ^8.4`
- **Laravel:** `^10.0 | ^11.0 | ^12.0 | ^13.0`

### Core Dependencies
These packages are automatically installed with Vaultix:
- `spatie/laravel-backup`: For the robust core backup engine.
- `masbug/flysystem-google-drive-ext`: For Google Drive integration.
- `league/flysystem-aws-s3-v3`: For AWS S3 and Cloudflare R2 support.
- `league/flysystem-sftp-v3`: For secure SFTP storage.

## 🛠 Installation

1. **Install via Composer:**
   ```bash
   composer require milton/vaultix
   ```

2. **Publish Configuration:**
   ```bash
   php artisan vendor:publish --tag="vaultix-config"
   ```

3. **Run Migrations:**
   ```bash
   php artisan migrate
   ```

4. **Environment Setup:**
   Add the following to your `.env` file to define the Super Admin:
   ```env
   VAULTIX_SUPER_ADMIN=your-email@example.com
   ```

## ⚙️ Configuration

Vaultix allows you to manage most settings directly from the dashboard, but you should ensure your Laravel Scheduler and Queue Worker are running:

```bash
# Add this to your server's crontab
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1

# Ensure queue worker is active for background backups
php artisan queue:work
```

## 🔐 Security & Auditing

Vaultix is designed with security as the top priority:
- **Activity Logs:** Every action (Add storage, Update Job, Download file) is recorded with User IP, User Agent, and a detailed "Before/After" snapshot of data.
- **Signed Downloads:** All backup downloads are generated as temporary signed URLs and streamed through the server to prevent storage link leakage.
- **Access Control:** Only the user defined in `VAULTIX_SUPER_ADMIN` can manage authorized users and view sensitive activity logs.

## 📊 Exporting Logs

You can export your audit logs directly from the dashboard:
- **CSV:** Perfect for Excel/Spreadsheet auditing.
- **JSON:** Ideal for developers and third-party integrations.

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
