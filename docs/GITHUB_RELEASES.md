# URPE GitHub Releases

Run **Build and Publish URPE Release** manually from GitHub Actions with a new tag such as `v2026.10.24.1`.

The workflow builds Laravel 13 / PHP 8.4 production dependencies and Vite assets on GitHub Actions, packages only approved directories, and publishes **release.zip** plus **release.zip.sha256** to a GitHub Release. It uses the current `master` commit and records its SHA in `kerberos-release.json`.

**This workflow only builds and publishes.** It does not contact cPanel, perform backups, run migrations or deploy. Kerberos must later fetch this exact release asset, verify the trusted SHA-256, and pass it to a reviewed Laravel-aware installer. No production deployment is authorized by publishing a Release.

The application deployment must preserve server-managed `.env`, `storage`, private clinical uploads and database data; it must follow `docs/UAT_DEPLOYMENT.md` and `docs/BACKUP_RESTORE.md`. Database migrations may require a database restore and are not necessarily reversible by switching application files.
