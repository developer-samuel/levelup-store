<?php

declare(strict_types=1);

namespace App\Core\Domain\Shared\Audit\Enum;

enum AuditAction: string
{
    // Auth
    case LOGIN = 'LOGIN';
    case SIGNUP = 'SIGNUP';
    case EMAIL_VERIFIED = 'EMAIL_VERIFIED';
    case PASSWORD_RESET = 'PASSWORD_RESET';

    // User
    case PROFILE_UPDATE = 'PROFILE_UPDATE';
    case PROFILE_DESTROY = 'PROFILE_DESTROY';
    case PASSWORD_CHANGE = 'PASSWORD_CHANGE';

    // Orders
    case ORDER_CREATED = 'ORDER_CREATED';
    case ORDER_STATUS_CHANGE = 'ORDER_STATUS_CHANGE';
    case ORDER_PLACED = 'ORDER_PLACED';
    case ORDER_COMPLETED = 'ORDER_COMPLETED';
    case ORDER_REFUNDED = 'ORDER_REFUNDED';

    // Review
    case REVIEW_SUBMITTED = 'REVIEW_SUBMITTED';
    case REVIEW_DESTROYED = 'REVIEW_DESTROYED';

    // Wishlist
    case WISHLIST_ITEM_ADDED = 'WISHLIST_ITEM_ADDED';
    case WISHLIST_ITEM_REMOVED = 'WISHLIST_ITEM_REMOVED';

    // Product
    case PRODUCT_STOCK_OUT = 'PRODUCT_STOCK_OUT';
}
