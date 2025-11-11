# Fixing Invalid composer.json in Branches

## Overview
This document describes the fixes needed for two branches that have invalid `composer.json` files causing Packagist update failures.

## Problem
The following branches contain invalid package names in their `composer.json` files:
- `migration-silverstripe-foxy`
- `pull/SSOcorrection`

Both branches have `phpunit/PHPUnit` (with uppercase letters) in `require-dev`, which violates Composer's requirement that package names must be lowercase.

## Error from Packagist
```
require-dev.phpunit/PHPUnit is invalid, it should not contain uppercase characters.
Please use phpunit/phpunit instead.
```

## Required Changes
In both branches, the following change needs to be made in `composer.json`:

**Change:**
```json
"require-dev": {
    "phpunit/PHPUnit": "^5.7",
```

**To:**
```json
"require-dev": {
    "phpunit/phpunit": "^5.7",
```

## Status
✅ Fixes have been committed locally to both branches
✅ Both `composer.json` files validated successfully with `composer validate`
⚠️  Commits need to be pushed to GitHub

## Local Commits
The following commits have been made locally:

### migration-silverstripe-foxy branch
- Commit: `bba4ff3cf8ec22458ce8e1cd7f3e1f3c9797a933`
- Message: "Fix composer.json - change phpunit/PHPUnit to phpunit/phpunit (lowercase)"

### pull/SSOcorrection branch
- Commit: `3f53ed23d35a7f843bc9e8060dd405864f30e5a8`
- Message: "Fix composer.json - change phpunit/PHPUnit to phpunit/phpunit (lowercase)"

## How to Apply the Fixes

### Option 1: Manual Push (Requires Repository Write Access)
If you have write access to the repository, you can push the changes:

```bash
# Push migration-silverstripe-foxy branch
git push origin migration-silverstripe-foxy

# Push pull/SSOcorrection branch
git push origin pull/SSOcorrection
```

### Option 2: Apply Patches
Patch files have been created for both branches. To apply them:

```bash
# For migration-silverstripe-foxy
git checkout migration-silverstripe-foxy
git apply /path/to/migration-silverstripe-foxy.patch
git commit -m "Fix composer.json - change phpunit/PHPUnit to phpunit/phpunit (lowercase)"
git push origin migration-silverstripe-foxy

# For pull/SSOcorrection
git checkout pull/SSOcorrection
git apply /path/to/pull-SSOcorrection.patch
git commit -m "Fix composer.json - change phpunit/PHPUnit to phpunit/phpunit (lowercase)"
git push origin pull/SSOcorrection
```

### Option 3: Manual Edit
You can manually edit the `composer.json` file in each branch:

1. Checkout the branch
2. Open `composer.json`
3. Find the line: `"phpunit/PHPUnit": "^5.7",`
4. Change it to: `"phpunit/phpunit": "^5.7",`
5. Run `composer validate` to verify
6. Commit and push the change

## Verification
After applying the fixes, verify them with:

```bash
composer validate
```

Both files should show: `./composer.json is valid`

## Impact
Once these fixes are pushed:
- Packagist will be able to process updates for these branches
- The package will no longer fail during Packagist updates
- Both branches will have valid Composer configuration
