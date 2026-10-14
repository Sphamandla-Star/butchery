# Ubuntu-Butchery

> A full-stack butchery website built as a Web Development 3.2 assignment  
> **Vaal University of Technology — due Friday 16 October 2026**

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
9. [Features](#features)
10. [Brand Style Guide](#brand-style-guide)
11. [Sitemap](#sitemap)
12. [Team](#team)
13. [Known Issues & TODO](#known-issues--todo)

---

## Project Overview

Ubuntu-Butchery is a responsive, multi-page website for a local butchery based in  
Vanderbijlpark SE5, Gauteng, South Africa. It allows customers to browse meat products,  
calculate prices by weight, build an order cart, and send messages — all from the browser.

The site is written in **HTML, CSS, JavaScript, PHP and MySQL** and is designed to run  
on a local XAMPP server. An optional admin panel lets the business owner view orders  
and messages.

---

## Business Details

| Field | Value |
|---|---|
| **Business name** | Ubuntu-Butchery |
| **Address** | Vanderbijlpark SE5, Gauteng, South Africa |
| **Phone** | 071 942 8917 |
| **WhatsApp** | [wa.me/27719428917](https://wa.me/27719428917) |
| **Languages** | English and isiZulu |

---

## Pages

| File | Page | Description |
|---|---|---|
| `index.html` | Home | Hero section, highlights, featured products |
| `about.html` | About Us | Our story, values, team cards, behind-the-counter videos |
| `products.html` | Products | Full product grid with weight-based pricing, cart, and order form |
| `contact.html` | Contact Us | Contact details, opening hours, message form, Google Maps embed |

---

## Tech Stack

| Layer | Technology |
|---|---|
| Markup | HTML5 (semantic elements: `nav`, `section`, `article`, `figure`, `iframe`) |
| Styling | CSS3 (custom properties, flexbox, grid, 3 responsive breakpoints) |
| Interactivity | Vanilla JavaScript (ES2020, no frameworks) |
| Server | PHP 8+ (PDO, prepared statements) |
| Database | MySQL 5.7+ / MariaDB 10.4+ |
| Local server | XAMPP (Apache + MySQL) |

---

## Folder Structure

```
ubuntu-butchery/
│
├── index.html              # Home page
├── about.html              # About Us page
├── products.html           # Products & cart page
├── contact.html            # Contact Us page
│
├── css/
│   └── style.css           # All styles and responsive breakpoints
│
├── js/
│   └── script.js           # Mobile menu, filter, pricing, cart, forms
│
├── php/
│   ├── db.php              # Database connection and shared helpers
│   ├── send_message.php    # Receives contact form submissions (JSON API)
│   └── submit_order.php    # Receives order requests, validates, saves to DB
│
├── images/                 # All site images (lowercase, hyphenated names)
│
├── database/
│   └── butchery.sql        # Full database schema + starter product data
│
├── admin/                  # Admin panel (not public-facing)
│   ├── index.php           # Dashboard
│   ├── order.php           # View and update orders
│   ├── message.php         # View messages
│   ├── login.php           # Admin login
│   ├── logout.php          # Session logout
│   ├── auth.php            # Session and CSRF protection
│   ├── layout.php          # Shared admin page layout
│   └── admin.css           # Admin panel styles
│
└── README.md
```

---

## Local Setup (XAMPP)

> The PHP and database features require a local server. Opening the HTML files  
> directly in a browser will work for layout but the order form and contact form  
> will not submit.

### Step-by-step

1. **Install XAMPP** from [apachefriends.org](https://www.apachefriends.org/) if you have not already.
2. **Copy this folder** into your XAMPP `htdocs` directory:
   ```
   C:\xampp\htdocs\butchery\
   ```
3. **Start Apache and MySQL** in the XAMPP Control Panel.
4. **Import the database** — see [Database Setup](#database-setup) below.
5. Open your browser and go to:
   ```
   http://localhost/butchery/
   ```

### Database credentials

The default XAMPP credentials are already set in [`php/db.php`](php/db.php):

| Setting | Value |
|---|---|
| Host | `localhost` |
| Database | `butchery` |
| Username | `root` |
| Password | *(empty)* |

If you have set a MySQL password, update `DB_PASS` in `php/db.php`.

---

## Database Setup

1. Open **phpMyAdmin**: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Click **Import** in the top menu.
3. Click **Choose File** and select `database/butchery.sql`.
4. Click **Go**.

This creates the `butchery` database with four tables:

| Table | Purpose |
|---|---|
| `products` | Meat products and server-side prices per kg |
| `orders` | Customer order requests |
| `order_items` | Individual line items within each order |
| `messages` | Contact form submissions |

> **Important:** Prices in the database are the authoritative prices. The browser  
> prices shown on the Products page are for display only — the server always  
> recalculates totals from the database before saving an order.

---

## Admin Panel

The admin panel is located at `http://localhost/butchery/admin/`.

> ⚠️ **Note:** The `admins` table is not yet included in `butchery.sql`.  
> Before the admin login will work, an `admins` table and a user record must be  
> added to the database. This is a known issue listed in [Known Issues](#known-issues--todo).

Once set up, the admin panel allows the business owner to:

- View new order requests and update their status (`new → confirmed → ready → completed`)
- Read customer messages sent via the contact form
- All forms are protected with CSRF tokens

---

## Features

### JavaScript (6 functionalities)

| Feature | Description |
|---|---|
| **Mobile menu toggle** | Hamburger button opens/closes the nav on small screens |
| **Product category filter** | Filter buttons show/hide product cards by meat type |
| **Weight-based price calculator** | Price updates live when weight or quantity changes |
| **Cart (localStorage)** | Items persist across page refreshes; count shown in nav |
| **Order form validation** | Client-side checks before the form is submitted to PHP |
| **Contact form async submit** | Sent in the background via `fetch`; shows success/error message without page reload |

### PHP

- Contact form messages saved to the `messages` table via [`php/send_message.php`](php/send_message.php)
- Order requests saved to `orders` + `order_items` via [`php/submit_order.php`](php/submit_order.php)
- Server-side price recalculation — browser prices are never trusted
- Input sanitisation and prepared statements throughout

### Responsive Design

Three CSS breakpoints in [`css/style.css`](css/style.css):

| Breakpoint | Target |
|---|---|
| `max-width: 900px` | Tablet — stacks two-column layouts |
| `max-width: 768px` | Mobile — shows hamburger menu, adjusts font sizes |
| `max-width: 480px` | Small phones — single-column product grid |

---

## Brand Style Guide

| Token | Value | Usage |
|---|---|---|
| `--primary` | `#8b1e1e` | Buttons, nav bar, header background |
| `--primary-dark` | `#6a1515` | Hover states on primary elements |
| `--accent` | `#d4a24c` | Decorative underlines, highlights |
| `--light` | `#faf7f2` | Alternating section backgrounds |
| `--text` | `#333333` | Body copy |
| `--muted` | `#6b6b6b` | Subtitles, captions |
| `--border` | `#e6dfd3` | Card borders, table dividers |
| `--white` | `#ffffff` | Page background, card backgrounds |
| Body font | `"Segoe UI", Roboto, Arial, sans-serif` | All body text |
| Border radius | `10px` | Cards, buttons, inputs |

---

## Sitemap

```
Ubuntu-Butchery
│
├── Home                    index.html
├── About Us                about.html
├── Products                products.html
│   ├── Beef               #beef
│   ├── Pork               #pork-chops
│   ├── Lamb               #lamb-chops
│   ├── Chicken            #chicken
│   ├── Specials           #boerewors
│   └── Order Cart         #cart
└── Contact Us              contact.html
```

---

## Team

| Name | Role |
|---|---|
| *(Team member 1)* | *(Role)* |
| *(Team member 2)* | *(Role)* |
| *(Team member 3)* | *(Role)* |
| *(Team member 4)* | *(Role)* |

> **Note to team:** Replace the placeholders above with your real names and roles  
> before submitting. Your names must also match the About page team cards.

---

## Known Issues & TODO

These are tracked and will be fixed on the `rubric-fixes` branch:

- [ ] `admins` table missing from `butchery.sql` — admin login throws a DB error on fresh install
- [ ] Business name inconsistent across files — must be `Ubuntu-Butchery` everywhere
- [ ] Contact page has placeholder address, phone, and email
- [ ] Google Maps embed points to Johannesburg — must point to Vanderbijlpark SE5
- [ ] All social media links are `href="#"` — need real URLs
- [ ] No Products dropdown menu — rubric requires Beef / Pork / Chicken sub-links
- [ ] No Zulu (isiZulu) language content — rubric requires two languages
- [ ] No favicon
- [ ] No Google Font loaded (site uses system fonts only)
- [ ] Two Pexels images in `images/` are not used on any page and have non-standard names
- [ ] Hero section has no background image — shows as a plain colour block
- [ ] Boerewors video caption does not match its title
- [ ] Team names on About page may not be real — needs confirmation
- [ ] No brand style guide document (only inline CSS variables)
- [ ] No sitemap page

---

*Ubuntu-Butchery — Vanderbijlpark SE5, Gauteng, South Africa*  
*© 2026 Ubuntu-Butchery. All rights reserved.*
