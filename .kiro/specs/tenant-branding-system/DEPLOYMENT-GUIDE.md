# Tenant Branding System - Deployment Guide

**Date**: April 27, 2026  
**Version**: 1.0.0  
**Estimated Time**: 15 minutes

---

## 🚀 Quick Start

### Step 1: Run Database Migration

The new migration adds theme system columns to the `churches` table in **tenant databases**.

```bash
# Run migration on all tenant databases
php artisan tenants:migrate

# Or run on specific tenant
php artisan tenants:migrate --tenants=<tenant-id>
```

**What this does**:
- Adds `secondary_color` column
- Adds `accent_color` column
- Adds `logo_dark_path` column
- Adds `favicon_path` column
- Adds `theme_config` JSON column

### Step 2: Clear Caches

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 3: Verify Storage Link

Ensure the storage link exists for logo uploads:

```bash
# Create storage link if not exists
php artisan storage:link
```

### Step 4: Test the Feature

1. Log in as a church admin
2. Navigate to **Settings → Appearance**
3. Upload a logo (PNG, JPG, SVG, or WebP)
4. Change the primary color
5. Click **Save Changes**
6. Verify logo appears in sidebar
7. Verify theme colors apply throughout dashboard

---

## 🔍 Verification Checklist

### Database Verification

```bash
# Check if migration ran successfully
php artisan tenants:migrate-status

# Verify columns exist (in tinker)
php artisan tinker
>>> $church = \App\Models\Church::first();
>>> $church->getAttributes();
# Should show: secondary_color, accent_color, logo_dark_path, favicon_path, theme_config
```

### File System Verification

```bash
# Check storage directory exists
ls -la storage/app/public/

# Check if storage link exists
ls -la public/storage

# If link doesn't exist, create it
php artisan storage:link
```

### UI Verification

- [ ] Logo upload form appears
- [ ] Color picker works
- [ ] Live preview updates
- [ ] Save button works
- [ ] Logo displays in sidebar
- [ ] Theme colors apply to buttons
- [ ] Theme colors apply to forms
- [ ] Theme colors apply to navigation
- [ ] No yellow background on logo
- [ ] Accessibility warnings show (if applicable)

---

## 🐛 Troubleshooting

### Issue: Migration Fails

**Error**: `Column already exists`

**Solution**:
```bash
# Check if columns already exist
php artisan tinker
>>> Schema::hasColumn('churches', 'secondary_color');

# If true, migration already ran
# If false, check migration file for errors
```

### Issue: Logo Not Uploading

**Error**: `The logo failed to upload`

**Solution**:
1. Check file permissions:
   ```bash
   chmod -R 775 storage/app/public
   ```

2. Verify storage link:
   ```bash
   ls -la public/storage
   # Should point to ../storage/app/public
   ```

3. Check disk space:
   ```bash
   df -h
   ```

### Issue: Logo Not Displaying

**Symptoms**: Logo uploads but doesn't show in sidebar

**Solution**:
1. Check browser console for errors
2. Verify logo URL is correct:
   ```bash
   php artisan tinker
   >>> $church = \App\Models\Church::first();
   >>> $church->logo_url;
   # Should return: http://your-domain/storage/tenant-xxx/logos/xxx.png
   ```

3. Check if file exists:
   ```bash
   ls -la storage/app/public/tenant-*/logos/
   ```

4. Clear browser cache (Ctrl+Shift+Delete)

### Issue: Theme Not Applying

**Symptoms**: Colors don't change after saving

**Solution**:
1. Clear Laravel caches:
   ```bash
   php artisan cache:clear
   php artisan view:clear
   ```

2. Clear browser cache

3. Verify primary_color is set:
   ```bash
   php artisan tinker
   >>> $church = \App\Models\Church::first();
   >>> $church->primary_color;
   # Should return: #XXXXXX
   ```

4. Check CSS variables in browser DevTools:
   - Open DevTools (F12)
   - Go to Elements tab
   - Select `:root` element
   - Check if `--tenant-primary` is set

### Issue: Accessibility Warnings

**Symptoms**: Warning shows after choosing color

**Solution**: This is expected behavior!
- The warning indicates the color may have poor contrast
- You can still save the color (it's a warning, not an error)
- Consider choosing a different shade for better accessibility
- Use the color picker to find a more accessible color

---

## 📊 Rollback Plan

If you need to rollback the changes:

### Step 1: Rollback Migration

```bash
# Rollback last migration on all tenants
php artisan tenants:rollback

# Or rollback specific migration
php artisan tenants:rollback --step=1
```

### Step 2: Restore Previous Files

```bash
# If you have git, revert the changes
git checkout HEAD~1 -- app/Services/ThemeService.php
git checkout HEAD~1 -- app/Services/LogoService.php
git checkout HEAD~1 -- app/Http/Controllers/AppearanceController.php
git checkout HEAD~1 -- app/Models/Church.php
git checkout HEAD~1 -- resources/views/layouts/app.blade.php
git checkout HEAD~1 -- resources/views/partials/sidebar.blade.php
git checkout HEAD~1 -- resources/views/settings/appearance.blade.php
```

### Step 3: Clear Caches

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

---

## 🔐 Security Checklist

Before deploying to production:

- [ ] File upload validation is working
- [ ] File size limits are enforced (2MB)
- [ ] Only allowed file types can be uploaded
- [ ] Logos are stored in tenant-specific directories
- [ ] No cross-tenant access is possible
- [ ] Storage permissions are correct (775)
- [ ] Error messages don't expose sensitive info

---

## 📈 Monitoring

After deployment, monitor:

### Application Logs

```bash
# Watch Laravel logs
tail -f storage/logs/laravel.log

# Check for errors related to:
# - Logo uploads
# - Theme generation
# - File storage
```

### Storage Usage

```bash
# Check storage usage
du -sh storage/app/public/tenant-*/logos/

# Monitor growth over time
```

### Performance

```bash
# Check page load times
# - Appearance settings page
# - Dashboard (with theme applied)
# - Sidebar (with logo)

# Should be < 2 seconds for all pages
```

---

## 📞 Support

If you encounter issues:

1. **Check logs**: `storage/logs/laravel.log`
2. **Check browser console**: F12 → Console tab
3. **Verify file permissions**: `ls -la storage/app/public`
4. **Clear all caches**: `php artisan cache:clear && php artisan view:clear`
5. **Restart queue workers**: `php artisan queue:restart`

---

## ✅ Post-Deployment Checklist

- [ ] Migration ran successfully on all tenants
- [ ] Storage link exists
- [ ] File permissions are correct
- [ ] Logo upload works
- [ ] Logo displays in sidebar
- [ ] Theme colors apply throughout UI
- [ ] Accessibility validation works
- [ ] No errors in logs
- [ ] Performance is acceptable
- [ ] Browser testing complete
- [ ] Mobile testing complete
- [ ] Documentation updated
- [ ] Team notified of new feature

---

## 🎉 Success!

If all checks pass, the Tenant Branding System is successfully deployed!

**Next Steps**:
1. Notify church admins about the new feature
2. Create user documentation/tutorial
3. Monitor usage and feedback
4. Plan Phase 3 enhancements

---

**End of Deployment Guide**
