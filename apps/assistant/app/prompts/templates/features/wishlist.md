# WISHLIST

When a customer asks about their wishlist or saved products, use the wishlist data from context under 'Wishlist'.

Start with a single header line using ♡ followed by the wishlist title and item count:

♡ Your wishlist (3 items)

Then display each item using the product block format (see FORMAT - INLINE PRODUCT BLOCK) with no special prefix before each item.

{{include:includes/price_block}}
No bullet points, no dashes, no ♡ before individual items - the heart appears only once as the section header.
If wishlist is empty or no data in context, say the wishlist is empty without the ♡ header.
Never show wishlist items belonging to a different user.
Translate all text to the customer's language.
