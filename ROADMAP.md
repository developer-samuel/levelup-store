# Roadmap

## Planned

### AI Assistant

- [ ] Semantic product search via assistant
- [ ] Product comparison via chat
- [ ] Personalized recommendations based on order history
- [ ] Voice input / text-to-speech responses
- [ ] Test coverage (pytest, pytest-asyncio, httpx)

### Ecommerce

- [ ] Multi-currency support - daily ECB exchange rates ([eurofxref-daily.xml](https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml))
- [ ] Additional payment gateways (PayPal, Mollie)
- [ ] Promo codes / discount system
- [ ] Product parameters / attributes
- [ ] Product listing, filters and detail page improvements
- [ ] Improved checkout flow
- [ ] Monthly newsletter with new products
- [ ] Expand test coverage - PHPUnit across all segments
- [ ] Mobile app via PWA (installable, offline cache)

#### Search (Ecommerce)

- [ ] Split search results by entity type - a single query returns separate ranked sections for products (matched on name, description, brand, category), categories, and brands; each section has its own score and result limit
- [ ] Autocomplete - suggest matching products, brands, and categories while the user types
- [ ] Typo tolerance - "samsugn" still finds "Samsung"
- [ ] Synonyms - "mobile", "phone", and "smartphone" all return the same results
- [ ] Smart boosting - in-stock and bestselling products rank higher; out-of-stock products are excluded from results entirely
- [ ] Zero-results tracking - log searches that return nothing and surface them in the admin so catalog gaps are visible

### Admin Menu (Ecommerce)

- [ ] Analytics dashboard (sales, revenue, top products)
- [ ] UI improvements and new features
- [ ] Extract Admin into a standalone app

### Infrastructure

- [ ] Pyroscope - continuous profiling to catch CPU/memory hotspots in production
- [ ] Failure testing - chaos engineering to validate resilience under pod failures, network partitions and slow dependencies
