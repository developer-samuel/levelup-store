import debounce from '@/ts/shared/utils/debounce'

export const hideUserDropdown = debounce((): void => {
  document
    .querySelectorAll<HTMLElement>('.header__main-user-dropdown.visible')
    .forEach((el) => el.classList.remove('visible'))
}, 750)
