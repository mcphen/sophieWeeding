/**
 * Vue Directive for Visitor Tracking
 *
 * This directive makes it easy to add tracking to elements in Vue components.
 * Usage:
 * <button v-track="'button_name'">Click Me</button>
 * <button v-track:click="{ id: 'custom_id', data: { key: 'value' } }">Custom Data</button>
 */

import { trackButtonClick, trackAction } from './tracker';
import type { Directive, DirectiveBinding } from 'vue';

interface TrackingOptions {
  id?: string;
  action?: string;
  data?: Record<string, any>;
}

/**
 * Parse the directive value into tracking options
 */
const parseValue = (value: string | TrackingOptions): TrackingOptions => {
  if (typeof value === 'string') {
    return { id: value };
  }
  return value;
};

/**
 * The tracking directive
 */
export const vTrack: Directive = {
  mounted(el: HTMLElement, binding: DirectiveBinding) {
    const options = parseValue(binding.value);
    const eventType = binding.arg || 'click';

    el.setAttribute('data-track-click', 'true');
    if (options.id) {
      el.setAttribute('data-track-id', options.id);
    }

    if (options.data) {
      el.setAttribute('data-track-data', JSON.stringify(options.data));
    }

    el.addEventListener(eventType, () => {
      const elementId = options.id || el.id || 'unnamed_element';
      const elementText = el.textContent?.trim() || '';

      if (options.action) {
        trackAction(options.action, JSON.stringify({
          elementId,
          elementText,
          data: options.data || {},
        }));
      } else {
        trackButtonClick(elementId, elementText);
      }
    });
  }
};

export default vTrack;
