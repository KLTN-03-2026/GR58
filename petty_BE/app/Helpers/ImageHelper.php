<?php

namespace App\Helpers;

/**
 * Generic image URL resolver for uploaded files.
 *
 * Handles 3 cases:
 * 1. Empty/null value → return default URL (or null if no default provided)
 * 2. Full URL (starts with http:// or https://) → return as-is (preserves external URLs like Google OAuth avatars)
 * 3. Relative path → build full URL from APP_URL + /storage/ prefix
 *
 * This pattern allows DB to store relative paths while supporting backward compatibility
 * with legacy full URLs and external URLs.
 */
class ImageHelper
{
    /**
     * Resolve a file path/URL to a full URL.
     *
     * @param string|null $value The value from DB (relative path, full URL, or null)
     * @param string|null $defaultUrl Default URL to return if value is empty
     * @return string|null Full URL or default
     */
    public static function resolveUrl($value, $defaultUrl = null): ?string
    {
        // Case 1: empty/null → return default
        if (empty($value)) {
            return $defaultUrl;
        }

        // Case 2: full URL (http:// or https://) → return as-is
        if (preg_match('#^https?://#i', $value)) {
            return $value;
        }

        // Case 3: relative path → build full URL
        // If path already starts with 'storage/' or 'images/', use as-is
        // Otherwise, prepend 'storage/'
        if (str_starts_with($value, 'storage/') || str_starts_with($value, 'images/')) {
            return url($value);
        }
        return url('storage/' . ltrim($value, '/'));
    }
}
