export const storageUrl = (path: string): string => (document.body.dataset.storageUrl ?? '/uploads/') + path
