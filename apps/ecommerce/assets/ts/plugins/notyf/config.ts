import type { NotyfConfig } from '@/ts/plugins/notyf/types'

/** Default Notyf configuration */
const NOTYF_CONFIG: NotyfConfig = {
  position: { x: 'center', y: 'top' },
  dismissible: true,
  ripple: false,
  types: [
    {
      type: 'success',
      duration: 3500,
      background: '#22C55E',
      icon: { className: 'fas fa-check-circle', tagName: 'i' },
    },
    {
      type: 'error',
      duration: 7500,
      background: '#FF4F4F',
      icon: { className: 'fas fa-times-circle', tagName: 'i' },
    },
    {
      type: 'info',
      duration: 5000,
      background: '#3DBBFF',
      icon: { className: 'fas fa-info-circle', tagName: 'i' },
    },
  ],
}

export default NOTYF_CONFIG
