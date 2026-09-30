/** Timeout ID returned by setTimeout */
export type TimeoutId = ReturnType<typeof setTimeout>

/** DOM element list types */
export type HtmlElList = NodeListOf<HTMLElement>
export type HtmlInputList = NodeListOf<HTMLInputElement>

/** Common record types */
export type UnknownRecord = Record<string, unknown>
export type StringRecord = Record<string, string>
export type StringListRecord = Record<string, string[]>
