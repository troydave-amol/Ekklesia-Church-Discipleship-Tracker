#!/bin/sh

# Release script for Discipleship Progress Tracker
# Usage: ./scripts/release.sh 1.2.0

set -e

# Validate semver argument
if [ -z "$1" ]; then
    echo "Error: Version argument required"
    echo "Usage: ./scripts/release.sh 1.2.0"
    exit 1
fi

VERSION="$1"

# Validate semver format (X.Y.Z)
if ! echo "$VERSION" | grep -qE '^[0-9]+\.[0-9]+\.[0-9]+$'; then
    echo "Error: Invalid version format. Expected: X.Y.Z (e.g., 1.2.0)"
    exit 1
fi

echo "📦 Preparing release: $VERSION"

# Check CHANGELOG.md exists
if [ ! -f "CHANGELOG.md" ]; then
    echo "Error: CHANGELOG.md not found"
    exit 1
fi

# Get current date in ISO format
RELEASE_DATE=$(date +%Y-%m-%d)

# Create temporary file for updated CHANGELOG
TEMP_CHANGELOG=$(mktemp)

# Read current CHANGELOG and update [Unreleased] section
{
    while IFS= read -r line; do
        # Check if line contains [Unreleased]
        if echo "$line" | grep -q '^\[Unreleased\]'; then
            # Output the version header with date
            echo "## [$VERSION] - $RELEASE_DATE"
            echo ""
            # Read rest of Unreleased section until next version
            while IFS= read -r unreleased_line; do
                if echo "$unreleased_line" | grep -qE '^\['; then
                    # Found next version, output it and break
                    echo ""
                    echo "$unreleased_line"
                    break
                elif [ -n "$unreleased_line" ] && ! echo "$unreleased_line" | grep -q '^\[Unreleased\]'; then
                    echo "$unreleased_line"
                fi
            done
            # Now add the new [Unreleased] marker and continue reading rest
            echo ""
            echo "## [Unreleased]"
            echo ""
        else
            echo "$line"
        fi
    done < CHANGELOG.md
} > "$TEMP_CHANGELOG"

# Copy temp file back to CHANGELOG.md
cp "$TEMP_CHANGELOG" CHANGELOG.md
rm "$TEMP_CHANGELOG"

echo "✓ CHANGELOG.md updated"

# Stage and commit
git add CHANGELOG.md
git commit -m "chore: release $VERSION"
echo "✓ Changes committed"

# Create Git tag
git tag "v$VERSION"
echo "✓ Git tag created: v$VERSION"

# Push tag
git push origin "v$VERSION"
echo "✓ Tag pushed to origin"

# Generate GitHub release draft URL
REPO_URL=$(git config --get remote.origin.url | sed 's/.git$//')
REPO_URL=$(echo "$REPO_URL" | sed 's|git@github.com:|https://github.com/|')
RELEASE_DRAFT_URL="$REPO_URL/releases/new?tag=v$VERSION&title=Release%20$VERSION&body=See%20CHANGELOG.md"

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "✅ Release $VERSION prepared successfully!"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "Next steps:"
echo "1. Create PR: release/$VERSION → main"
echo "2. Merge PR to main after approval"
echo "3. Create GitHub release (draft prepared below):"
echo ""
echo "   $RELEASE_DRAFT_URL"
echo ""
echo "4. Merge main back to develop"
echo ""
echo "Tag created: v$VERSION"
echo ""
