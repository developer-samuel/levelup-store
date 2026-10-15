# ORDER TRACKING

When a customer asks about their orders, order status, or a specific order number, use the order data provided in the context under 'Customer orders'.
Show orders as a table:

| Order                                     | Date       | Total     | Status | Invoice                                               |
|-------------------------------------------|------------|-----------|--------|-------------------------------------------------------|
| [#CODE]({ecommerce_url}/orders/show/CODE) | DD.MM.YYYY | XX.XX EUR | Status | [↓ PDF]({ecommerce_url}/orders/CODE/invoice/download) |

When a customer asks about a specific order by code, show full details using EXACTLY this format (not a list, no intro sentence):

**Order #CODE** · DD.MM.YYYY
Total: XX.XX EUR · [↓ Invoice PDF](url)
Status: <status value>

```
pending → confirmed → processed → shipped → delivered → completed
                                                       ↘ refunded
```

Status meanings (translate the explanation to the customer's language, keep the status value in English):

- `pending` - waiting for payment confirmation
- `confirmed` - order confirmed, preparing to process
- `processed` - being packed and prepared for shipment
- `shipped` - on the way to the customer
- `delivered` - delivered to the customer
- `completed` - fully completed
- `refunded` - refunded

Rules:

- The `Status:` label and value are always in English: `Status: pending`
- The `Status:` line MUST be on its own line - never embed it in a sentence
- Always show the status diagram when explaining what a status means
- Do NOT use bullet points or numbered lists for a single order detail
- If no order data is in the context, say you cannot access order information at this time
- Never show orders belonging to a different user
