# Roadmap

## Planned

### AI Assistant

- [x] Product comparison via chat
- [x] Personalized recommendations based on order history
- [x] Voice input and text-to-speech responses
- [ ] Voice & Vision customer support - real-time voice and camera support for customer support use cases
  - Real-time voice input/output via Live API (speech-to-speech)
  - Webcam support - customer can show a product or issue visually (e.g. damaged item, wrong delivery)
  - Integrated into existing assistant context and RAG pipeline
- [ ] Interactive quick-reply options - assistant can present checkboxes or radio buttons when asking clarifying questions to narrow down the best product match; user picks options instead of typing
- [x] Test coverage (pytest)

### Ecommerce

- [x] Outbox pattern - guaranteed email delivery via Doctrine transport; messages are stored in DB within the same transaction before being dispatched
- [ ] Multi-currency support - daily ECB exchange rates ([eurofxref-daily.xml](https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml))
- [ ] Additional payment gateways (PayPal, Mollie)
- [ ] Guest checkout - allow orders without registration; guest cart stored in Redis with 7-day TTL; replace the current "you must register" alert with a guest checkout flow
- [ ] Improved checkout flow
- [ ] Promo codes / discount system
  - **Discount type:** fixed (€) or percentage (%)
  - **Validity:** date range (from / to)
  - **Usage limit:** max total uses
  - **Per user:** single-use or multi-use
  - **Min. cart value:** minimum order amount required
  - **Status:** active / inactive
- [ ] Referral system - unique referral code per user shared via URL (`?ref=CODE`); code stored in cookie for 30 days; new user gets a configurable reward (percentage or fixed amount) off their first order on registration; referrer earns a configurable reward per completed order placed by referred users; referral dashboard shows list of referred users and credited amounts
- [ ] Promotional header banner - dismissible (×) banner above the site header promoting a registration discount; hides permanently once dismissed (stored in cookie) or after the user registers; on registration a success modal confirms the discount was applied
- [ ] Cart promo code input - apply a promo code at cart/checkout; list of currently available public codes shown below the input
- [ ] Shipping types and pricing - three tiers with per-country rates shown at checkout
  - **Economy:** SK 3.50 €, EU 5 € - free above 100 € (SK) / 200 € (EU)
  - **Standard:** SK 5 €, EU 10 €
  - **Express:** SK TBD, EU 15 €
  - Outside EU: rates TBD, to be decided later
- [ ] Restrict cash on delivery (COD) to Slovakia only - disable for all other countries
- [ ] Returns and refunds - customers can submit a return request per order; admin can approve or reject it and mark the order as refunded
- [ ] Order status: `uncollected` - package was shipped but customer did not pick it up and it was returned to warehouse; admin can choose next step: re-ship to address or customer picks up in person at a branch
- [ ] Delivery method: personal pickup at branch vs. ship to address - currently only address delivery exists; add pickup option tied to warehouse locations (see Warehouse management)
- [ ] Product listing, filters and detail page improvements
- [ ] User reviews page - list of all reviews the logged-in user has submitted, with product link and option to edit or delete
- [ ] Monthly newsletter - opt-in checkbox on registration; monthly email with new products sent only to subscribed users; unsubscribe link in every email
- [ ] Modular monolith refactor - restructure ecommerce from a single hexagonal DDD app into isolated domain modules (Order, Product, User, Cart, …); each module exposes a public API and communicates via domain events only, with no direct cross-module dependencies; improves readability and enforces boundaries
- [ ] Expand test coverage - PHPUnit across all segments
- [ ] Warehouse management - create and manage multiple warehouses; stock levels and EANs extracted from `ProductVariant` into a dedicated `Warehouse` domain; checkout lets the customer select a pickup warehouse
- [ ] Product parameters / attributes
- [ ] Mobile app via PWA (installable, offline cache)

#### Search (Ecommerce)

- [x] Extended search fields - matches across name, brand, category, type and subtypes with relevance boosting
- [ ] Split search results by entity type - separate ranked sections for products, categories and brands; each section has its own score and result limit
- [ ] Autocomplete - suggest matching products, brands, and categories while the user types
- [x] Typo tolerance - "samsugn" still finds "Samsung"
- [x] Synonyms - "mobile", "phone", and "smartphone" all return the same results
- [ ] Smart boosting - in-stock and bestselling products rank higher; out-of-stock products are excluded from results entirely
- [ ] Zero-results tracking - log searches that return nothing and surface them in the admin so catalog gaps are visible

### Admin Menu (Ecommerce)

- [ ] Product variant management - create, update and delete (list already exists)
- [ ] Product image management - upload and delete variant images (list already exists)
- [ ] Category, type and subtype management - CRUD (currently list-only)
- [ ] Search synonym management - add, edit and remove synonyms without redeploying
- [ ] Promo code management - create, edit and deactivate codes with all discount attributes (type, validity, usage limit, per-user limit, min. cart value, status)
- [ ] Promotional banner management - create and manage header promo banners; configurable message, link, and active/inactive status
- [ ] Referral system management - configure reward amounts (percentage or fixed €) for referrer and new user; view referral list per user by email
- [ ] Shipping configuration - configure rates per tier (Economy / Standard / Express) and per zone (SK / EU / outside EU); set free shipping thresholds
- [ ] Returns and refunds management - list return requests; approve or reject each request and mark order as refunded
- [ ] Warehouse management - CRUD for warehouses
- [ ] Newsletter management - list subscribers, manually trigger newsletter send
- [ ] Guest orders - guest orders visible in the admin order list (no user account linked)
- [ ] User management - filter and ban users (list and order history already exist)
- [ ] Analytics dashboard (sales, revenue, top products)
- [ ] UI improvements and new features
- [ ] Extract Admin into a standalone app
- [ ] Admin role management - introduce granular admin roles (e.g. Editor for products/brands, Support for orders/returns, Moderator for reviews/banners) so different staff members have access only to the areas relevant to their work; role assigned per user in the admin user management panel
- [ ] Admin permissions - each role grants access only to the specific admin areas and actions it needs; unauthorized areas are hidden from the UI and blocked at the API level

### Infrastructure

- [ ] Multi-cluster deployment - split ecommerce and assistant onto separate k3s clusters; assistant cluster runs Ollama on a GPU node for local inference while ecommerce stays on the current ARM node; both clusters share the same Argo CD instance and Helm chart structure
  - Taints & Tolerations - taint the GPU node so only Ollama workloads are scheduled on it; keep ecommerce and assistant API pods on CPU nodes
  - Cilium - replace Flannel with Cilium (eBPF CNI) for L7 network policies, Hubble observability UI and improved cross-node networking performance
  - Longhorn - distributed block storage across nodes so PVCs are not tied to a single node; replaces local-path provisioner
  - MetalLB - bare-metal load balancer to distribute ingress traffic across multiple nodes instead of relying on a single Traefik instance
  - Cluster Autoscaler - automatically provision and remove OCI compute instances based on pending pod demand
  - PodDisruptionBudget - guarantee minimum available replicas during node drain or rolling upgrades so services stay up
- [ ] Packer - build a custom OCI machine image with k3s prerequisites baked in (packages, kernel params, SSH hardening) to reduce node bootstrap time
- [ ] Kubernetes RBAC - define ServiceAccounts and RoleBindings per namespace so each workload has least-privilege access; add dedicated CI/CD service accounts for Argo CD and Jenkins with scoped permissions instead of relying on default service accounts
- [x] Coraza WAF - deploy Coraza as a Traefik middleware plugin with the OWASP Core Rule Set to block SQLi, XSS, path traversal and malicious bots at the ingress level before requests reach the application
- [x] Vault expansion - per-app KV v2 policies, Kubernetes auth roles and ExternalSecret manifests; ESO pulls secrets from Vault into K8s secrets
- [ ] Vault AppRole auth for CI/CD pipelines
- [ ] Vault PKI engine for internal mTLS between services
- [ ] Vault dynamic PostgreSQL credentials with TTL
- [ ] Crossplane - Kubernetes-native infrastructure management; replace or complement Terraform by managing OCI resources (compute, networking, storage) as Kubernetes CRDs; enables GitOps-driven infrastructure alongside ArgoCD
- [ ] Pyroscope - continuous profiling to catch CPU/memory hotspots in production
- [ ] Failure testing - chaos engineering to validate resilience under pod failures, network partitions and slow dependencies
