/**
 * Visitor Tracking Utility
 *
 * This utility provides functions to track user interactions on the website,
 * such as page visits and button clicks.
 */

import axios from 'axios';

/**
 * Check if a URL path is an API endpoint
 *
 * @param path - The URL path to check
 * @returns True if the path is an API endpoint, false otherwise
 */
export const isApiEndpoint = (path: string): boolean => {
  // Skip the tracking endpoint itself
  if (path === '/api/track-action') {
    return false;
  }

  // Consider paths starting with /api/ as API endpoints
  if (path.startsWith('/api/')) {
    return true;
  }

  // Check for API-like routes that don't have the api/ prefix
  // Routes ending with /listes are typically API endpoints
  if (path.endsWith('/listes')) {
    return true;
  }

  // Routes with .json extension
  if (path.endsWith('.json')) {
    return true;
  }

  // Routes that are likely to be API endpoints based on naming conventions
  const apiPatterns = ['/data', '/export', '/import', '/search', '/autocomplete'];
  for (const pattern of apiPatterns) {
    if (path.endsWith(pattern)) {
      return true;
    }
  }

  return false;
};

/**
 * Track a user action
 *
 * @param action - The action performed (e.g., 'click', 'submit')
 * @param details - Additional details about the action
 */
export const trackAction = async (action: string, details?: string): Promise<void> => {
  try {
    // Get the current page path
    const currentPath = window.location.pathname;

    // Skip tracking for API endpoints
    if (isApiEndpoint(currentPath)) {
      return;
    }

    await axios.post('/api/track-action', {
      page_visited: currentPath,
      action,
      action_details: details,
    });
  } catch (error) {
    // Silent fail - don't interrupt user experience if tracking fails
    console.error('Tracking error:', error);
  }
};

/**
 * Track a button click
 *
 * @param buttonId - The ID or name of the button
 * @param buttonText - The text content of the button
 */
export const trackButtonClick = (buttonId: string, buttonText?: string): void => {
  const details = JSON.stringify({
    buttonId,
    buttonText: buttonText || '',
    timestamp: new Date().toISOString(),
  });

  trackAction('button_click', details);
};

/**
 * Track a form submission
 *
 * @param formId - The ID or name of the form
 * @param formData - Optional data about the form submission
 */
export const trackFormSubmit = (formId: string, formData?: Record<string, any>): void => {
  // Filter out sensitive data if formData is provided
  const safeFormData = formData ? Object.keys(formData).reduce((acc, key) => {
    // Don't track passwords, tokens, etc.
    if (!['password', 'token', 'csrf', '_token'].includes(key.toLowerCase())) {
      acc[key] = typeof formData[key] === 'string' ? formData[key] : 'complex-data';
    }
    return acc;
  }, {} as Record<string, any>) : {};

  const details = JSON.stringify({
    formId,
    fields: Object.keys(safeFormData),
    timestamp: new Date().toISOString(),
  });

  trackAction('form_submit', details);
};

/**
 * Initialize global click tracking
 *
 * This function adds a global click event listener to track clicks on elements
 * with data-track-click attribute.
 */
export const initializeClickTracking = (): void => {
  document.addEventListener('click', (event) => {
    const target = event.target as HTMLElement;
    const trackableElement = target.closest('[data-track-click]');

    if (trackableElement) {
      const elementId = trackableElement.id || 'unknown';
      const elementText = trackableElement.textContent?.trim() || '';
      const customData = trackableElement.getAttribute('data-track-data');

      trackButtonClick(elementId, elementText);

      if (customData) {
        try {
          const parsedData = JSON.parse(customData);
          trackAction('custom_interaction', JSON.stringify(parsedData));
        } catch {
          trackAction('custom_interaction', customData);
        }
      }
    }
  });
};

/**
 * Initialize the tracking system
 */
export const initializeTracking = (): void => {
  // Track page view when the tracking system is initialized
  trackAction('page_view');

  // Initialize click tracking
  initializeClickTracking();

  // Track when user leaves the page
  window.addEventListener('beforeunload', () => {
    trackAction('page_exit');
  });
};

export default {
  isApiEndpoint,
  trackAction,
  trackButtonClick,
  trackFormSubmit,
  initializeTracking,
};
