#!/bin/bash
# Script to apply composer.json fixes to the affected branches
# This script requires write access to the repository

set -e

echo "========================================="
echo "Applying composer.json fixes to branches"
echo "========================================="

# Check if we're in a git repository
if [ ! -d ".git" ]; then
    echo "Error: Must be run from the root of the foxystripe repository"
    exit 1
fi

# Function to fix a branch
fix_branch() {
    local branch_name=$1
    local patch_file=$2
    
    echo ""
    echo "Fixing branch: $branch_name"
    echo "-------------------------------------------"
    
    # Fetch the latest version of the branch
    echo "Fetching branch from origin..."
    git fetch origin "$branch_name:$branch_name" 2>/dev/null || git checkout "$branch_name"
    
    # Checkout the branch
    echo "Checking out branch..."
    git checkout "$branch_name"
    
    # Apply the fix if patch file exists
    if [ -f "$patch_file" ]; then
        echo "Applying patch: $patch_file"
        git apply "$patch_file" || {
            echo "Patch already applied or manual fix needed"
            # Try manual fix
            sed -i 's/"phpunit\/PHPUnit":/"phpunit\/phpunit":/g' composer.json
        }
    else
        # Manual fix
        echo "Applying manual fix to composer.json..."
        sed -i 's/"phpunit\/PHPUnit":/"phpunit\/phpunit":/g' composer.json
    fi
    
    # Validate composer.json
    echo "Validating composer.json..."
    if command -v composer &> /dev/null; then
        composer validate
    else
        echo "Warning: composer not found, skipping validation"
    fi
    
    # Check if there are changes to commit
    if git diff --quiet composer.json; then
        echo "No changes needed - fix already applied"
    else
        # Commit the changes
        echo "Committing changes..."
        git add composer.json
        git commit -m "Fix composer.json - change phpunit/PHPUnit to phpunit/phpunit (lowercase)"
        
        # Push the changes
        echo "Pushing changes to origin..."
        git push origin "$branch_name"
        
        echo "✓ Branch $branch_name fixed and pushed successfully!"
    fi
}

# Fix both branches
fix_branch "migration-silverstripe-foxy" "migration-silverstripe-foxy.patch"
fix_branch "pull/SSOcorrection" "pull-SSOcorrection.patch"

echo ""
echo "========================================="
echo "All fixes applied successfully!"
echo "========================================="
echo ""
echo "Packagist should now be able to update these branches."
echo "You can verify by running 'composer validate' on each branch."
