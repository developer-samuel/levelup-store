import { createFormAlertHandler } from '@/ts/shared/elements/form/_factory/formHandlerFactory'

import profileSubmit from '@/ts/features/users/profile/_services/profileService'

export const handleProfileFormSubmit = createFormAlertHandler(profileSubmit)
