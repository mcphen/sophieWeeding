// Utility functions to handle image paths in different environments

/**
 * Get the correct URL for an image path based on the current environment
 * @param path The image path
 * @returns The correct URL for the current environment
 */
export function getImageUrl(path: string | null): string | null {
  if (!path) {
    return null;
  }

  // If the path is already a data URL (base64), return it as is
  if (path.startsWith('data:')) {
    return path;
  }

  // If the path is already an absolute URL, return it as is
  if (path.startsWith('http://') || path.startsWith('https://')) {
    return path;
  }

  // Check if we're in production environment
  // This is a frontend utility, so we need to determine the environment differently
  // We can check the current URL to determine if we're in production
  const isProduction = window.location.hostname !== 'localhost' &&
                      !window.location.hostname.includes('127.0.0.1');

  // Clean the path
  const cleanPath = path.startsWith('/') ? path.substring(1) : path;

  // In production, prepend sophieWeeding/public/storage
  if (isProduction) {
    return `/sophieWeeding/public/storage/${cleanPath}`;
  }

  // In development, just prepend /storage
  return `/storage/${cleanPath}`;
}
