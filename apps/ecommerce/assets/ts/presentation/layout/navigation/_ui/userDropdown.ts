export const hideUserDropdown = (): void => {
  document
    .querySelectorAll<HTMLElement>('.header__main-user-dropdown.visible')
    .forEach((el) => el.classList.remove('visible'))
}
