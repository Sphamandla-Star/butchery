# Mokoka Butchery — Group Presentation
**Web Development 3.2 (AIWEY3A) · Vaal University of Technology · 16 October 2026**
Team: Esnath · Oratilwe · Siphamandla · Prudence · Humphrey

---

## Slide 1 — Introduction & Project Overview
**Presenter: Esnath** *(introduces the team and the business)*

**Mokoka Butchery** is a full-stack e-commerce website for a real family-run butchery in Vanderbijlpark SE5, Gauteng.

**The problem:** The business had no online presence. Customers could not browse products, check prices, or place orders without visiting in person.

**Our solution:** A 6-page, bilingual, responsive website where customers can:
- Browse and filter meat products (beef, pork, lamb, chicken, boerewors)
- Calculate prices by weight and build a shopping cart
- Submit order requests and contact enquiries online

**ILOs demonstrated:**
ILO1 (dynamic website from scratch) · ILO2 (HTML5 structure) · ILO3 (CSS + responsive design) · ILO4 (PHP form processing) · ILO5 (front-end + back-end integration) · ILO6 (e-commerce order forms) · ILO7 (web security best practices)

**Stack:** HTML5 · CSS3 · Vanilla JavaScript · PHP 8 · MySQL · XAMPP

📸 *Screenshot: Home page hero section — nav bar down to the two CTA buttons (`index.html`)*

---

## Slide 2 — Design: General Disposition *(Rubric: 25%)*
**Presenter: Prudence** *(built the style guide & global CSS — `style-guide.html`, `css/style.css`)*

**Brand colour palette — defined as CSS custom properties in `css/style.css`:**

| Token | Hex | Used for |
|---|---|---|
| `--primary` | `#8b1e1e` | Nav bar, buttons |
| `--accent` | `#d4a24c` | Gold highlights, logo dot, badges |
| `--dark` | `#2b2b2b` | Nav background, headings |
| `--light` | `#faf7f2` | Alternating section backgrounds |

**Typography:**
- Headings: **Playfair Display** (Google Fonts) — weight 600/700
- Body: **Open Sans** (Google Fonts) — weight 400/500
- `<h1>` present on every page · Primary button contrast ratio ≈ 7.7:1 **(WCAG AAA ✓)**

**Consistency & readability:**
- All 6 pages share the same nav, footer, colour tokens, and font stack
- Style guide documented at [`style-guide.html`](style-guide.html) — colours, fonts, buttons, spacing
- CSS Grid + Flexbox layout; no external CSS framework

**Colour scheme for contrast:** Deep red on white ≈ 7.7:1 · Gold on dark ≈ 6.1:1 — both WCAG AA/AAA ✓

📸 *Screenshot: Style guide colour swatches — scroll to "Colour Palette" section on `style-guide.html`*

---

## Slide 3 — HTML & CSS Implementation *(Rubric: 20%)*
**Presenter: Esnath** *(built Home & About pages — `index.html`, `about.html`)*

**6 pages delivered:**
`index.html` · `about.html` · `products.html` · `contact.html` · `sitemap.html` · `style-guide.html`

**Every page includes:** clickable logo → home · sticky nav with hover states · footer with business name, address, phone, social links, copyright.

**HTML elements used across the site:**

| Element | Purpose | Where |
|---|---|---|
| Semantic tags: `nav`, `section`, `article`, `address`, `footer` | Correct document structure | All pages |
| `<h1>` – `<h3>` heading hierarchy + `<span>` | Typography & content hierarchy | All pages |
| `<table>` | Opening hours, order summary, style guide reference | `contact.html`, `products.html`, `style-guide.html` |
| `<ul>` / `<ol>` lists | Services list, sitemap, feature lists | `index.html`, `sitemap.html` |
| `<iframe>` | Google Maps embed | `contact.html` |
| `<video>` | 3 behind-the-counter clips | `about.html` |
| `<img>` | 12+ product and team photos (JPG/SVG) | All pages |

📸 *Screenshot: Sitemap page — full grid of page cards (`sitemap.html`)*

---

## Slide 4 — Content & Visual Elements *(Rubric: 20%)*
**Presenter: Oratilwe** *(built Products page, cart & pricing — `products.html`, `js/script.js`)*

**Subject relevance:** All content is specific to Mokoka Butchery — real address (Vanderbijlpark SE5), real phone number (071 942 8917), real products with prices per kg.

**Organisation — clear content hierarchy on every page:**
- **Home:** hero → Why Choose Us → services → featured products → useful links *(Esnath)*
- **About:** story → values → team profiles → 3 behind-the-counter videos *(Esnath)*
- **Products:** filter tabs → product grid → cart → order form *(Oratilwe)*
- **Contact:** business details → opening hours table → message form → Google Maps *(Siphamandla)*

**Visual elements (exceeds expectations):**
- 12+ product and team images (lowercase, hyphenated filenames per industry convention)
- 3 embedded videos on the About page
- Google Maps iframe on the Contact page
- Colour-coded category filter badges on the Products page

**External links (active, open in `_blank`):** WhatsApp (`wa.me`), Facebook, Instagram, Google Maps — all related to the business subject.

📸 *Screenshot: About page — team section with photos and the 3 video embeds (`about.html`)*

---

## Slide 5 — Functionality & Navigation *(Rubric: 15%)*
**Presenter: Siphamandla** *(built Contact page & message handling — `contact.html`, `php/send_message.php`)*

**Navigation (exceeds expectations):**
- Sticky nav bar on all 6 pages with active-link highlighting
- **Dropdown sub-menu** on Products (desktop hover + mobile tap)
- Mobile hamburger menu with JS toggle (`aria-expanded` updated for accessibility)
- All internal pages linked; all external links open in `_blank`

**All elements usable and correct:**
- Category filter — shows/hides product cards by type instantly
- Weight dropdown — live price recalculates on the card in real time
- Cart badge — increments on "Add to Cart"; persists across page navigation
- Order form — client-side validation before PHP submission
- Contact form — validates then POSTs to `send_message.php` (returns JSON)
- Admin panel — order status dropdown updates persist to database

📸 *Screenshot: Products page desktop vs mobile side by side — F12 → Device Toolbar → 375 px width for mobile view*

---

## Slide 6 — JavaScript, Responsive & Bilingual *(Rubric: Other Elements 20%)*
**Presenter: Humphrey** *(built isiZulu language toggle — `js/lang.js`, all `data-i18n` attributes)*

**7 JavaScript features — vanilla ES2020, zero frameworks (exceeds expectations):**

| # | Feature | File | Built by |
|---|---|---|---|
| 1 | Mobile hamburger menu toggle | `js/script.js` | Oratilwe |
| 2 | Products dropdown (mobile tap) | `js/script.js` | Oratilwe |
| 3 | Category filter — show/hide cards | `js/script.js` | Oratilwe |
| 4 | Weight-based live price calculator | `js/script.js` | Oratilwe |
| 5 | Shopping cart with `localStorage` persistence | `js/script.js` | Oratilwe |
| 6 | Order form validation + cart-to-PHP submission | `js/script.js` | Oratilwe |
| 7 | EN / isiZulu language toggle + `localStorage` | `js/lang.js` | Humphrey |

**Responsive design — 3 breakpoints in `css/style.css` (exceeds expectations):**
- **900 px** (tablet) — two-column layouts collapse to single column
- **768 px** — hamburger menu activates, font sizes reduce
- **480 px** (small phone) — product grid → single column, images stack

**Bilingual — English & isiZulu (exceeds expectations):**
- ZU / EN toggle button in every nav bar — no page reload required
- 73 translation keys in `js/lang.js` applied via `data-i18n` attributes
- Language preference saved to `localStorage` — remembered across all pages
- All isiZulu strings reviewed and confirmed by Siphamandla

📸 *Screenshot: Home page hero side by side — English (EN) vs isiZulu (ZU) after clicking the toggle button*

---

## Slide 7 — Backend, Security & Conclusion *(Rubric: ILO4, ILO5, ILO7)*
**Presenter: Prudence** *(built Admin panel — `admin/`)* ← *also wraps up the conclusion for the team*

**PHP — 3 files, PDO prepared statements throughout (ILO4 + ILO7):**
- `php/db.php` — PDO connection + shared `e()` output-escaping helper *(Siphamandla)*
- `php/send_message.php` — saves contact enquiry to `messages` table, returns JSON *(Siphamandla)*
- `php/submit_order.php` — **server-side price re-check**: browser price is discarded, DB price is used; order + items saved in a transaction *(Siphamandla)*

**MySQL — 5 tables:** `products` · `orders` · `order_items` · `messages` · `admins` *(Siphamandla)*

**Admin panel** (`/admin/`) — bcrypt passwords, CSRF tokens, session regeneration, order status updates, message inbox *(Prudence — see Slide 2 & 5 presenter)*

**Security measures implemented (ILO7):**
- PDO prepared statements — no SQL injection risk
- `password_hash()` / `password_verify()` — bcrypt admin passwords
- CSRF tokens on admin forms · `session_regenerate_id(true)` on login
- `htmlspecialchars()` via `e()` helper — XSS prevention on all output

**Conclusion — what was delivered:**
- 6-page responsive e-commerce website for a real local business
- Bilingual (English / isiZulu), 73 translation keys
- Full-stack: HTML5 · CSS3 · 7 JS features · PHP 8 · MySQL
- Secure admin panel — order management and message inbox
- All 7 ILOs and all rubric criteria addressed

📸 *Screenshot: phpMyAdmin showing all 5 tables · Admin dashboard with orders table · Single order detail page*

---

## Team Contributions Summary

| # | Area | Files | Done by |
|---|---|---|---|
| 1 | Home & About pages | `index.html`, `about.html` | Esnath |
| 2 | Products, cart & pricing | `products.html`, `js/script.js` | Oratilwe |
| 3 | Contact page & message handling | `contact.html`, `php/send_message.php` | Siphamandla |
| 4 | Database & order handling | `database/butchery.sql`, `php/db.php`, `php/submit_order.php` | Siphamandla |
| 5 | Admin panel | `admin/` | Prudence |
| 6 | isiZulu translation & review | `js/lang.js` | Humphrey |
| 7 | Style guide & global CSS | `style-guide.html`, `css/style.css` | Prudence |
| — | Testing & XAMPP set-up | Whole project | Everyone |
| — | Report & presentation | Report, slides, demo | Everyone |

---

*Mokoka Butchery · Vanderbijlpark SE5, 1911 · © 2026 All rights reserved.*
