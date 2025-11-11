# Manual Push Instructions

## Overview
The fixes for both branches have been committed locally but need to be pushed to GitHub. This requires repository write access.

## Prerequisites
- Repository write access to `dynamic/foxystripe`
- Git installed and configured
- Clone of the repository

## Quick Push (Recommended)

If you have this repository cloned with the local commits already present:

```bash
# Push both branches
git push origin migration-silverstripe-foxy
git push origin pull/SSOcorrection
```

## Alternative: Clone and Apply Patches

If you need to apply the fixes in a fresh clone:

### Step 1: Clone the repository
```bash
git clone https://github.com/dynamic/foxystripe.git
cd foxystripe
```

### Step 2: Fix migration-silverstripe-foxy branch
```bash
# Checkout the branch
git checkout migration-silverstripe-foxy

# Edit composer.json - change line 30
# From: "phpunit/PHPUnit": "^5.7",
# To:   "phpunit/phpunit": "^5.7",
sed -i 's/"phpunit\/PHPUnit":/"phpunit\/phpunit":/g' composer.json

# Validate
composer validate

# Commit and push
git add composer.json
git commit -m "Fix composer.json - change phpunit/PHPUnit to phpunit/phpunit (lowercase)"
git push origin migration-silverstripe-foxy
```

### Step 3: Fix pull/SSOcorrection branch
```bash
# Checkout the branch
git checkout pull/SSOcorrection

# Edit composer.json - change line 30
# From: "phpunit/PHPUnit": "^5.7",
# To:   "phpunit/phpunit": "^5.7",
sed -i 's/"phpunit\/PHPUnit":/"phpunit\/phpunit":/g' composer.json

# Validate
composer validate

# Commit and push
git add composer.json
git commit -m "Fix composer.json - change phpunit/PHPUnit to phpunit/phpunit (lowercase)"
git push origin pull/SSOcorrection
```

## Verification

After pushing, verify the changes on GitHub:

1. Visit: https://github.com/dynamic/foxystripe/tree/migration-silverstripe-foxy
2. Check composer.json shows `"phpunit/phpunit": "^5.7",` (lowercase)
3. Visit: https://github.com/dynamic/foxystripe/tree/pull/SSOcorrection  
4. Check composer.json shows `"phpunit/phpunit": "^5.7",` (lowercase)

## Expected Outcome

Once pushed:
- Packagist will be able to process updates for these branches
- The error "require-dev.phpunit/PHPUnit is invalid" will be resolved
- Package updates will resume normally

## Need Help?

If you encounter authentication issues:
- Ensure you have write access to the repository
- Check your Git credentials: `git config --list | grep credential`
- Try using SSH instead of HTTPS: `git remote set-url origin git@github.com:dynamic/foxystripe.git`

## Automated Option

If you prefer automation, use the included script:
```bash
./apply-branch-fixes.sh
```

This script will:
1. Checkout each branch
2. Apply the fix (or skip if already applied)
3. Validate with `composer validate`
4. Commit changes
5. Push to origin

The script handles cases where fixes are already applied and provides clear status messages.