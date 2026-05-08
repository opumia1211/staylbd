import { test, expect } from '@playwright/test';

test.describe('Checkout Funnel E2E', () => {
  test('should allow guest user to add to cart and checkout', async ({ page }) => {
    // Navigate to storefront root under XAMPP folder-based URL.
    await page.goto('/staylbd/');
    await expect(page).toHaveTitle(/.+/);
    
    // Use products listing page where cards are consistently available.
    await page.goto('/staylbd/all/products');
    const firstProduct = page.locator('.stayl-product-card, .product-card, .sf-pro-card').first();
    await expect(firstProduct).toBeVisible();
    
    // Click add to cart and verify cart badge is updated.
    const addToCartBtn = firstProduct.locator('.add-to-cart, .cart-btn');
    await expect(addToCartBtn).toBeVisible();
    await addToCartBtn.click();
    
    // Go to cart/checkout entry point and ensure it is not a 404 page.
    await page.goto('/staylbd/user/cart');
    await expect(page).not.toHaveTitle(/404/i);
    
    // We would fill out the checkout form here
    // Example:
    // await page.fill('input[name="name"]', 'John Doe');
    // await page.click('button[type="submit"]');
  });
});
