# 🛡️ StayLBD Project: Comprehensive Structure & Features Report

This document provides a deep-dive analysis of the **StayLBD** e-commerce platform. It outlines the project's technical architecture, the structure of all public-facing pages (from Home to Checkout), and the detailed features available in the User Profile area.

---

## 📂 1. Technical Architecture & Project Structure

The project is built on the **Laravel** framework with a highly optimized frontend using **Tailwind CSS** and **Vanilla JS/Alpine.js**.

### Core Directories
- **`core/app/`**: Contains the backend logic (Controllers, Models, Middlewares).
- **`core/resources/views/templates/basic/`**: The primary location for frontend Blade templates.
- **`core/routes/`**: Route definitions (`web.php` for public, `user.php` for customers, `admin.php` for management).
- **`core/public/`**: Assets like images, compiled CSS, and JS (though many are served dynamically via `ServeAssetController`).
- **`core/database/migrations/`**: Database schema definitions.

---

## 🌐 2. Public Pages Roadmap (Home to Checkout)

All public pages are designed for speed and conversion, utilizing **Laravel Cache** and **AJAX** for smooth interactions.

### A. Home Page
- **Template Path**: `core/resources/views/templates/basic/home.blade.php`
- **Controller**: `SiteController@index`
- **Key Features**:
    - **Dynamic Banner System**: Targeted visibility (Guest/User/Campaign) with analytics tracking.
    - **Sectional Content**: "Today's Deals", "Hot Deals", "Best Selling", and "New Arrivals".
    - **Below-the-Fold Lazy Loading**: Uses AJAX (`homeBelowFoldFragment`) to load content as users scroll, reducing initial load time.
    - **Global Search**: Real-time suggestions, voice search, and image search capability.
    - **Social Proof Widgets**: Real-time poll showing view counts for products in the last 24 hours.

### B. Product Listing & Filtering
- **Template Path**: `core/resources/views/templates/basic/products/index.blade.php`
- **Controller**: `SiteController@products` / `SiteController@filterProduct`
- **Key Features**:
    - **Advanced Sidebar Filters**: Filter by Categories, Brands, Price (Range Slider), and Specifications.
    - **Quick View**: Modal-based product preview without navigating away from the list.
    - **AJAX Pagination**: Seamless infinite scroll or "Load More" functionality.

### C. Product Details Page
- **Template Path**: `core/resources/views/templates/basic/products/details.blade.php`
- **Controller**: `SiteController@productDetail`
- **Key Features**:
    - **Variant Selection**: Dynamic price and SKU updates based on color/size/attribute selection.
    - **Review System**: Five-star rating breakdown, helpfulness voting, and AJAX-loaded reviews.
    - **Smart Recommendations**: "Related Products" (Category-based), "Same Brand", and "You May Also Like" (Trending).
    - **Recently Viewed**: Persistent tracking of viewed products stored in local cookies.

### D. Cart Management
- **Template Path**: `core/resources/views/templates/basic/cart.blade.php`
- **Controller**: `CartController`
- **Key Features**:
    - **Real-time AJAX Updates**: Change quantities or remove items without page reloads.
    - **Coupon Engine**: Instant discount calculation based on promo codes.
    - **Selection Sync**: Choose specific items from the cart to proceed to checkout.

### E. The Checkout Process (Guest & User)
- **Template Paths**: 
    - `core/resources/views/templates/basic/checkout.blade.php` (User)
    - `core/resources/views/templates/basic/guest_order.blade.php` (Guest)
- **Controller**: `CheckoutController` / `GuestCheckoutController`
- **Flow Details**:
    1. **Location Selection**: Interactive selection for Shipping zones.
    2. **Shipping Options**: Dynamic list of available couriers and costs.
    3. **Payment Selection**: Support for automated gateways and manual bank transfers.
    4. **Order Summary**: Real-time breakdown of Subtotal, Tax, Shipping, and Discounts.

---

## 👤 3. User Profile & Account Management

Once logged in, users have access to a robust dashboard to manage their shopping journey.

### A. Dashboard Overview
- **Path**: `core/resources/views/templates/basic/user/dashboard.blade.php`
- **Features**: Visual summary of total orders, wishlisted items, and recent notifications.

### B. Profile & Address Management
- **Path**: `core/resources/views/templates/basic/user/profile_setting.blade.php`
- **Features**:
    - **Personal Info**: Manage Name, Email, and Phone.
    - **Address Book**: Save multiple shipping addresses with a "Set as Default" feature.
    - **Two-Factor Security**: Google Authenticator (G2FA) support.

### C. Order Tracking & History
- **Path**: `core/resources/views/templates/basic/user/order/`
- **Features**: 
    - List of all past and current orders.
    - **Live Order Tracking**: Real-time status updates (Processing, Shipped, Delivered).
    - **Digital Downloads**: Access to purchased digital products/files.

### D. Support & Communication
- **Path**: `core/resources/views/templates/basic/user/support/`
- **Features**:
    - **Ticket System**: Create and manage support requests with attachments.
    - **Live Chat Panel**: Floating persistent chat widget for instant help.

---

## 🛠️ 4. Technical "Under the Hood" (Structure & Logic)

| Feature | Implementation | Location |
| :--- | :--- | :--- |
| **Styling** | Tailwind CSS (Modular Configs) | `core/tailwind.config.js` |
| **Routing** | Laravel Route Groups | `core/routes/web.php` |
| **Asset Delivery** | `ServeAssetController` | `core/app/Http/Controllers/ServeAssetController.php` |
| **Social Proof** | Real-time Poll System | `core/app/Http/Controllers/Storefront/RealtimePollController.php` |
| **Translation** | Server-side `__('')` + Google Widget | `core/lang/` + Header Widget |
| **SEO** | Dynamic Meta Tags & JSON-LD | `core/app/Http/Controllers/SeoController.php` |

---

## 📑 5. List of All Public Page Templates

1.  **Home**: `home.blade.php`
2.  **Category Browse**: `all_category.blade.php`
3.  **Brand Browse**: `all_brands.blade.php`
4.  **Product Detail**: `products/details.blade.php`
5.  **Search Results**: `products/index.blade.php`
6.  **Cart**: `cart.blade.php`
7.  **Checkout**: `checkout.blade.php`
8.  **Track Order**: `track/track_order.blade.php`
9.  **Contact Us**: `contact.blade.php`
10. **Policy Pages**: `policy.blade.php`

---
*Report generated by Antigravity AI Assistant.*
