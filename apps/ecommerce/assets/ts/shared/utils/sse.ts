export function createEventSource(hubUrl: string, topic: string): EventSource {
  const url = new URL(hubUrl)
  url.searchParams.append('topic', topic)

  return new EventSource(url.toString())
}
