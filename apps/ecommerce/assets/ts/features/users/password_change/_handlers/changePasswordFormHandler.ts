import { createFormAlertHandler } from '@/ts/shared/elements/form/_factory/formHandlerFactory'

import changePasswordSubmit from '@/ts/features/users/password_change/_services/changePasswordService'

export const handleChangePasswordFormSubmit = createFormAlertHandler(changePasswordSubmit)
