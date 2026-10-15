/* ==========================================================
   Mokoka Butcher - Main JavaScript
   Features: mobile menu, dropdown nav, product filter,
   weight-based pricing, quantities, cart (saved in the browser),
   order and contact forms
   ========================================================== */

const CART_KEY = "butcheryCart";

document.addEventListener("DOMContentLoaded", () => {
    setupMobileMenu();
    setupProductFilter();
    setupProducts();
    setupCart();
    setupOrderForm();
    setupContactForm();
    highlightProductFromHash();
    updateCartCount();
});

/* ---------- Helpers ---------- */

// Price for a given weight in grams, based on price per kg.
// Example: R180/kg at 500g = R90.00
function calculatePrice(pricePerKg, grams) {
    return Math.round((pricePerKg * grams / 1000) * 100) / 100;
}

function formatMoney(amount) {
    return amount.toFixed(2);
}

// 500 -> "500 g", 1000 -> "1 kg", 1500 -> "1.5 kg"
function formatWeight(grams) {
    return grams >= 1000 ? `${grams / 1000} kg` : `${grams} g`;
}

function escapeHtml(text) {
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
}

/* ---------- Mobile Menu ---------- */

function setupMobileMenu() {
    const toggle = document.querySelector(".menu-toggle");
    const nav = document.querySelector(".main-nav");
    if (!toggle || !nav) return;

    toggle.addEventListener("click", () => {
        const isOpen = nav.classList.toggle("open");
        toggle.setAttribute("aria-expanded", isOpen);
    });

    // On mobile, tap the Products link to open/close its dropdown
    // instead of navigating (desktop uses CSS :hover/:focus-within)
    const dropdownParents = nav.querySelectorAll(".has-dropdown");
    dropdownParents.forEach((item) => {
        const link = item.querySelector(":scope > a");
        link.addEventListener("click", (e) => {
            // Only intercept on mobile (hamburger visible)
            if (window.getComputedStyle(toggle).display === "none") return;
            e.preventDefault();
            const isOpen = item.classList.toggle("open");
            // Close other open dropdowns
            dropdownParents.forEach((other) => {
                if (other !== item) other.classList.remove("open");
            });
            link.setAttribute("aria-expanded", isOpen);
        });
    });
}

/* ---------- Category Filter (Products page) ---------- */

function setupProductFilter() {
    const buttons = document.querySelectorAll(".filter-btn");
    const cards = document.querySelectorAll(".product-card");
    if (buttons.length === 0) return;

    buttons.forEach((btn) => {
        btn.addEventListener("click", () => {
            buttons.forEach((b) => b.classList.remove("active"));
            btn.classList.add("active");

            const filter = btn.dataset.filter;
            cards.forEach((card) => {
                const show = filter === "all" || card.dataset.category === filter;
                card.classList.toggle("is-hidden", !show);
            });
        });
    });
}

/* ---------- Product Cards: weight pricing and quantity ---------- */

function setupProducts() {
    const cards = document.querySelectorAll(".product-card");
    if (cards.length === 0) return;

    cards.forEach((card) => {
        const select = card.querySelector(".weight-select");
        const qtyInput = card.querySelector(".qty-input");
        const addBtn = card.querySelector(".add-to-cart");

        // Show the price per kg from the button's data attribute
        const pricePerKg = parseFloat(addBtn.dataset.pricePerKg) || 0;
        card.querySelector(".product-price-per-kg span").textContent = formatMoney(pricePerKg);

        const updatePrice = () => {
            const grams = parseInt(select.value, 10);
            const qty = Math.max(1, parseInt(qtyInput.value, 10) || 1);
            qtyInput.value = qty;

            const total = calculatePrice(pricePerKg, grams) * qty;
            card.querySelector(".total-price").textContent = `R ${formatMoney(total)}`;
        };

        select.addEventListener("change", updatePrice);
        qtyInput.addEventListener("input", updatePrice);

        addBtn.addEventListener("click", () => {
            const grams = parseInt(select.value, 10);
            const qty = Math.max(1, parseInt(qtyInput.value, 10) || 1);

            addToCart({
                id: addBtn.dataset.id,
                name: addBtn.dataset.name,
                pricePerKg: pricePerKg,
                grams: grams,
                qty: qty
            });

            // Brief confirmation on the button
            const original = addBtn.textContent;
            addBtn.textContent = "Added \u2713";
            addBtn.classList.add("added");
            setTimeout(() => {
                addBtn.textContent = original;
                addBtn.classList.remove("added");
            }, 1200);
        });

        updatePrice();
    });
}

/* ---------- Cart storage ---------- */

function getCart() {
    try {
        return JSON.parse(localStorage.getItem(CART_KEY)) || [];
    } catch (error) {
        return [];
    }
}

function saveCart(cart) {
    try {
        localStorage.setItem(CART_KEY, JSON.stringify(cart));
    } catch (error) {
        console.warn("Could not save cart:", error);
    }
    updateCartCount();
}

function addToCart(item) {
    const cart = getCart();

    // Same product and same weight: increase quantity instead of adding a new line
    const existing = cart.find((i) => i.id === item.id && i.grams === item.grams);
    if (existing) {
        existing.qty += item.qty;
    } else {
        cart.push(item);
    }

    saveCart(cart);
    renderCart();
}

function updateCartCount() {
    const count = getCart().reduce((sum, item) => sum + item.qty, 0);
    document.querySelectorAll(".cart-count").forEach((el) => {
        el.textContent = count;
    });
}

/* ---------- Cart display (Products page) ---------- */

function setupCart() {
    const tbody = document.getElementById("cart-body");
    if (!tbody) return;

    // Quantity buttons and remove buttons use event delegation
    tbody.addEventListener("click", (event) => {
        const row = event.target.closest("tr");
        if (!row) return;
        const index = parseInt(row.dataset.index, 10);
        const cart = getCart();
        if (!cart[index]) return;

        if (event.target.matches(".qty-plus")) {
            cart[index].qty += 1;
        } else if (event.target.matches(".qty-minus")) {
            cart[index].qty -= 1;
            if (cart[index].qty < 1) cart.splice(index, 1);
        } else if (event.target.matches(".cart-remove")) {
            cart.splice(index, 1);
        } else {
            return;
        }

        saveCart(cart);
        renderCart();
    });

    renderCart();
}

function renderCart() {
    const tbody = document.getElementById("cart-body");
    if (!tbody) return;

    const table = document.querySelector(".cart-table");
    const emptyMsg = document.querySelector(".cart-empty");
    const totalEl = document.getElementById("cart-total");
    const cart = getCart();

    if (cart.length === 0) {
        table.hidden = true;
        emptyMsg.hidden = false;
        tbody.innerHTML = "";
        totalEl.textContent = "0.00";
        return;
    }

    table.hidden = false;
    emptyMsg.hidden = true;

    let grandTotal = 0;
    tbody.innerHTML = cart.map((item, index) => {
        const subtotal = calculatePrice(item.pricePerKg, item.grams) * item.qty;
        grandTotal += subtotal;

        return `
            <tr data-index="${index}">
                <td>${escapeHtml(item.name)}</td>
                <td>${formatWeight(item.grams)}</td>
                <td>
                    <div class="qty-controls">
                        <button type="button" class="qty-minus">-</button>
                        <span>${item.qty}</span>
                        <button type="button" class="qty-plus">+</button>
                    </div>
                </td>
                <td>R ${formatMoney(subtotal)}</td>
                <td><button type="button" class="cart-remove" aria-label="Remove item">&times;</button></td>
            </tr>`;
    }).join("");

    totalEl.textContent = formatMoney(grandTotal);
}

/* ---------- Order form (Products page) ---------- */

function setupOrderForm() {
    const form = document.getElementById("order-form");
    if (!form) return;

    form.addEventListener("submit", (event) => {
        const cart = getCart();

        if (cart.length === 0) {
            event.preventDefault();
            alert("Your cart is empty. Please add some products first.");
            return;
        }

        if (!validateRequiredFields(form)) {
            event.preventDefault();
            return;
        }

        // Send the cart to PHP as JSON in a hidden field
        const cartField = document.getElementById("cart-data");
        cartField.value = JSON.stringify(cart.map((item) => ({
            id: item.id,
            name: item.name,
            grams: item.grams,
            qty: item.qty,
            pricePerKg: item.pricePerKg,
            total: calculatePrice(item.pricePerKg, item.grams) * item.qty
        })));

        // The form then submits normally to php/submit_order.php
    });
}

/* ---------- Contact form (Contact page) ---------- */

function setupContactForm() {
    const form = document.getElementById("contact-form");
    if (!form) return;

    const status = document.getElementById("form-status");
    const submitBtn = form.querySelector("button[type='submit']");

    form.addEventListener("submit", async (event) => {
        event.preventDefault();
        status.textContent = "";
        status.className = "form-status";

        if (!validateRequiredFields(form)) {
            status.textContent = "Please fill in all required fields.";
            status.classList.add("error");
            return;
        }

        submitBtn.disabled = true;
        submitBtn.textContent = "Sending...";

        try {
            const response = await fetch(form.action, {
                method: "POST",
                body: new FormData(form)
            });

            if (!response.ok) throw new Error("Server error");

            status.textContent = "Thank you! Your message has been sent.";
            status.classList.add("success");
            form.reset();
        } catch (error) {
            status.textContent = "Sorry, something went wrong. Please call or WhatsApp us instead.";
            status.classList.add("error");
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = "Send Message";
        }
    });
}

/* ---------- Form validation ---------- */

function validateRequiredFields(form) {
    let valid = true;

    form.querySelectorAll("[required]").forEach((field) => {
        const value = field.value.trim();
        let fieldValid = value !== "";

        // Basic email and phone checks
        if (fieldValid && field.type === "email") {
            fieldValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
        }
        if (fieldValid && field.type === "tel") {
            fieldValid = /^[0-9+\s()-]{7,}$/.test(value);
        }

        field.classList.toggle("field-error", !fieldValid);
        if (!fieldValid) valid = false;
    });

    return valid;
}

/* ---------- Link from Home page: highlight product ---------- */

function highlightProductFromHash() {
    const id = window.location.hash.slice(1);
    if (!id) return;

    const card = document.getElementById(id);
    if (card && card.classList.contains("product-card")) {
        card.classList.add("highlight");
        setTimeout(() => card.classList.remove("highlight"), 2500);
    }
}
