# Darlington RnB audio manager

This site-specific feature keeps the Howdle CMS package generic. The CMS owner can manage the Darlington RnB audio library at:

`/admin/audio-tracks`

Each track has a title, artist, MP3 file, album cover, cover description, display order, and published/hidden setting. Only published tracks appear on `/audio`.

## First deployment

1. Deploy the `feature/darlington-audio-manager` branch to the local/test environment.
2. Ensure PHP upload limits allow the selected files. The app accepts MP3 files up to 50 MB and JPG, PNG, or WebP covers up to 10 MB.
3. Run the database migration and public storage link if they are not already set up:

   ```sh
   php artisan migrate --force
   php artisan storage:link
   ```

4. Copy the current bundled 17-song library into Laravel public storage and create published database records:

   ```sh
   php artisan rnb:import-legacy-audio
   ```

   The import is safe to run again: existing title/artist records are skipped. Missing legacy files are reported and skipped.

5. Sign in with a CMS owner account and visit `/admin/audio-tracks`. Check `/audio` for the migrated player.

New uploads are stored on Laravel's `public` disk under `storage/app/public/rnb-audio`. Back up that directory along with the site database. The importer leaves the old `public/media` files in place.
