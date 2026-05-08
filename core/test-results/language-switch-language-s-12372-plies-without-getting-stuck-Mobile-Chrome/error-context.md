# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: language-switch.spec.ts >> language switch applies without getting stuck
- Location: tests\e2e\language-switch.spec.ts:15:5

# Error details

```
Test timeout of 30000ms exceeded.
```

```
Error: locator.click: Test timeout of 30000ms exceeded.
Call log:
  - waiting for locator('.stayl-topbar-menu__btn:has(#staylCurrentLanguageLabel)').first()
    - locator resolved to <button type="button" class="stayl-topbar-menu__btn">…</button>
  - attempting click action
    2 × waiting for element to be visible, enabled and stable
      - element is visible, enabled and stable
      - scrolling into view if needed
      - done scrolling
      - element is outside of the viewport
    - retrying click action
    - waiting 20ms
    2 × waiting for element to be visible, enabled and stable
      - element is visible, enabled and stable
      - scrolling into view if needed
      - done scrolling
      - element is outside of the viewport
    - retrying click action
      - waiting 100ms
    32 × waiting for element to be visible, enabled and stable
       - element is visible, enabled and stable
       - scrolling into view if needed
       - done scrolling
       - element is outside of the viewport
     - retrying click action
       - waiting 500ms

```

# Page snapshot

```yaml
- generic [active] [ref=e1]:
  - banner [ref=e2]:
    - generic [ref=e4]:
      - generic [ref=e5]:
        - link "888-777-999" [ref=e6] [cursor=pointer]:
          - /url: tel:888777999
          - img [ref=e7]
          - generic [ref=e9]: 888-777-999
        - link "support@staylbd.com" [ref=e10] [cursor=pointer]:
          - /url: mailto:support@staylbd.com
          - img [ref=e11]
          - generic [ref=e14]: support@staylbd.com
        - generic [ref=e15]:
          - img [ref=e16]
          - generic [ref=e21]: Cash on Delivery available nationwide opu
      - generic [ref=e22]:
        - generic "Bangladesh, Dhaka" [ref=e24]:
          - img [ref=e26]
          - generic [ref=e29]: Bangladesh, Joar Sahara
        - generic [ref=e31]:
          - img [ref=e33]
          - generic [ref=e37]:
            - img [ref=e38]
            - text: "Today High: 27°C | Low: 22°C"
      - generic [ref=e40]:
        - button "EN" [ref=e42] [cursor=pointer]:
          - generic [ref=e44]: EN
          - img [ref=e45]
        - button "BDT" [ref=e48] [cursor=pointer]:
          - generic [ref=e50]: BDT
          - img [ref=e51]
        - link "Registration" [ref=e53] [cursor=pointer]:
          - /url: http://localhost/staylbd/user/register
        - link "BECOME A SELLER" [ref=e54] [cursor=pointer]:
          - /url: http://localhost/staylbd/seller/apply
    - generic [ref=e56]:
      - link "Staylbd" [ref=e58] [cursor=pointer]:
        - /url: http://localhost/staylbd
        - img "Staylbd" [ref=e59]
      - generic [ref=e61]:
        - generic [ref=e62]:
          - textbox "Search for products, brands and more..." [ref=e63]
          - button "Search" [ref=e64] [cursor=pointer]:
            - img [ref=e65]
          - button "Voice Search" [ref=e68] [cursor=pointer]:
            - img [ref=e69]
        - button "Camera Search" [ref=e72] [cursor=pointer]:
          - img [ref=e73]
      - generic [ref=e76]:
        - link "Orders" [ref=e77] [cursor=pointer]:
          - /url: http://localhost/staylbd/user/order
          - img [ref=e78]
        - link "Track Order" [ref=e81] [cursor=pointer]:
          - /url: http://localhost/staylbd/user/ordertrack
          - img [ref=e82]
        - link "0" [ref=e87] [cursor=pointer]:
          - /url: http://localhost/staylbd/user/wishlist
          - img [ref=e88]
          - generic [ref=e90]: "0"
        - link "0" [ref=e91] [cursor=pointer]:
          - /url: http://localhost/staylbd/user/cart
          - img [ref=e92]
          - generic [ref=e96]: "0"
        - link "Login" [ref=e97] [cursor=pointer]:
          - /url: http://localhost/staylbd/user/dashboard
          - img [ref=e98]
          - generic: Login
    - navigation [ref=e103]:
      - generic "Open Menu" [ref=e104] [cursor=pointer]:
        - img [ref=e105]
      - button "ALL CATEGORIES" [ref=e107] [cursor=pointer]:
        - generic [ref=e108]: ALL CATEGORIES
        - img [ref=e109]
      - list [ref=e111]:
        - listitem [ref=e112]:
          - link "Homepage" [ref=e113] [cursor=pointer]:
            - /url: /
        - listitem [ref=e114]:
          - link "Shop Products" [ref=e115] [cursor=pointer]:
            - /url: /products
        - listitem [ref=e116]:
          - link "Pages" [ref=e117] [cursor=pointer]:
            - /url: "#"
            - text: Pages
            - img [ref=e118]
        - listitem [ref=e120]:
          - link "About Us" [ref=e121] [cursor=pointer]:
            - /url: "#"
        - listitem [ref=e122]:
          - link "Latest Blog" [ref=e123] [cursor=pointer]:
            - /url: "#"
        - listitem [ref=e124]:
          - link "Contact Us" [ref=e125] [cursor=pointer]:
            - /url: /contact
  - main [ref=e126]:
    - generic [ref=e127]:
      - region "Banner" [ref=e128]:
        - link [ref=e132] [cursor=pointer]:
          - /url: "#"
      - generic [ref=e141]:
        - link "Gift Voucher Gift Voucher Aliquam eleifend in elit congue" [ref=e142] [cursor=pointer]:
          - /url: javascript:void(0)
          - img "Gift Voucher" [ref=e144]
          - generic [ref=e145]:
            - heading "Gift Voucher" [level=5] [ref=e146]
            - paragraph [ref=e147]: Aliquam eleifend in elit congue
        - link "Online Support 24/7 Online Support 24/7 Aliquam eleifend in elit congue" [ref=e148] [cursor=pointer]:
          - /url: javascript:void(0)
          - img "Online Support 24/7" [ref=e150]
          - generic [ref=e151]:
            - heading "Online Support 24/7" [level=5] [ref=e152]
            - paragraph [ref=e153]: Aliquam eleifend in elit congue
        - link "Money Back Guarantee Money Back Guarantee Aliquam eleifend in elit congue" [ref=e154] [cursor=pointer]:
          - /url: javascript:void(0)
          - img "Money Back Guarantee" [ref=e156]
          - generic [ref=e157]:
            - heading "Money Back Guarantee" [level=5] [ref=e158]
            - paragraph [ref=e159]: Aliquam eleifend in elit congue
        - link "Free Shipping Free Shipping Aliquam eleifend in elit congue" [ref=e160] [cursor=pointer]:
          - /url: javascript:void(0)
          - img "Free Shipping" [ref=e162]
          - generic [ref=e163]:
            - heading "Free Shipping" [level=5] [ref=e164]
            - paragraph [ref=e165]: Aliquam eleifend in elit congue
      - region "Category" [ref=e168]:
        - generic [ref=e169]:
          - img [ref=e171]
          - heading "Category row" [level=3] [ref=e176]
        - generic [ref=e177]:
          - link "9h 9h" [ref=e179] [cursor=pointer]:
            - /url: http://localhost/staylbd/category/product/9h/13
            - img "9h" [ref=e181]
            - heading "9h" [level=4] [ref=e182]
          - link "8h 8h" [ref=e184] [cursor=pointer]:
            - /url: http://localhost/staylbd/category/product/8h/12
            - img "8h" [ref=e186]
            - heading "8h" [level=4] [ref=e187]
          - link "7h 7h" [ref=e189] [cursor=pointer]:
            - /url: http://localhost/staylbd/category/product/7h/11
            - img "7h" [ref=e191]
            - heading "7h" [level=4] [ref=e192]
          - link "6h 6h" [ref=e194] [cursor=pointer]:
            - /url: http://localhost/staylbd/category/product/6h/10
            - img "6h" [ref=e196]
            - heading "6h" [level=4] [ref=e197]
          - link "5h 5h" [ref=e199] [cursor=pointer]:
            - /url: http://localhost/staylbd/category/product/5h/9
            - img "5h" [ref=e201]
            - heading "5h" [level=4] [ref=e202]
          - link "3h 3h" [ref=e204] [cursor=pointer]:
            - /url: http://localhost/staylbd/category/product/3h/8
            - img "3h" [ref=e206]
            - heading "3h" [level=4] [ref=e207]
          - link "2h 2h" [ref=e209] [cursor=pointer]:
            - /url: http://localhost/staylbd/category/product/2h/7
            - img "2h" [ref=e211]
            - heading "2h" [level=4] [ref=e212]
          - link "1h 1h" [ref=e214] [cursor=pointer]:
            - /url: http://localhost/staylbd/category/product/1h/6
            - img "1h" [ref=e216]
            - heading "1h" [level=4] [ref=e217]
          - link "ab ab" [ref=e219] [cursor=pointer]:
            - /url: http://localhost/staylbd/category/product/ab/5
            - img "ab" [ref=e221]
            - heading "ab" [level=4] [ref=e222]
          - link "RIAZUL ISLAM SHOJOL RIAZUL ISLAM SHOJOL" [ref=e224] [cursor=pointer]:
            - /url: http://localhost/staylbd/category/product/riazul-islam-shojol/4
            - img "RIAZUL ISLAM SHOJOL" [ref=e226]
            - heading "RIAZUL ISLAM SHOJOL" [level=4] [ref=e227]
          - link "Affordable Custom Cricket Jersey With Sublimation Printing Affordable Custom Cricket Jersey With Sublimation Printing" [ref=e229] [cursor=pointer]:
            - /url: http://localhost/staylbd/category/product/affordable-custom-cricket-jersey-with-sublimation-printing/3
            - img "Affordable Custom Cricket Jersey With Sublimation Printing" [ref=e231]
            - heading "Affordable Custom Cricket Jersey With Sublimation Printing" [level=4] [ref=e232]
          - link "T-shirt, টি-শার্ট T-shirt, টি-শার্ট" [ref=e234] [cursor=pointer]:
            - /url: http://localhost/staylbd/category/product/t-shirt-ti-sart/2
            - img "T-shirt, টি-শার্ট" [ref=e236]
            - heading "T-shirt, টি-শার্ট" [level=4] [ref=e237]
          - link "WinTerSMM WinTerSMM" [ref=e239] [cursor=pointer]:
            - /url: http://localhost/staylbd/category/product/wintersmm/1
            - generic [ref=e240]:
              - img "WinTerSMM"
            - heading "WinTerSMM" [level=4] [ref=e241]
      - generic [ref=e242]:
        - generic [ref=e243]:
          - heading "Quick Deals" [level=2] [ref=e244]:
            - img [ref=e245]
            - generic [ref=e247]: Quick Deals
          - link "View All" [ref=e248] [cursor=pointer]:
            - /url: http://localhost/staylbd/product/hot-deal
            - text: View All
            - img [ref=e250]
        - generic [ref=e256]:
          - generic [ref=e257]:
            - button "Wishlist" [ref=e258] [cursor=pointer]:
              - img [ref=e259]
            - button "Quick View" [ref=e261] [cursor=pointer]:
              - img [ref=e262]
            - button "Compare" [ref=e265] [cursor=pointer]:
              - img [ref=e266]
          - link "RIAZUL ISLAM SHOJOL" [ref=e268] [cursor=pointer]:
            - /url: http://localhost/staylbd/product/riazul-islam-1
            - img "RIAZUL ISLAM SHOJOL" [ref=e270]
          - generic [ref=e271]:
            - generic [ref=e273]: WinTerSMM
            - heading "RIAZUL ISLAM SHOJOL" [level=3] [ref=e274]:
              - link "RIAZUL ISLAM SHOJOL" [ref=e275] [cursor=pointer]:
                - /url: http://localhost/staylbd/product/riazul-islam-1
            - generic [ref=e277]: In Stock
            - generic [ref=e278]:
              - generic [ref=e280]: ৳100.00
              - button "Add to Cart" [ref=e281] [cursor=pointer]:
                - img [ref=e282]
      - generic "vb" [ref=e288]:
        - img "vb" [ref=e289]
      - generic [ref=e290]:
        - generic [ref=e291]:
          - heading "Hot Deals" [level=2] [ref=e292]:
            - img [ref=e293]
            - text: Hot Deals
          - link "View All" [ref=e295] [cursor=pointer]:
            - /url: http://localhost/staylbd/product/hot-deal
            - text: View All
            - img [ref=e297]
        - generic [ref=e301]:
          - generic [ref=e303]:
            - generic [ref=e305]: "-5% OFF"
            - generic [ref=e306]:
              - button "Wishlist" [ref=e307] [cursor=pointer]:
                - img [ref=e308]
              - button "Quick View" [ref=e310] [cursor=pointer]:
                - img [ref=e311]
              - button "Compare" [ref=e314] [cursor=pointer]:
                - img [ref=e315]
            - link "Affordable Custom Cricket Jersey With Sublimation Printing" [ref=e317] [cursor=pointer]:
              - /url: http://localhost/staylbd/product/affordable-custom-5
              - img "Affordable Custom Cricket Jersey With Sublimation Printing" [ref=e319]
            - generic [ref=e320]:
              - generic [ref=e322]: WinTerSMM
              - heading "Affordable Custom Cricket Jersey With Sublimation Printing" [level=3] [ref=e323]:
                - link "Affordable Custom Cricket Jersey With Sublimation Printing" [ref=e324] [cursor=pointer]:
                  - /url: http://localhost/staylbd/product/affordable-custom-5
              - generic [ref=e326]: In Stock
              - generic [ref=e327]:
                - generic [ref=e328]:
                  - generic [ref=e329]: ৳5700.00
                  - generic [ref=e330]: ৳5700.00
                - button "Add to Cart" [ref=e331] [cursor=pointer]:
                  - img [ref=e332]
          - generic [ref=e336]:
            - generic [ref=e338]: "-5% OFF"
            - generic [ref=e339]:
              - button "Wishlist" [ref=e340] [cursor=pointer]:
                - img [ref=e341]
              - button "Quick View" [ref=e343] [cursor=pointer]:
                - img [ref=e344]
              - button "Compare" [ref=e347] [cursor=pointer]:
                - img [ref=e348]
            - link "Affordable Custom Cricket Jersey With Sublimation Printing" [ref=e350] [cursor=pointer]:
              - /url: http://localhost/staylbd/product/affordable-custom-4
              - img "Affordable Custom Cricket Jersey With Sublimation Printing" [ref=e352]
            - generic [ref=e353]:
              - generic [ref=e355]: WinTerSMM
              - heading "Affordable Custom Cricket Jersey With Sublimation Printing" [level=3] [ref=e356]:
                - link "Affordable Custom Cricket Jersey With Sublimation Printing" [ref=e357] [cursor=pointer]:
                  - /url: http://localhost/staylbd/product/affordable-custom-4
              - generic [ref=e359]: In Stock
              - generic [ref=e360]:
                - generic [ref=e361]:
                  - generic [ref=e362]: ৳4275.00
                  - generic [ref=e363]: ৳4275.00
                - button "Add to Cart" [ref=e364] [cursor=pointer]:
                  - img [ref=e365]
          - generic [ref=e369]:
            - generic [ref=e371]: "-5% OFF"
            - generic [ref=e372]:
              - button "Wishlist" [ref=e373] [cursor=pointer]:
                - img [ref=e374]
              - button "Quick View" [ref=e376] [cursor=pointer]:
                - img [ref=e377]
              - button "Compare" [ref=e380] [cursor=pointer]:
                - img [ref=e381]
            - link "Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing" [ref=e383] [cursor=pointer]:
              - /url: http://localhost/staylbd/product/cricket-jersey-3
              - img "Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing" [ref=e385]
            - generic [ref=e386]:
              - generic [ref=e388]: WinTerSMM
              - heading "Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing" [level=3] [ref=e389]:
                - link "Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing" [ref=e390] [cursor=pointer]:
                  - /url: http://localhost/staylbd/product/cricket-jersey-3
              - generic [ref=e392]: In Stock
              - generic [ref=e393]:
                - generic [ref=e394]:
                  - generic [ref=e395]: ৳3894.05
                  - generic [ref=e396]: ৳3894.05
                - button "Add to Cart" [ref=e397] [cursor=pointer]:
                  - img [ref=e398]
          - generic [ref=e402]:
            - generic [ref=e404]: "-50% OFF"
            - generic [ref=e405]:
              - button "Wishlist" [ref=e406] [cursor=pointer]:
                - img [ref=e407]
              - button "Quick View" [ref=e409] [cursor=pointer]:
                - img [ref=e410]
              - button "Compare" [ref=e413] [cursor=pointer]:
                - img [ref=e414]
            - link "WinTerSMM" [ref=e416] [cursor=pointer]:
              - /url: http://localhost/staylbd/product/wintersmm-2
              - img "WinTerSMM" [ref=e418]
            - generic [ref=e419]:
              - generic [ref=e421]: WinTerSMM
              - heading "WinTerSMM" [level=3] [ref=e422]:
                - link "WinTerSMM" [ref=e423] [cursor=pointer]:
                  - /url: http://localhost/staylbd/product/wintersmm-2
              - generic [ref=e425]: In Stock
              - generic [ref=e426]:
                - generic [ref=e427]:
                  - generic [ref=e428]: ৳50.00
                  - generic [ref=e429]: ৳50.00
                - button "Add to Cart" [ref=e430] [cursor=pointer]:
                  - img [ref=e431]
      - generic [ref=e434]:
        - generic [ref=e435]:
          - heading "Featured Products" [level=2] [ref=e436]:
            - img [ref=e437]
            - text: Featured Products
          - link "View All" [ref=e439] [cursor=pointer]:
            - /url: http://localhost/staylbd/product/featured
            - text: View All
            - img [ref=e441]
        - generic [ref=e445]:
          - generic [ref=e447]:
            - generic [ref=e449]: "-5% OFF"
            - generic [ref=e450]:
              - button "Wishlist" [ref=e451] [cursor=pointer]:
                - img [ref=e452]
              - button "Quick View" [ref=e454] [cursor=pointer]:
                - img [ref=e455]
              - button "Compare" [ref=e458] [cursor=pointer]:
                - img [ref=e459]
            - link "Affordable Custom Cricket Jersey With Sublimation Printing" [ref=e461] [cursor=pointer]:
              - /url: http://localhost/staylbd/product/affordable-custom-5
              - img "Affordable Custom Cricket Jersey With Sublimation Printing" [ref=e463]
            - generic [ref=e464]:
              - generic [ref=e466]: WinTerSMM
              - heading "Affordable Custom Cricket Jersey With Sublimation Printing" [level=3] [ref=e467]:
                - link "Affordable Custom Cricket Jersey With Sublimation Printing" [ref=e468] [cursor=pointer]:
                  - /url: http://localhost/staylbd/product/affordable-custom-5
              - generic [ref=e470]: In Stock
              - generic [ref=e471]:
                - generic [ref=e472]:
                  - generic [ref=e473]: ৳5700.00
                  - generic [ref=e474]: ৳5700.00
                - button "Add to Cart" [ref=e475] [cursor=pointer]:
                  - img [ref=e476]
          - generic [ref=e480]:
            - generic [ref=e482]: "-5% OFF"
            - generic [ref=e483]:
              - button "Wishlist" [ref=e484] [cursor=pointer]:
                - img [ref=e485]
              - button "Quick View" [ref=e487] [cursor=pointer]:
                - img [ref=e488]
              - button "Compare" [ref=e491] [cursor=pointer]:
                - img [ref=e492]
            - link "Affordable Custom Cricket Jersey With Sublimation Printing" [ref=e494] [cursor=pointer]:
              - /url: http://localhost/staylbd/product/affordable-custom-4
              - img "Affordable Custom Cricket Jersey With Sublimation Printing" [ref=e496]
            - generic [ref=e497]:
              - generic [ref=e499]: WinTerSMM
              - heading "Affordable Custom Cricket Jersey With Sublimation Printing" [level=3] [ref=e500]:
                - link "Affordable Custom Cricket Jersey With Sublimation Printing" [ref=e501] [cursor=pointer]:
                  - /url: http://localhost/staylbd/product/affordable-custom-4
              - generic [ref=e503]: In Stock
              - generic [ref=e504]:
                - generic [ref=e505]:
                  - generic [ref=e506]: ৳4275.00
                  - generic [ref=e507]: ৳4275.00
                - button "Add to Cart" [ref=e508] [cursor=pointer]:
                  - img [ref=e509]
          - generic [ref=e513]:
            - generic [ref=e515]: "-5% OFF"
            - generic [ref=e516]:
              - button "Wishlist" [ref=e517] [cursor=pointer]:
                - img [ref=e518]
              - button "Quick View" [ref=e520] [cursor=pointer]:
                - img [ref=e521]
              - button "Compare" [ref=e524] [cursor=pointer]:
                - img [ref=e525]
            - link "Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing" [ref=e527] [cursor=pointer]:
              - /url: http://localhost/staylbd/product/cricket-jersey-3
              - img "Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing" [ref=e529]
            - generic [ref=e530]:
              - generic [ref=e532]: WinTerSMM
              - heading "Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing" [level=3] [ref=e533]:
                - link "Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing" [ref=e534] [cursor=pointer]:
                  - /url: http://localhost/staylbd/product/cricket-jersey-3
              - generic [ref=e536]: In Stock
              - generic [ref=e537]:
                - generic [ref=e538]:
                  - generic [ref=e539]: ৳3894.05
                  - generic [ref=e540]: ৳3894.05
                - button "Add to Cart" [ref=e541] [cursor=pointer]:
                  - img [ref=e542]
          - generic [ref=e546]:
            - generic [ref=e548]: "-50% OFF"
            - generic [ref=e549]:
              - button "Wishlist" [ref=e550] [cursor=pointer]:
                - img [ref=e551]
              - button "Quick View" [ref=e553] [cursor=pointer]:
                - img [ref=e554]
              - button "Compare" [ref=e557] [cursor=pointer]:
                - img [ref=e558]
            - link "WinTerSMM" [ref=e560] [cursor=pointer]:
              - /url: http://localhost/staylbd/product/wintersmm-2
              - img "WinTerSMM" [ref=e562]
            - generic [ref=e563]:
              - generic [ref=e565]: WinTerSMM
              - heading "WinTerSMM" [level=3] [ref=e566]:
                - link "WinTerSMM" [ref=e567] [cursor=pointer]:
                  - /url: http://localhost/staylbd/product/wintersmm-2
              - generic [ref=e569]: In Stock
              - generic [ref=e570]:
                - generic [ref=e571]:
                  - generic [ref=e572]: ৳50.00
                  - generic [ref=e573]: ৳50.00
                - button "Add to Cart" [ref=e574] [cursor=pointer]:
                  - img [ref=e575]
      - generic [ref=e578]:
        - generic [ref=e579]:
          - heading "New Arrivals" [level=2] [ref=e580]:
            - img [ref=e581]
            - text: New Arrivals
          - link "View All" [ref=e583] [cursor=pointer]:
            - /url: http://localhost/staylbd/all/products?sort=newest
            - text: View All
            - img [ref=e585]
        - link "Browse Products" [ref=e681] [cursor=pointer]:
          - /url: http://localhost/staylbd/all/products
      - generic [ref=e682]:
        - generic [ref=e683]:
          - heading "Trending Now" [level=2] [ref=e684]:
            - img [ref=e685]
            - text: Trending Now
          - link "View All" [ref=e688] [cursor=pointer]:
            - /url: http://localhost/staylbd/all/products?sort=popular
            - text: View All
            - img [ref=e690]
        - link "Browse Products" [ref=e786] [cursor=pointer]:
          - /url: http://localhost/staylbd/all/products
      - generic [ref=e787]:
        - generic [ref=e788]:
          - heading "Best Selling" [level=2] [ref=e789]:
            - img [ref=e790]
            - text: Best Selling
          - link "View All" [ref=e792] [cursor=pointer]:
            - /url: http://localhost/staylbd/product/best-selling
            - text: View All
            - img [ref=e794]
        - link "Browse Products" [ref=e890] [cursor=pointer]:
          - /url: http://localhost/staylbd/all/products
      - generic "hrfh" [ref=e894]:
        - img "hrfh" [ref=e895]
      - generic [ref=e896]:
        - generic [ref=e897]:
          - heading "Recommended For You" [level=2] [ref=e898]:
            - img [ref=e899]
            - text: Recommended For You
          - link "View All" [ref=e901] [cursor=pointer]:
            - /url: http://localhost/staylbd/product/best-selling
            - text: View All
            - img [ref=e903]
        - generic [ref=e909]:
          - generic [ref=e910]:
            - button "Wishlist" [ref=e911] [cursor=pointer]:
              - img [ref=e912]
            - button "Quick View" [ref=e914] [cursor=pointer]:
              - img [ref=e915]
            - button "Compare" [ref=e918] [cursor=pointer]:
              - img [ref=e919]
          - link "RIAZUL ISLAM SHOJOL" [ref=e921] [cursor=pointer]:
            - /url: http://localhost/staylbd/product/riazul-islam-1
            - img "RIAZUL ISLAM SHOJOL" [ref=e923]
          - generic [ref=e924]:
            - generic [ref=e926]: WinTerSMM
            - heading "RIAZUL ISLAM SHOJOL" [level=3] [ref=e927]:
              - link "RIAZUL ISLAM SHOJOL" [ref=e928] [cursor=pointer]:
                - /url: http://localhost/staylbd/product/riazul-islam-1
            - generic [ref=e930]: In Stock
            - generic [ref=e931]:
              - generic [ref=e933]: ৳100.00
              - button "Add to Cart" [ref=e934] [cursor=pointer]:
                - img [ref=e935]
      - generic [ref=e938]:
        - heading "md opu" [level=2] [ref=e940]:
          - img [ref=e941]
          - text: md
          - generic [ref=e944]: opu
        - link "Browse Products" [ref=e1039] [cursor=pointer]:
          - /url: http://localhost/staylbd/all/products
  - contentinfo [ref=e1040]:
    - generic [ref=e1044]:
      - generic [ref=e1045]:
        - button "About Us" [ref=e1046] [cursor=pointer]
        - paragraph [ref=e1047]: "CEO: Mohammad Tariqul Islam"
        - paragraph [ref=e1048]: ওয়েলকাম staylbd কেনাকাটা নিশ্চিন্তে
        - paragraph [ref=e1049]: staylbd
        - paragraph [ref=e1050]: কেনাকাটা নিশ্চিন্তে
        - paragraph [ref=e1052]: "4901 Seminary Rd #120, Alexandria, Vermont USA"
      - generic [ref=e1056]:
        - button "Quick Links" [ref=e1057] [cursor=pointer]
        - list [ref=e1058]:
          - listitem [ref=e1059]:
            - link "ওয়েলকাম staylbd কেনাকাটা নিশ্চিন্তে" [ref=e1060] [cursor=pointer]:
              - /url: https://www.google.com/
          - listitem [ref=e1061]:
            - link "ওয়েলকাম staylbd কেনাকাটা নিশ্চিন্তে" [ref=e1062] [cursor=pointer]:
              - /url: https://www.google.com/
      - generic [ref=e1063]:
        - button "Support" [ref=e1064] [cursor=pointer]
        - list [ref=e1065]:
          - listitem [ref=e1066]:
            - link "Help Center" [ref=e1067] [cursor=pointer]:
              - /url: https://www.google.com/
          - listitem [ref=e1068]:
            - link "Return Policy" [ref=e1069] [cursor=pointer]:
              - /url: https://www.google.com/
          - listitem [ref=e1070]:
            - link "Refund Policy" [ref=e1071] [cursor=pointer]:
              - /url: https://www.google.com/
          - listitem [ref=e1072]:
            - link "Track Order" [ref=e1073] [cursor=pointer]:
              - /url: https://www.google.com/
          - listitem [ref=e1074]:
            - link "Support Ticket" [ref=e1075] [cursor=pointer]:
              - /url: http://localhost/staylbd/message/new
          - listitem [ref=e1076]:
            - link "Contact Support" [ref=e1077] [cursor=pointer]:
              - /url: mailto:shtejnjrhrbb@gmail
      - generic [ref=e1078]:
        - button "Trust & Security" [ref=e1079] [cursor=pointer]
        - list [ref=e1080]:
          - listitem [ref=e1081]:
            - link "ওয়েলকাম staylbd কেনাকাটা নিশ্চিন্তে" [ref=e1082] [cursor=pointer]:
              - /url: https://www.google.com/
              - img "ওয়েলকাম staylbd কেনাকাটা নিশ্চিন্তে" [ref=e1083]
      - generic [ref=e1084]:
        - button "Product Return Request" [ref=e1085] [cursor=pointer]
        - generic [ref=e1088]:
          - textbox "Name" [ref=e1090]
          - textbox "Email" [ref=e1092]
          - textbox "Order number (optional)" [ref=e1094]
          - textbox "Reason for return (optional)" [ref=e1096]
          - textbox "Message" [ref=e1098]
          - button "Submit Request" [ref=e1100] [cursor=pointer]
      - generic [ref=e1101]:
        - button "Payment Methods" [ref=e1102] [cursor=pointer]
        - list [ref=e1103]:
          - listitem [ref=e1104]:
            - img "Payment method" [ref=e1106]
          - listitem [ref=e1107]:
            - img "Payment method" [ref=e1109]
          - listitem [ref=e1110]:
            - img "Payment method" [ref=e1112]
          - listitem [ref=e1113]:
            - img "Payment method" [ref=e1115]
          - listitem [ref=e1116]:
            - img "Payment method" [ref=e1118]
          - listitem [ref=e1119]:
            - img "Payment method" [ref=e1121]
          - listitem [ref=e1122]:
            - img "Payment method" [ref=e1124]
          - listitem [ref=e1125]:
            - img "Payment method" [ref=e1127]
      - generic [ref=e1129]:
        - heading "Subscribe Newsletter" [level=6] [ref=e1130]
        - form "Newsletter subscription" [ref=e1131]:
          - generic [ref=e1132]:
            - generic:
              - generic:
                - img
            - textbox "Email address" [ref=e1133]:
              - /placeholder: Enter Your Email
            - button "Subscribe" [ref=e1134] [cursor=pointer]:
              - img [ref=e1136]
        - navigation "Account" [ref=e1139]:
          - link "Login" [ref=e1140] [cursor=pointer]:
            - /url: http://localhost/staylbd/user/login?open=login&redirect=http%3A%2F%2Flocalhost%2Fstaylbd
          - link "Registration" [ref=e1141] [cursor=pointer]:
            - /url: http://localhost/staylbd/user/register?open=register&redirect=http%3A%2F%2Flocalhost%2Fstaylbd
          - link "Seller account" [ref=e1142] [cursor=pointer]:
            - /url: http://localhost/staylbd/seller/apply
      - generic [ref=e1143]:
        - paragraph [ref=e1144]: To get updates follow us on Facebook, Twitters etc.
        - list [ref=e1146]:
          - listitem [ref=e1147]:
            - link "Email" [ref=e1148] [cursor=pointer]:
              - /url: mailto:contact@dealshop.com
              - img [ref=e1150]
          - listitem [ref=e1153]:
            - link "Facebook" [ref=e1154] [cursor=pointer]:
              - /url: https://www.facebook.com/
          - listitem [ref=e1157]:
            - link "Twitter" [ref=e1158] [cursor=pointer]:
              - /url: https://www.twitter.com/
          - listitem [ref=e1161]:
            - link "Instagram" [ref=e1162] [cursor=pointer]:
              - /url: https://www.instagram.com/
          - listitem [ref=e1165]:
            - link "opu" [ref=e1166] [cursor=pointer]:
              - /url: https://www.ryans.com/opu
          - listitem [ref=e1169]:
            - link "hi" [ref=e1170] [cursor=pointer]:
              - /url: https://www.ryans.com/opuhi
          - listitem [ref=e1173]:
            - link "uo" [ref=e1174] [cursor=pointer]:
              - /url: https://www.ryans.com/opuhiwe
        - generic [ref=e1177]:
          - button "Get our app" [ref=e1178] [cursor=pointer]
          - generic [ref=e1179]:
            - link "Get it on Android" [ref=e1180] [cursor=pointer]:
              - /url: "#"
              - img [ref=e1183]
              - generic [ref=e1185]: Get it on Android
            - link "Get it on Desktop" [ref=e1186] [cursor=pointer]:
              - /url: "#"
              - generic [ref=e1190]: Get it on Desktop
            - link "Get it on Mac" [ref=e1191] [cursor=pointer]:
              - /url: "#"
              - generic [ref=e1195]: Get it on Mac
            - link "Get it on Windows" [ref=e1196] [cursor=pointer]:
              - /url: "#"
              - generic [ref=e1200]: Get it on Windows
    - generic [ref=e1203]:
      - generic [ref=e1204]:
        - link "opu" [ref=e1205] [cursor=pointer]:
          - /url: http://localhost/staylbd
          - img "opu" [ref=e1206]
        - generic [ref=e1207]: Copyright © 2026 All Right Reserved
      - navigation "Legal and policies" [ref=e1208]:
        - link "Privacy Policy" [ref=e1209] [cursor=pointer]:
          - /url: http://localhost/staylbd/policy/42
        - link "Terms of Service" [ref=e1210] [cursor=pointer]:
          - /url: http://localhost/staylbd/policy/43
        - link "Shipping and Delivery" [ref=e1211] [cursor=pointer]:
          - /url: http://localhost/staylbd/policy/58
        - link "Cookie Preferences" [ref=e1212] [cursor=pointer]:
          - /url: http://localhost/staylbd/cookie/revoke
  - navigation "Mobile Navigation" [ref=e1213]:
    - link "Home" [ref=e1214] [cursor=pointer]:
      - /url: http://localhost/staylbd?mb=1
      - img [ref=e1216]
      - generic [ref=e1219]: Home
    - link "Categories" [ref=e1220] [cursor=pointer]:
      - /url: http://localhost/staylbd/category/all?mb=1
      - img [ref=e1222]
      - generic [ref=e1227]: Categories
    - link "Messages" [ref=e1228] [cursor=pointer]:
      - /url: http://localhost/staylbd/user/login?mb=1
      - img [ref=e1230]
      - generic [ref=e1233]: Messages
    - link "0 Cart" [ref=e1234] [cursor=pointer]:
      - /url: http://localhost/staylbd/user/cart?mb=1
      - generic [ref=e1235]:
        - img [ref=e1237]
        - generic [ref=e1241]: "0"
      - generic [ref=e1242]: Cart
    - link "Account" [ref=e1243] [cursor=pointer]:
      - /url: http://localhost/staylbd/account/guest-menu?mb=1
      - img [ref=e1245]
      - generic [ref=e1248]: Account
  - dialog "Cookie consent" [ref=e1250]:
    - generic [ref=e1251]:
      - img [ref=e1253]
      - generic [ref=e1254]:
        - paragraph [ref=e1255]:
          - text: We may use cookies or any other tracking technologies when you visit our website, including any other media form, mobile website, or mobile application related or connected to help customize the Site and improve your experience.
          - link "learn more" [ref=e1256] [cursor=pointer]:
            - /url: http://localhost/staylbd/cookie-policy
        - generic [ref=e1257]:
          - button "Allow" [ref=e1258] [cursor=pointer]
          - button "Decline" [ref=e1259] [cursor=pointer]
  - button [ref=e1260] [cursor=pointer]:
    - img [ref=e1262]
  - generic [ref=e1265]:
    - generic [ref=e1267]:
      - generic [ref=e1269]:
        - generic [ref=e1271] [cursor=pointer]: 
        - generic [ref=e1273] [cursor=pointer]: 
        - generic [ref=e1275] [cursor=pointer]: 
        - generic [ref=e1276] [cursor=pointer]:
          - generic [ref=e1277]: 
          - text: "0"
        - generic [ref=e1279] [cursor=pointer]: 
        - generic [ref=e1280] [cursor=pointer]:
          - generic [ref=e1281]: 
          - text: "3"
        - generic [ref=e1282] [cursor=pointer]:
          - generic [ref=e1283]: 
          - text: "10"
        - generic [ref=e1285] [cursor=pointer]: 
        - generic [ref=e1287] [cursor=pointer]: 
        - generic [ref=e1289] [cursor=pointer]: 
      - generic [ref=e1290]:
        - combobox [ref=e1294] [cursor=pointer]:
          - option "#1 index.php (17:11:07)"
          - option "#2 realtime?ids=1%2C5%2C4%2C3%2C2 (ajax) (17:11:10)"
          - option "#3 count (ajax) (17:11:10)"
          - option "#4 count (ajax) (17:11:10)"
          - option "#5 count (ajax) (17:11:12)"
          - option "#6 realtime?ids=1%2C5%2C4%2C3%2C2 (ajax) (17:11:21)"
          - option "#7 realtime?ids=1%2C5%2C4%2C3%2C2 (ajax) (17:11:33)" [selected]
        - generic [ref=e1295]:
          - generic [ref=e1296]: 
          - text: 8.2.12
        - generic [ref=e1297]:
          - generic [ref=e1298]: 
          - text: 227ms
        - generic [ref=e1299]:
          - generic [ref=e1300]: 
          - text: 25MB
        - generic [ref=e1301]:
          - generic [ref=e1302]: 
          - text: GET api/v1/products/realtime
    - text:                    
  - text: 
  - status: "Live updates: polling (backup)"
  - generic [ref=e1303]:
    - img "Product" [ref=e1304]
    - generic [ref=e1305]:
      - generic [ref=e1306]:
        - text: Rubel in Barishal recently bought
        - text: Affordable Custom Cricket...
      - generic [ref=e1307]: 10 minutes ago
```

# Test source

```ts
  1  | import { test, expect } from '@playwright/test';
  2  | 
  3  | test('language and currency dropdown open aligned on hover', async ({ page }) => {
  4  |   await page.goto('');
  5  | 
  6  |   const langMenu = page.locator('.stayl-topbar-menu:has(#staylCurrentLanguageLabel)').first();
  7  |   await langMenu.hover();
  8  |   await expect(langMenu.locator('.stayl-topbar-menu__panel').first()).toBeVisible();
  9  | 
  10 |   const currencyMenu = page.locator('.stayl-topbar-menu:has(#staylCurrentCurrencyLabel)').first();
  11 |   await currencyMenu.hover();
  12 |   await expect(currencyMenu.locator('.stayl-topbar-menu__panel').first()).toBeVisible();
  13 | });
  14 | 
  15 | test('language switch applies without getting stuck', async ({ page }) => {
  16 |   await page.goto('');
  17 | 
  18 |   const trigger = page.locator('.stayl-topbar-menu__btn:has(#staylCurrentLanguageLabel)').first();
> 19 |   await trigger.click();
     |                 ^ Error: locator.click: Test timeout of 30000ms exceeded.
  20 | 
  21 |   const hindiOption = page.locator('[data-stayl-lang-option="HI"]').first();
  22 |   await expect(hindiOption).toBeVisible();
  23 |   await hindiOption.click();
  24 | 
  25 |   await expect
  26 |     .poll(async () => {
  27 |       const cookies = await page.context().cookies();
  28 |       const c = cookies.find((item) => item.name === 'stayl_display_language_code');
  29 |       return c?.value ?? '';
  30 |     })
  31 |     .toMatch(/hi/i);
  32 | 
  33 |   await expect(page.locator('[data-stayl-lang-option="HI"]').first()).toHaveClass(/is-active/);
  34 | });
  35 | 
  36 | test('multiple global languages activate without forced reload', async ({ page }) => {
  37 |   await page.goto('');
  38 | 
  39 |   const trigger = page.locator('.stayl-topbar-menu__btn:has(#staylCurrentLanguageLabel)').first();
  40 |   await trigger.click();
  41 | 
  42 |   for (const code of ['UR', 'AR', 'RU', 'ZH']) {
  43 |     const option = page.locator(`[data-stayl-lang-option="${code}"]`).first();
  44 |     await expect(option).toBeVisible();
  45 |     await option.click();
  46 |     await expect
  47 |       .poll(async () => {
  48 |         const cookies = await page.context().cookies();
  49 |         const c = cookies.find((item) => item.name === 'stayl_display_language_code');
  50 |         return c?.value ?? '';
  51 |       })
  52 |       .toMatch(new RegExp(code === 'ZH' ? 'zh' : code.toLowerCase(), 'i'));
  53 |     await trigger.click();
  54 |   }
  55 | });
  56 | 
```