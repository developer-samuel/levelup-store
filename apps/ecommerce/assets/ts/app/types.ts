export type AppModule = {
  selector: string
  module: () => Promise<unknown>
}
