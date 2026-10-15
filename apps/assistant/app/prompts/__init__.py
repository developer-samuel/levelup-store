from app.config import settings as _settings
from app.prompts.blocked import BLOCKED_PATTERNS
from app.prompts.loader import load as _load

__all__ = ["CHAT_SYSTEM_PROMPT", "BLOCKED_PATTERNS"]

CHAT_SYSTEM_PROMPT = "\n\n".join(
    [
        # rules
        _load("rules", "base"),
        _load("rules", "greeting"),
        _load("rules", "scope"),
        _load("rules", "tone"),
        _load("rules", "emojis"),
        _load("rules", "language"),
        _load("rules", "answers"),
        _load("rules", "out_of_stock"),
        _load("rules", "upsell"),
        _load("rules", "behavior"),
        _load("rules", "unknown", support_email=_settings.support_email),
        # format
        _load("format", "product_block"),
        _load("format", "listing"),
        _load("format", "comparison"),
        # features
        _load("features", "product_compare_suggestion"),
        _load("features", "product_quiz"),
        _load("features", "product_upgrade"),
        _load("features", "promo_banner", ecommerce_url=_settings.ecommerce_url),
        _load("features", "order_tracking", ecommerce_url=_settings.ecommerce_url),
        _load("features", "warranty", support_email=_settings.support_email),
        _load("features", "shipping"),
        _load("features", "payment"),
        _load("features", "return_policy", support_email=_settings.support_email),
        _load("features", "product_availability"),
        _load("features", "cart"),
        _load("features", "my_reviews"),
        _load("features", "wishlist"),
        # constraints
        _load("constraints", "demo_warning"),
        _load("constraints", "response_length"),
        _load("constraints", "confidence"),
        # escalation
        _load("escalation", "human_handoff", support_email=_settings.support_email),
        _load("escalation", "complaint_handling"),
        # context
        _load("context", "products"),
        _load("context", "documents"),
    ]
)
