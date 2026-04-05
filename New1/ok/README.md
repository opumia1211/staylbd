# Lucide outline icons — STYLE BD header‑এর মতো আউটলাইন স্টাইল

**সোর্স:** [Lucide Icons](https://lucide.dev/) (ISC লাইসেন্স) — ফাইলগুলো সরাসরি GitHub থেকে `curl.exe` দিয়ে নামানো হয় (কোনো unpkg লিংক লাগে না)।

## টার্মিনাল দিয়ে সব আইকন আবার ডাউনলোড (Windows)

**PowerShell** খুলে এক লাইনে:

```powershell
powershell -NoProfile -ExecutionPolicy Bypass -File "C:\xampp\htdocs\staylbd\New1\download_lucide_icons.ps1"
```

অথবা আগে ফোল্ডারে গিয়ে:

```powershell
cd C:\xampp\htdocs\staylbd\New1
powershell -ExecutionPolicy Bypass -File .\download_lucide_icons.ps1
```

প্রয়োজন: Windows‑এ **`curl.exe`** (Win10/11‑এ সাধারণত আছে) এবং ইন্টারনেট।

## শুধু একটি ফাইল টেস্ট (উদাহরণ)

```powershell
curl.exe -sSL -o "C:\xampp\htdocs\staylbd\New1\lucide-ecommerce\search_icon.svg" "https://raw.githubusercontent.com/lucide-icons/lucide/main/icons/search.svg"
```

বেস URL (ম্যানুয়াল ডাউনলোডের জন্য ব্রাউজারে **নয়** — raw ফাইল):

`https://raw.githubusercontent.com/lucide-icons/lucide/main/icons/` **+** `search.svg` ইত্যাদি।

## স্ক্রিনশটের হেডার আইকন ↔ এই ফাইল

| স্ক্রিনে যা দেখা যায় | ফাইল (এই ফোল্ডারে) |
|----------------------|---------------------|
| সার্চ বারের শেষে লুপ | `search_icon.svg` |
| মাইক্রোফোন | `voice_search_icon.svg` |
| ইমেজ/ভিজুয়াল সার্চ | `image_search_icon.svg` |
| ডানদিকে বক্স/প্যাকেজ (প্রোডাক্টস) | `products_icon.svg` |
| ফোন | `contact_icon.svg` |
| ট্রাক | `track_order_icon.svg` |
| ভাষা (A + অক্ষর) | `language_icon.svg` |
| হার্ট উইশলিস্ট | `wishlist_icon.svg` |
| কম্পেয়ার তীর | `compare_icon.svg` |
| কার্ট | `cart_icon.svg` |
| ইউজার প্রোফাইল | `login_icon.svg` |

হ্যামবার্গার মেনু ও লোগো এই প্যাকের অংশ নয় (লোগো আলাদা **Site logo** আপলোড)।

## অ্যাডমিনে বসানো

**Admin → Frontend → Header Icons** — প্রতিটি স্কয়ার টাইলে সংশ্লিষ্ট `.svg` বেছে **Save all icons**।

| ফাইল | অ্যাডমিন স্লট |
|------|----------------|
| `search_icon.svg` | Search (submit) |
| `voice_search_icon.svg` | Voice search |
| `image_search_icon.svg` | Image / camera search |
| `home_icon.svg` | Home (Lucide **house**) |
| `categories_icon.svg` | Categories (mobile bar) |
| … | (বাকিগুলো পূর্বের মতো টেবিল) |

`home_icon.svg` = Lucide **`house.svg`** (হোম আইকন হিসেবে একই কাজ)।
