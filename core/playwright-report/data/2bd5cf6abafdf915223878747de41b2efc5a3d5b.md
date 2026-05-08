# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: language-switch.spec.ts >> multiple global languages activate without forced reload
- Location: tests\e2e\language-switch.spec.ts:36:5

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
    36 × waiting for element to be visible, enabled and stable
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
            - text: "Tomorrow Forecast: Showers |"
            - img [ref=e38]
            - text: Rain 92% |
            - img [ref=e40]
            - text: 28°/22°C
      - generic [ref=e42]:
        - button "EN" [ref=e44] [cursor=pointer]:
          - generic [ref=e46]: EN
          - img [ref=e47]
        - button "BDT" [ref=e50] [cursor=pointer]:
          - generic [ref=e52]: BDT
          - img [ref=e53]
        - link "Registration" [ref=e55] [cursor=pointer]:
          - /url: http://localhost/staylbd/user/register
        - link "BECOME A SELLER" [ref=e56] [cursor=pointer]:
          - /url: http://localhost/staylbd/seller/apply
    - generic [ref=e58]:
      - link "Staylbd" [ref=e60] [cursor=pointer]:
        - /url: http://localhost/staylbd
        - img "Staylbd" [ref=e61]
      - generic [ref=e63]:
        - generic [ref=e64]:
          - textbox "Search for products, brands and more..." [ref=e65]
          - button "Search" [ref=e66] [cursor=pointer]:
            - img [ref=e67]
          - button "Voice Search" [ref=e70] [cursor=pointer]:
            - img [ref=e71]
        - button "Camera Search" [ref=e74] [cursor=pointer]:
          - img [ref=e75]
      - generic [ref=e78]:
        - link "Orders" [ref=e79] [cursor=pointer]:
          - /url: http://localhost/staylbd/user/order
          - img [ref=e80]
        - link "Track Order" [ref=e83] [cursor=pointer]:
          - /url: http://localhost/staylbd/user/ordertrack
          - img [ref=e84]
        - link "0" [ref=e89] [cursor=pointer]:
          - /url: http://localhost/staylbd/user/wishlist
          - img [ref=e90]
          - generic [ref=e92]: "0"
        - link "0" [ref=e93] [cursor=pointer]:
          - /url: http://localhost/staylbd/user/cart
          - img [ref=e94]
          - generic [ref=e98]: "0"
        - link "Login" [ref=e99] [cursor=pointer]:
          - /url: http://localhost/staylbd/user/dashboard
          - img [ref=e100]
          - generic: Login
    - navigation [ref=e105]:
      - generic "Open Menu" [ref=e106] [cursor=pointer]:
        - img [ref=e107]
      - button "ALL CATEGORIES" [ref=e109] [cursor=pointer]:
        - generic [ref=e110]: ALL CATEGORIES
        - img [ref=e111]
      - list [ref=e113]:
        - listitem [ref=e114]:
          - link "Homepage" [ref=e115] [cursor=pointer]:
            - /url: /
        - listitem [ref=e116]:
          - link "Shop Products" [ref=e117] [cursor=pointer]:
            - /url: /products
        - listitem [ref=e118]:
          - link "Pages" [ref=e119] [cursor=pointer]:
            - /url: "#"
            - text: Pages
            - img [ref=e120]
        - listitem [ref=e122]:
          - link "About Us" [ref=e123] [cursor=pointer]:
            - /url: "#"
        - listitem [ref=e124]:
          - link "Latest Blog" [ref=e125] [cursor=pointer]:
            - /url: "#"
        - listitem [ref=e126]:
          - link "Contact Us" [ref=e127] [cursor=pointer]:
            - /url: /contact
  - main [ref=e128]:
    - generic [ref=e129]:
      - region "Banner" [ref=e130]:
        - link [ref=e134] [cursor=pointer]:
          - /url: "#"
      - generic [ref=e143]:
        - link "Gift Voucher Gift Voucher Aliquam eleifend in elit congue" [ref=e144] [cursor=pointer]:
          - /url: javascript:void(0)
          - img "Gift Voucher" [ref=e146]
          - generic [ref=e147]:
            - heading "Gift Voucher" [level=5] [ref=e148]
            - paragraph [ref=e149]: Aliquam eleifend in elit congue
        - link "Online Support 24/7 Online Support 24/7 Aliquam eleifend in elit congue" [ref=e150] [cursor=pointer]:
          - /url: javascript:void(0)
          - img "Online Support 24/7" [ref=e152]
          - generic [ref=e153]:
            - heading "Online Support 24/7" [level=5] [ref=e154]
            - paragraph [ref=e155]: Aliquam eleifend in elit congue
        - link "Money Back Guarantee Money Back Guarantee Aliquam eleifend in elit congue" [ref=e156] [cursor=pointer]:
          - /url: javascript:void(0)
          - img "Money Back Guarantee" [ref=e158]
          - generic [ref=e159]:
            - heading "Money Back Guarantee" [level=5] [ref=e160]
            - paragraph [ref=e161]: Aliquam eleifend in elit congue
        - link "Free Shipping Free Shipping Aliquam eleifend in elit congue" [ref=e162] [cursor=pointer]:
          - /url: javascript:void(0)
          - img "Free Shipping" [ref=e164]
          - generic [ref=e165]:
            - heading "Free Shipping" [level=5] [ref=e166]
            - paragraph [ref=e167]: Aliquam eleifend in elit congue
      - region "Category" [ref=e170]:
        - generic [ref=e171]:
          - img [ref=e173]
          - heading "Category row" [level=3] [ref=e178]
        - generic [ref=e179]:
          - link "9h 9h" [ref=e181] [cursor=pointer]:
            - /url: http://localhost/staylbd/category/product/9h/13
            - img "9h" [ref=e183]
            - heading "9h" [level=4] [ref=e184]
          - link "8h 8h" [ref=e186] [cursor=pointer]:
            - /url: http://localhost/staylbd/category/product/8h/12
            - img "8h" [ref=e188]
            - heading "8h" [level=4] [ref=e189]
          - link "7h 7h" [ref=e191] [cursor=pointer]:
            - /url: http://localhost/staylbd/category/product/7h/11
            - img "7h" [ref=e193]
            - heading "7h" [level=4] [ref=e194]
          - link "6h 6h" [ref=e196] [cursor=pointer]:
            - /url: http://localhost/staylbd/category/product/6h/10
            - img "6h" [ref=e198]
            - heading "6h" [level=4] [ref=e199]
          - link "5h 5h" [ref=e201] [cursor=pointer]:
            - /url: http://localhost/staylbd/category/product/5h/9
            - img "5h" [ref=e203]
            - heading "5h" [level=4] [ref=e204]
          - link "3h 3h" [ref=e206] [cursor=pointer]:
            - /url: http://localhost/staylbd/category/product/3h/8
            - img "3h" [ref=e208]
            - heading "3h" [level=4] [ref=e209]
          - link "2h 2h" [ref=e211] [cursor=pointer]:
            - /url: http://localhost/staylbd/category/product/2h/7
            - img "2h" [ref=e213]
            - heading "2h" [level=4] [ref=e214]
          - link "1h 1h" [ref=e216] [cursor=pointer]:
            - /url: http://localhost/staylbd/category/product/1h/6
            - img "1h" [ref=e218]
            - heading "1h" [level=4] [ref=e219]
          - link "ab ab" [ref=e221] [cursor=pointer]:
            - /url: http://localhost/staylbd/category/product/ab/5
            - img "ab" [ref=e223]
            - heading "ab" [level=4] [ref=e224]
          - link "RIAZUL ISLAM SHOJOL RIAZUL ISLAM SHOJOL" [ref=e226] [cursor=pointer]:
            - /url: http://localhost/staylbd/category/product/riazul-islam-shojol/4
            - img "RIAZUL ISLAM SHOJOL" [ref=e228]
            - heading "RIAZUL ISLAM SHOJOL" [level=4] [ref=e229]
          - link "Affordable Custom Cricket Jersey With Sublimation Printing Affordable Custom Cricket Jersey With Sublimation Printing" [ref=e231] [cursor=pointer]:
            - /url: http://localhost/staylbd/category/product/affordable-custom-cricket-jersey-with-sublimation-printing/3
            - img "Affordable Custom Cricket Jersey With Sublimation Printing" [ref=e233]
            - heading "Affordable Custom Cricket Jersey With Sublimation Printing" [level=4] [ref=e234]
          - link "T-shirt, টি-শার্ট T-shirt, টি-শার্ট" [ref=e236] [cursor=pointer]:
            - /url: http://localhost/staylbd/category/product/t-shirt-ti-sart/2
            - img "T-shirt, টি-শার্ট" [ref=e238]
            - heading "T-shirt, টি-শার্ট" [level=4] [ref=e239]
          - link "WinTerSMM WinTerSMM" [ref=e241] [cursor=pointer]:
            - /url: http://localhost/staylbd/category/product/wintersmm/1
            - generic [ref=e242]:
              - img "WinTerSMM"
            - heading "WinTerSMM" [level=4] [ref=e243]
      - generic [ref=e244]:
        - generic [ref=e245]:
          - heading "Quick Deals" [level=2] [ref=e246]:
            - img [ref=e247]
            - generic [ref=e249]: Quick Deals
          - link "View All" [ref=e250] [cursor=pointer]:
            - /url: http://localhost/staylbd/product/hot-deal
            - text: View All
            - img [ref=e252]
        - generic [ref=e258]:
          - generic [ref=e259]:
            - button "Wishlist" [ref=e260] [cursor=pointer]:
              - img [ref=e261]
            - button "Quick View" [ref=e263] [cursor=pointer]:
              - img [ref=e264]
            - button "Compare" [ref=e267] [cursor=pointer]:
              - img [ref=e268]
          - link "RIAZUL ISLAM SHOJOL" [ref=e270] [cursor=pointer]:
            - /url: http://localhost/staylbd/product/riazul-islam-1
            - img "RIAZUL ISLAM SHOJOL" [ref=e272]
          - generic [ref=e273]:
            - generic [ref=e275]: WinTerSMM
            - heading "RIAZUL ISLAM SHOJOL" [level=3] [ref=e276]:
              - link "RIAZUL ISLAM SHOJOL" [ref=e277] [cursor=pointer]:
                - /url: http://localhost/staylbd/product/riazul-islam-1
            - generic [ref=e279]: In Stock
            - generic [ref=e280]:
              - generic [ref=e282]: ৳100.00
              - button "Add to Cart" [ref=e283] [cursor=pointer]:
                - img [ref=e284]
      - generic "vb" [ref=e290]:
        - img "vb" [ref=e291]
      - generic [ref=e292]:
        - generic [ref=e293]:
          - heading "Hot Deals" [level=2] [ref=e294]:
            - img [ref=e295]
            - text: Hot Deals
          - link "View All" [ref=e297] [cursor=pointer]:
            - /url: http://localhost/staylbd/product/hot-deal
            - text: View All
            - img [ref=e299]
        - generic [ref=e303]:
          - generic [ref=e305]:
            - generic [ref=e307]: "-5% OFF"
            - generic [ref=e308]:
              - button "Wishlist" [ref=e309] [cursor=pointer]:
                - img [ref=e310]
              - button "Quick View" [ref=e312] [cursor=pointer]:
                - img [ref=e313]
              - button "Compare" [ref=e316] [cursor=pointer]:
                - img [ref=e317]
            - link "Affordable Custom Cricket Jersey With Sublimation Printing" [ref=e319] [cursor=pointer]:
              - /url: http://localhost/staylbd/product/affordable-custom-5
              - img "Affordable Custom Cricket Jersey With Sublimation Printing" [ref=e321]
            - generic [ref=e322]:
              - generic [ref=e324]: WinTerSMM
              - heading "Affordable Custom Cricket Jersey With Sublimation Printing" [level=3] [ref=e325]:
                - link "Affordable Custom Cricket Jersey With Sublimation Printing" [ref=e326] [cursor=pointer]:
                  - /url: http://localhost/staylbd/product/affordable-custom-5
              - generic [ref=e328]: In Stock
              - generic [ref=e329]:
                - generic [ref=e330]:
                  - generic [ref=e331]: ৳5700.00
                  - generic [ref=e332]: ৳5700.00
                - button "Add to Cart" [ref=e333] [cursor=pointer]:
                  - img [ref=e334]
          - generic [ref=e338]:
            - generic [ref=e340]: "-5% OFF"
            - generic [ref=e341]:
              - button "Wishlist" [ref=e342] [cursor=pointer]:
                - img [ref=e343]
              - button "Quick View" [ref=e345] [cursor=pointer]:
                - img [ref=e346]
              - button "Compare" [ref=e349] [cursor=pointer]:
                - img [ref=e350]
            - link "Affordable Custom Cricket Jersey With Sublimation Printing" [ref=e352] [cursor=pointer]:
              - /url: http://localhost/staylbd/product/affordable-custom-4
              - img "Affordable Custom Cricket Jersey With Sublimation Printing" [ref=e354]
            - generic [ref=e355]:
              - generic [ref=e357]: WinTerSMM
              - heading "Affordable Custom Cricket Jersey With Sublimation Printing" [level=3] [ref=e358]:
                - link "Affordable Custom Cricket Jersey With Sublimation Printing" [ref=e359] [cursor=pointer]:
                  - /url: http://localhost/staylbd/product/affordable-custom-4
              - generic [ref=e361]: In Stock
              - generic [ref=e362]:
                - generic [ref=e363]:
                  - generic [ref=e364]: ৳4275.00
                  - generic [ref=e365]: ৳4275.00
                - button "Add to Cart" [ref=e366] [cursor=pointer]:
                  - img [ref=e367]
          - generic [ref=e371]:
            - generic [ref=e373]: "-5% OFF"
            - generic [ref=e374]:
              - button "Wishlist" [ref=e375] [cursor=pointer]:
                - img [ref=e376]
              - button "Quick View" [ref=e378] [cursor=pointer]:
                - img [ref=e379]
              - button "Compare" [ref=e382] [cursor=pointer]:
                - img [ref=e383]
            - link "Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing" [ref=e385] [cursor=pointer]:
              - /url: http://localhost/staylbd/product/cricket-jersey-3
              - img "Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing" [ref=e387]
            - generic [ref=e388]:
              - generic [ref=e390]: WinTerSMM
              - heading "Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing" [level=3] [ref=e391]:
                - link "Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing" [ref=e392] [cursor=pointer]:
                  - /url: http://localhost/staylbd/product/cricket-jersey-3
              - generic [ref=e394]: In Stock
              - generic [ref=e395]:
                - generic [ref=e396]:
                  - generic [ref=e397]: ৳3894.05
                  - generic [ref=e398]: ৳3894.05
                - button "Add to Cart" [ref=e399] [cursor=pointer]:
                  - img [ref=e400]
          - generic [ref=e404]:
            - generic [ref=e406]: "-50% OFF"
            - generic [ref=e407]:
              - button "Wishlist" [ref=e408] [cursor=pointer]:
                - img [ref=e409]
              - button "Quick View" [ref=e411] [cursor=pointer]:
                - img [ref=e412]
              - button "Compare" [ref=e415] [cursor=pointer]:
                - img [ref=e416]
            - link "WinTerSMM" [ref=e418] [cursor=pointer]:
              - /url: http://localhost/staylbd/product/wintersmm-2
              - img "WinTerSMM" [ref=e420]
            - generic [ref=e421]:
              - generic [ref=e423]: WinTerSMM
              - heading "WinTerSMM" [level=3] [ref=e424]:
                - link "WinTerSMM" [ref=e425] [cursor=pointer]:
                  - /url: http://localhost/staylbd/product/wintersmm-2
              - generic [ref=e427]: In Stock
              - generic [ref=e428]:
                - generic [ref=e429]:
                  - generic [ref=e430]: ৳50.00
                  - generic [ref=e431]: ৳50.00
                - button "Add to Cart" [ref=e432] [cursor=pointer]:
                  - img [ref=e433]
      - generic [ref=e436]:
        - generic [ref=e437]:
          - heading "Featured Products" [level=2] [ref=e438]:
            - img [ref=e439]
            - text: Featured Products
          - link "View All" [ref=e441] [cursor=pointer]:
            - /url: http://localhost/staylbd/product/featured
            - text: View All
            - img [ref=e443]
        - generic [ref=e447]:
          - generic [ref=e449]:
            - generic [ref=e451]: "-5% OFF"
            - generic [ref=e452]:
              - button "Wishlist" [ref=e453] [cursor=pointer]:
                - img [ref=e454]
              - button "Quick View" [ref=e456] [cursor=pointer]:
                - img [ref=e457]
              - button "Compare" [ref=e460] [cursor=pointer]:
                - img [ref=e461]
            - link "Affordable Custom Cricket Jersey With Sublimation Printing" [ref=e463] [cursor=pointer]:
              - /url: http://localhost/staylbd/product/affordable-custom-5
              - img "Affordable Custom Cricket Jersey With Sublimation Printing" [ref=e465]
            - generic [ref=e466]:
              - generic [ref=e468]: WinTerSMM
              - heading "Affordable Custom Cricket Jersey With Sublimation Printing" [level=3] [ref=e469]:
                - link "Affordable Custom Cricket Jersey With Sublimation Printing" [ref=e470] [cursor=pointer]:
                  - /url: http://localhost/staylbd/product/affordable-custom-5
              - generic [ref=e472]: In Stock
              - generic [ref=e473]:
                - generic [ref=e474]:
                  - generic [ref=e475]: ৳5700.00
                  - generic [ref=e476]: ৳5700.00
                - button "Add to Cart" [ref=e477] [cursor=pointer]:
                  - img [ref=e478]
          - generic [ref=e482]:
            - generic [ref=e484]: "-5% OFF"
            - generic [ref=e485]:
              - button "Wishlist" [ref=e486] [cursor=pointer]:
                - img [ref=e487]
              - button "Quick View" [ref=e489] [cursor=pointer]:
                - img [ref=e490]
              - button "Compare" [ref=e493] [cursor=pointer]:
                - img [ref=e494]
            - link "Affordable Custom Cricket Jersey With Sublimation Printing" [ref=e496] [cursor=pointer]:
              - /url: http://localhost/staylbd/product/affordable-custom-4
              - img "Affordable Custom Cricket Jersey With Sublimation Printing" [ref=e498]
            - generic [ref=e499]:
              - generic [ref=e501]: WinTerSMM
              - heading "Affordable Custom Cricket Jersey With Sublimation Printing" [level=3] [ref=e502]:
                - link "Affordable Custom Cricket Jersey With Sublimation Printing" [ref=e503] [cursor=pointer]:
                  - /url: http://localhost/staylbd/product/affordable-custom-4
              - generic [ref=e505]: In Stock
              - generic [ref=e506]:
                - generic [ref=e507]:
                  - generic [ref=e508]: ৳4275.00
                  - generic [ref=e509]: ৳4275.00
                - button "Add to Cart" [ref=e510] [cursor=pointer]:
                  - img [ref=e511]
          - generic [ref=e515]:
            - generic [ref=e517]: "-5% OFF"
            - generic [ref=e518]:
              - button "Wishlist" [ref=e519] [cursor=pointer]:
                - img [ref=e520]
              - button "Quick View" [ref=e522] [cursor=pointer]:
                - img [ref=e523]
              - button "Compare" [ref=e526] [cursor=pointer]:
                - img [ref=e527]
            - link "Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing" [ref=e529] [cursor=pointer]:
              - /url: http://localhost/staylbd/product/cricket-jersey-3
              - img "Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing" [ref=e531]
            - generic [ref=e532]:
              - generic [ref=e534]: WinTerSMM
              - heading "Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing" [level=3] [ref=e535]:
                - link "Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing" [ref=e536] [cursor=pointer]:
                  - /url: http://localhost/staylbd/product/cricket-jersey-3
              - generic [ref=e538]: In Stock
              - generic [ref=e539]:
                - generic [ref=e540]:
                  - generic [ref=e541]: ৳3894.05
                  - generic [ref=e542]: ৳3894.05
                - button "Add to Cart" [ref=e543] [cursor=pointer]:
                  - img [ref=e544]
          - generic [ref=e548]:
            - generic [ref=e550]: "-50% OFF"
            - generic [ref=e551]:
              - button "Wishlist" [ref=e552] [cursor=pointer]:
                - img [ref=e553]
              - button "Quick View" [ref=e555] [cursor=pointer]:
                - img [ref=e556]
              - button "Compare" [ref=e559] [cursor=pointer]:
                - img [ref=e560]
            - link "WinTerSMM" [ref=e562] [cursor=pointer]:
              - /url: http://localhost/staylbd/product/wintersmm-2
              - img "WinTerSMM" [ref=e564]
            - generic [ref=e565]:
              - generic [ref=e567]: WinTerSMM
              - heading "WinTerSMM" [level=3] [ref=e568]:
                - link "WinTerSMM" [ref=e569] [cursor=pointer]:
                  - /url: http://localhost/staylbd/product/wintersmm-2
              - generic [ref=e571]: In Stock
              - generic [ref=e572]:
                - generic [ref=e573]:
                  - generic [ref=e574]: ৳50.00
                  - generic [ref=e575]: ৳50.00
                - button "Add to Cart" [ref=e576] [cursor=pointer]:
                  - img [ref=e577]
      - generic [ref=e580]:
        - generic [ref=e581]:
          - heading "New Arrivals" [level=2] [ref=e582]:
            - img [ref=e583]
            - text: New Arrivals
          - link "View All" [ref=e585] [cursor=pointer]:
            - /url: http://localhost/staylbd/all/products?sort=newest
            - text: View All
            - img [ref=e587]
        - link "Browse Products" [ref=e683] [cursor=pointer]:
          - /url: http://localhost/staylbd/all/products
      - generic [ref=e684]:
        - generic [ref=e685]:
          - heading "Trending Now" [level=2] [ref=e686]:
            - img [ref=e687]
            - text: Trending Now
          - link "View All" [ref=e690] [cursor=pointer]:
            - /url: http://localhost/staylbd/all/products?sort=popular
            - text: View All
            - img [ref=e692]
        - link "Browse Products" [ref=e788] [cursor=pointer]:
          - /url: http://localhost/staylbd/all/products
      - generic [ref=e789]:
        - generic [ref=e790]:
          - heading "Best Selling" [level=2] [ref=e791]:
            - img [ref=e792]
            - text: Best Selling
          - link "View All" [ref=e794] [cursor=pointer]:
            - /url: http://localhost/staylbd/product/best-selling
            - text: View All
            - img [ref=e796]
        - link "Browse Products" [ref=e892] [cursor=pointer]:
          - /url: http://localhost/staylbd/all/products
      - generic "hrfh" [ref=e896]:
        - img "hrfh" [ref=e897]
      - generic [ref=e898]:
        - generic [ref=e899]:
          - heading "Recommended For You" [level=2] [ref=e900]:
            - img [ref=e901]
            - text: Recommended For You
          - link "View All" [ref=e903] [cursor=pointer]:
            - /url: http://localhost/staylbd/product/best-selling
            - text: View All
            - img [ref=e905]
        - generic [ref=e911]:
          - generic [ref=e912]:
            - button "Wishlist" [ref=e913] [cursor=pointer]:
              - img [ref=e914]
            - button "Quick View" [ref=e916] [cursor=pointer]:
              - img [ref=e917]
            - button "Compare" [ref=e920] [cursor=pointer]:
              - img [ref=e921]
          - link "RIAZUL ISLAM SHOJOL" [ref=e923] [cursor=pointer]:
            - /url: http://localhost/staylbd/product/riazul-islam-1
            - img "RIAZUL ISLAM SHOJOL" [ref=e925]
          - generic [ref=e926]:
            - generic [ref=e928]: WinTerSMM
            - heading "RIAZUL ISLAM SHOJOL" [level=3] [ref=e929]:
              - link "RIAZUL ISLAM SHOJOL" [ref=e930] [cursor=pointer]:
                - /url: http://localhost/staylbd/product/riazul-islam-1
            - generic [ref=e932]: In Stock
            - generic [ref=e933]:
              - generic [ref=e935]: ৳100.00
              - button "Add to Cart" [ref=e936] [cursor=pointer]:
                - img [ref=e937]
      - generic [ref=e940]:
        - heading "md opu" [level=2] [ref=e942]:
          - img [ref=e943]
          - text: md
          - generic [ref=e946]: opu
        - link "Browse Products" [ref=e1041] [cursor=pointer]:
          - /url: http://localhost/staylbd/all/products
  - contentinfo [ref=e1042]:
    - generic [ref=e1046]:
      - generic [ref=e1047]:
        - button "About Us" [ref=e1048] [cursor=pointer]
        - paragraph [ref=e1049]: "CEO: Mohammad Tariqul Islam"
        - paragraph [ref=e1050]: ওয়েলকাম staylbd কেনাকাটা নিশ্চিন্তে
        - paragraph [ref=e1051]: staylbd
        - paragraph [ref=e1052]: কেনাকাটা নিশ্চিন্তে
        - paragraph [ref=e1054]: "4901 Seminary Rd #120, Alexandria, Vermont USA"
      - generic [ref=e1058]:
        - button "Quick Links" [ref=e1059] [cursor=pointer]
        - list [ref=e1060]:
          - listitem [ref=e1061]:
            - link "ওয়েলকাম staylbd কেনাকাটা নিশ্চিন্তে" [ref=e1062] [cursor=pointer]:
              - /url: https://www.google.com/
          - listitem [ref=e1063]:
            - link "ওয়েলকাম staylbd কেনাকাটা নিশ্চিন্তে" [ref=e1064] [cursor=pointer]:
              - /url: https://www.google.com/
      - generic [ref=e1065]:
        - button "Support" [ref=e1066] [cursor=pointer]
        - list [ref=e1067]:
          - listitem [ref=e1068]:
            - link "Help Center" [ref=e1069] [cursor=pointer]:
              - /url: https://www.google.com/
          - listitem [ref=e1070]:
            - link "Return Policy" [ref=e1071] [cursor=pointer]:
              - /url: https://www.google.com/
          - listitem [ref=e1072]:
            - link "Refund Policy" [ref=e1073] [cursor=pointer]:
              - /url: https://www.google.com/
          - listitem [ref=e1074]:
            - link "Track Order" [ref=e1075] [cursor=pointer]:
              - /url: https://www.google.com/
          - listitem [ref=e1076]:
            - link "Support Ticket" [ref=e1077] [cursor=pointer]:
              - /url: http://localhost/staylbd/message/new
          - listitem [ref=e1078]:
            - link "Contact Support" [ref=e1079] [cursor=pointer]:
              - /url: mailto:shtejnjrhrbb@gmail
      - generic [ref=e1080]:
        - button "Trust & Security" [ref=e1081] [cursor=pointer]
        - list [ref=e1082]:
          - listitem [ref=e1083]:
            - link "ওয়েলকাম staylbd কেনাকাটা নিশ্চিন্তে" [ref=e1084] [cursor=pointer]:
              - /url: https://www.google.com/
              - img "ওয়েলকাম staylbd কেনাকাটা নিশ্চিন্তে" [ref=e1085]
      - generic [ref=e1086]:
        - button "Product Return Request" [ref=e1087] [cursor=pointer]
        - generic [ref=e1090]:
          - textbox "Name" [ref=e1092]
          - textbox "Email" [ref=e1094]
          - textbox "Order number (optional)" [ref=e1096]
          - textbox "Reason for return (optional)" [ref=e1098]
          - textbox "Message" [ref=e1100]
          - button "Submit Request" [ref=e1102] [cursor=pointer]
      - generic [ref=e1103]:
        - button "Payment Methods" [ref=e1104] [cursor=pointer]
        - list [ref=e1105]:
          - listitem [ref=e1106]:
            - img "Payment method" [ref=e1108]
          - listitem [ref=e1109]:
            - img "Payment method" [ref=e1111]
          - listitem [ref=e1112]:
            - img "Payment method" [ref=e1114]
          - listitem [ref=e1115]:
            - img "Payment method" [ref=e1117]
          - listitem [ref=e1118]:
            - img "Payment method" [ref=e1120]
          - listitem [ref=e1121]:
            - img "Payment method" [ref=e1123]
          - listitem [ref=e1124]:
            - img "Payment method" [ref=e1126]
          - listitem [ref=e1127]:
            - img "Payment method" [ref=e1129]
      - generic [ref=e1131]:
        - heading "Subscribe Newsletter" [level=6] [ref=e1132]
        - form "Newsletter subscription" [ref=e1133]:
          - generic [ref=e1134]:
            - generic:
              - generic:
                - img
            - textbox "Email address" [ref=e1135]:
              - /placeholder: Enter Your Email
            - button "Subscribe" [ref=e1136] [cursor=pointer]:
              - img [ref=e1138]
        - navigation "Account" [ref=e1141]:
          - link "Login" [ref=e1142] [cursor=pointer]:
            - /url: http://localhost/staylbd/user/login?open=login&redirect=http%3A%2F%2Flocalhost%2Fstaylbd
          - link "Registration" [ref=e1143] [cursor=pointer]:
            - /url: http://localhost/staylbd/user/register?open=register&redirect=http%3A%2F%2Flocalhost%2Fstaylbd
          - link "Seller account" [ref=e1144] [cursor=pointer]:
            - /url: http://localhost/staylbd/seller/apply
      - generic [ref=e1145]:
        - paragraph [ref=e1146]: To get updates follow us on Facebook, Twitters etc.
        - list [ref=e1148]:
          - listitem [ref=e1149]:
            - link "Email" [ref=e1150] [cursor=pointer]:
              - /url: mailto:contact@dealshop.com
              - img [ref=e1152]
          - listitem [ref=e1155]:
            - link "Facebook" [ref=e1156] [cursor=pointer]:
              - /url: https://www.facebook.com/
          - listitem [ref=e1159]:
            - link "Twitter" [ref=e1160] [cursor=pointer]:
              - /url: https://www.twitter.com/
          - listitem [ref=e1163]:
            - link "Instagram" [ref=e1164] [cursor=pointer]:
              - /url: https://www.instagram.com/
          - listitem [ref=e1167]:
            - link "opu" [ref=e1168] [cursor=pointer]:
              - /url: https://www.ryans.com/opu
          - listitem [ref=e1171]:
            - link "hi" [ref=e1172] [cursor=pointer]:
              - /url: https://www.ryans.com/opuhi
          - listitem [ref=e1175]:
            - link "uo" [ref=e1176] [cursor=pointer]:
              - /url: https://www.ryans.com/opuhiwe
        - generic [ref=e1179]:
          - button "Get our app" [ref=e1180] [cursor=pointer]
          - generic [ref=e1181]:
            - link "Get it on Android" [ref=e1182] [cursor=pointer]:
              - /url: "#"
              - img [ref=e1185]
              - generic [ref=e1187]: Get it on Android
            - link "Get it on Desktop" [ref=e1188] [cursor=pointer]:
              - /url: "#"
              - generic [ref=e1192]: Get it on Desktop
            - link "Get it on Mac" [ref=e1193] [cursor=pointer]:
              - /url: "#"
              - generic [ref=e1197]: Get it on Mac
            - link "Get it on Windows" [ref=e1198] [cursor=pointer]:
              - /url: "#"
              - generic [ref=e1202]: Get it on Windows
    - generic [ref=e1205]:
      - generic [ref=e1206]:
        - link "opu" [ref=e1207] [cursor=pointer]:
          - /url: http://localhost/staylbd
          - img "opu" [ref=e1208]
        - generic [ref=e1209]: Copyright © 2026 All Right Reserved
      - navigation "Legal and policies" [ref=e1210]:
        - link "Privacy Policy" [ref=e1211] [cursor=pointer]:
          - /url: http://localhost/staylbd/policy/42
        - link "Terms of Service" [ref=e1212] [cursor=pointer]:
          - /url: http://localhost/staylbd/policy/43
        - link "Shipping and Delivery" [ref=e1213] [cursor=pointer]:
          - /url: http://localhost/staylbd/policy/58
        - link "Cookie Preferences" [ref=e1214] [cursor=pointer]:
          - /url: http://localhost/staylbd/cookie/revoke
  - navigation "Mobile Navigation" [ref=e1215]:
    - link "Home" [ref=e1216] [cursor=pointer]:
      - /url: http://localhost/staylbd?mb=1
      - img [ref=e1218]
      - generic [ref=e1221]: Home
    - link "Categories" [ref=e1222] [cursor=pointer]:
      - /url: http://localhost/staylbd/category/all?mb=1
      - img [ref=e1224]
      - generic [ref=e1229]: Categories
    - link "Messages" [ref=e1230] [cursor=pointer]:
      - /url: http://localhost/staylbd/user/login?mb=1
      - img [ref=e1232]
      - generic [ref=e1235]: Messages
    - link "0 Cart" [ref=e1236] [cursor=pointer]:
      - /url: http://localhost/staylbd/user/cart?mb=1
      - generic [ref=e1237]:
        - img [ref=e1239]
        - generic [ref=e1243]: "0"
      - generic [ref=e1244]: Cart
    - link "Account" [ref=e1245] [cursor=pointer]:
      - /url: http://localhost/staylbd/account/guest-menu?mb=1
      - img [ref=e1247]
      - generic [ref=e1250]: Account
  - dialog "Cookie consent" [ref=e1252]:
    - generic [ref=e1253]:
      - img [ref=e1255]
      - generic [ref=e1256]:
        - paragraph [ref=e1257]:
          - text: We may use cookies or any other tracking technologies when you visit our website, including any other media form, mobile website, or mobile application related or connected to help customize the Site and improve your experience.
          - link "learn more" [ref=e1258] [cursor=pointer]:
            - /url: http://localhost/staylbd/cookie-policy
        - generic [ref=e1259]:
          - button "Allow" [ref=e1260] [cursor=pointer]
          - button "Decline" [ref=e1261] [cursor=pointer]
  - button [ref=e1262] [cursor=pointer]:
    - img [ref=e1264]
  - generic [ref=e1267]:
    - generic [ref=e1269]:
      - generic [ref=e1271]:
        - generic [ref=e1273] [cursor=pointer]: 
        - generic [ref=e1275] [cursor=pointer]: 
        - generic [ref=e1277] [cursor=pointer]: 
        - generic [ref=e1278] [cursor=pointer]:
          - generic [ref=e1279]: 
          - text: "0"
        - generic [ref=e1281] [cursor=pointer]: 
        - generic [ref=e1282] [cursor=pointer]:
          - generic [ref=e1283]: 
          - text: "3"
        - generic [ref=e1284] [cursor=pointer]:
          - generic [ref=e1285]: 
          - text: "10"
        - generic [ref=e1287] [cursor=pointer]: 
        - generic [ref=e1289] [cursor=pointer]: 
        - generic [ref=e1291] [cursor=pointer]: 
      - generic [ref=e1292]:
        - combobox [ref=e1296] [cursor=pointer]:
          - option "#1 index.php (17:11:07)"
          - option "#2 count (ajax) (17:11:16)"
          - option "#3 count (ajax) (17:11:16)"
          - option "#4 count (ajax) (17:11:16)"
          - option "#5 realtime?ids=1%2C5%2C4%2C3%2C2 (ajax) (17:11:17)"
          - option "#6 realtime?ids=1%2C5%2C4%2C3%2C2 (ajax) (17:11:28)" [selected]
        - generic [ref=e1297]:
          - generic [ref=e1298]: 
          - text: 8.2.12
        - generic [ref=e1299]:
          - generic [ref=e1300]: 
          - text: 199ms
        - generic [ref=e1301]:
          - generic [ref=e1302]: 
          - text: 25MB
        - generic [ref=e1303]:
          - generic [ref=e1304]: 
          - text: GET api/v1/products/realtime
    - text:                    
  - text: 
  - status: "Live updates: polling (backup)"
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
  19 |   await trigger.click();
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
> 40 |   await trigger.click();
     |                 ^ Error: locator.click: Test timeout of 30000ms exceeded.
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