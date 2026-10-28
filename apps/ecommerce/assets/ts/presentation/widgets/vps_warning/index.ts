import { bindVpsWarning } from '@/ts/presentation/widgets/vps_warning/_handlers/vpsWarning'

const trigger = document.getElementById('vps-warning-trigger')
const modal = document.getElementById('vps-warning-modal')

if (trigger && modal) {
  bindVpsWarning(trigger, modal)
}
