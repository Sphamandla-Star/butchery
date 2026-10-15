# Mokoka Butcher

> A full-stack butchery website — Web Development 3.2 assignment  
> **Vaal University of Technology · Due 16 October 2026**

---

## Table of Contents
1. [Project Overview](#project-overview)
2. [Business Details](#business-details)
3. [Pages](#pages)
4. [Tech Stack](#tech-stack)
5. [Folder Structure](#folder-structure)
6. [Local Setup (XAMPP)](#local-setup-xampp)
7. [Database Setup](#database-setup)
8. [Admin Panel](#admin-panel)
9. [JavaScript Features](#javascript-features)
10. [Brand Style Guide](#brand-style-guide)
11. [Sitemap](#sitemap)
12. [Languages](#languages)
13. [Team](#team)
14. [Known TODOs before submission](#known-todos-before-submission)

---

## Project Overview

Mokoka Butcher is a responsive, bilingual (English / isiZulu) multi-page website for a local butchery in Vanderbijlpark SE5, Gauteng. Customers can browse meat products, calculate prices by weight, build an order cart, and send enquiries — all from the browser. The site runs on a local XAMPP server.

---

## Business Details

| Field | Value |
|---|---|
| **Business name** | Mokoka Butcher |
| **Address** | Vanderbijlpark SE5, 1911, Gauteng, South Africa |
| **Phone** | 071 942 8917 |
| **WhatsApp** | [wa.me/27719428917](https://wa.me/27719428917) |
| **Languages** | English and isiZulu |

---

## Pages

| File | Page | Description |
|---|---|---|
| `index.html` | Home | Hero, Why Choose Us, Services list, Popular products, Useful Links |
| `about.html` | About Us | Our story, values, team (4 members), 3 behind-the-counter videos |
| `products.html` | Products | Full product grid with weight-based pricing, cart, order form |
| `contact.html` | Contact Us | Real contact details, opening hours table, message form, Google Maps |
| `sitemap.html` | Sitemap | All pages at a glance |
| `style-guide.html` | Style Guide | Colours, fonts, buttons, spacing rules for the project report |

---

## Tech Stack

| Layer | Technology |
|---|---|
| Markup | HTML5 — `nav`, `section`, `article`, `address`, `time`, `iframe`, `footer` |
| Styling | CSS3 — custom properties, flexbox, grid, 3 responsive breakpoints |
| Fonts | Google Fonts — Playfair Display (headings) + Open Sans (body) |
| Interactivity | Vanilla JavaScript (ES2020) — 7 features, no frameworks |
| Language toggle | EN / isiZulu via `data-i18n` attributes + localStorage |
| Server | PHP 8+ with PDO prepared statements |
| Database | MySQL 5.7+ / MariaDB 10.4+ |
| Local server | XAMPP (Apache + MySQL) |

---

## Folder Structure

```
ubuntu-butchery/
│
├── index.html          Home page
├── about.html          About Us
├── products.html       Products & cart
├── contact.html        Contact Us
├── sitemap.html        Sitemap
├── style-guide.html    Brand style guide
│
├── css/
│   └── style.css       All styles and responsive breakpoints
│
├── js/
│   ├── script.js       Mobile menu, filter, pricing, cart, forms
│   └── lang.js         EN/ZU language toggle (all strings here)
│
├── php/
│   ├── db.php          Database connection + shared helpers
│   ├── send_message.php  Contact form handler (JSON API)
│   └── submit_order.php  Order handler — server-side price validation
│
├── images/             All images (lowercase, hyphenated names)
│   ├── favicon.svg     Site favicon
│   ├── hero-meat-board.jpg
│   ├── chicken-counter-display.jpg
│   └── ...
│
├── database/
│   └── butchery.sql    Full schema + demo data + admin user
│
├── admin/              Admin panel (not public-facing)
│   ├── login.php
│   ├── index.php       Dashboard
│   ├── order.php
│   ├── message.php
│   ├── auth.php
│   ├── layout.php
│   └── admin.css
│
└── README.md
```

---

## Local Setup (XAMPP)

> PHP and database features require a local server.  
> Opening HTML files directly in a browser will show the layout but the order and contact forms will not submit.

**Steps:**

1. Install [XAMPP](https://www.apachefriends.org/) if not already installed.
2. Copy this folder into your XAMPP htdocs directory:
   ```
   C:\xampp\htdocs\butchery\
   ```
3. Start **Apache** and **MySQL** in the XAMPP Control Panel.
4. Import the database — see [Database Setup](#database-setup).
5. Open your browser and go to:
   ```
   http://localhost/butchery/
   ```

**Default database credentials** (set in [`php/db.php`](php/db.php)):

| Setting | Value |
|---|---|
| Host | `localhost` |
| Database | `butchery` |
| Username | `root` |
| Password | *(empty — XAMPP default)* |

If you have set a MySQL root password, update `DB_PASS` in `php/db.php`.

---

## Database Setup

1. Open **phpMyAdmin**: `http://localhost/phpmyadmin`
2. Click **Import** in the top menu.
3. Click **Choose File** → select `database/butchery.sql`
4. Click **Go**.

This creates the `butchery` database with five tables:

| Table | Purpose |
|---|---|
| `products` | Meat products and server-side prices per kg |
| `orders` | Customer order requests |
| `order_items` | Line items within each order |
| `messages` | Contact form submissions |
| `admins` | Admin panel users |

---

## Admin Panel

URL: `http://localhost/butchery/admin/`

| Field | Value |
|---|---|
| Username | `admin` |
| Password | `admin123` *(demo only — not for production)* |

The admin panel lets you view orders (with status updates) and read contact messages. It is protected by session authentication and CSRF tokens.

---

## JavaScript Features

| # | Feature | File |
|---|---|---|
| 1 | Mobile hamburger menu toggle | `js/script.js` |
| 2 | Products dropdown (mobile open/close) | `js/script.js` |
| 3 | Product category filter (show/hide cards) | `js/script.js` |
| 4 | Weight-based live price calculator | `js/script.js` |
| 5 | Cart with localStorage persistence | `js/script.js` |
| 6 | Order form validation + cart-to-PHP | `js/script.js` |
| 7 | EN/ZU language toggle + localStorage | `js/lang.js` |

---

## Brand Style Guide

See [`style-guide.html`](style-guide.html) for the full visual reference.

| Token | Value | Usage |
|---|---|---|
| `--primary` | `#8b1e1e` | Buttons, nav bar |
| `--primary-dark` | `#6a1515` | Hover states |
| `--accent` | `#d4a24c` | Gold highlights, underlines |
| `--light` | `#faf7f2` | Alternating section backgrounds |
| `--text` | `#333333` | Body copy |
| `--muted` | `#6b6b6b` | Subtitles, captions |
| Heading font | Playfair Display | h1, h2, h3 |
| Body font | Open Sans | All other text |
| Border radius | `10px` | Cards, buttons, inputs |

---

## Sitemap

```
Mokoka Butcher
│
├── Home                index.html
├── About Us            about.html
├── Products            products.html
│   ├── Beef           #beef
│   ├── Pork           #pork-chops
│   ├── Lamb           #lamb-chops
│   ├── Chicken        #chicken
│   ├── Specials       #boerewors
│   └── Order Cart     #cart
├── Contact Us          contact.html
├── Sitemap             sitemap.html
└── Style Guide         style-guide.html
```

---

## Languages

The site supports **English** and **isiZulu**. A toggle button (EN / ZU) in the nav switches languages. The user's choice is remembered in `localStorage`. All Zulu strings are in [`js/lang.js`](js/lang.js) — phrases marked `⚠️ UNSURE` must be reviewed by the Zulu-speaking team member (Siphamandla) before submission.

---

## Team

| Name | Role |
|---|---|
| Siphamandla | Owner & Head Butcher |
| Oratilwe | Co-owner & Vice Manager |
| Prudence | Operations Manager |
| Esnathi | Sales & Customer Service Manager |

---

## Known TODOs before submission

- [ ] Add real email address to `contact.html` (replace the TODO comment)
- [ ] Add real Facebook page URL to all footers (replace TODO comment)
- [ ] Add real Instagram page URL to all footers (replace TODO comment)
- [ ] Siphamandla to review all Zulu strings in `js/lang.js` (marked ⚠️ UNSURE)

---

*Mokoka Butcher · Vanderbijlpark SE5, 1911, Gauteng, South Africa*  
*© 2026 Mokoka Butcher. All rights reserved.*
