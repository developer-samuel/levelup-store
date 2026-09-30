/** Show alert in the DOM with success or error styling */
export function show(
  alertElement: HTMLElement,
  alertBody: HTMLElement,
  message: string,
  successClass: string,
  errorClass: string,
  success: boolean,
): void {
  alertBody.textContent = message
  alertElement.className = `alert ${success ? successClass : errorClass} alert--visible`
}

/** Hide alert from the DOM */
export function hide(
  alertElement: HTMLElement,
  alertBody: HTMLElement,
  successClass: string,
  errorClass: string,
): void {
  alertElement.classList.remove('alert--visible', successClass, errorClass)
  alertBody.textContent = ''
}
