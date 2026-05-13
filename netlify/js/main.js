const menuButton = document.querySelector(".menu-toggle");
const navLinks = document.querySelector(".nav-links");

if (menuButton && navLinks) {
  menuButton.addEventListener("click", () => {
    const isOpen = navLinks.classList.toggle("is-open");
    menuButton.setAttribute("aria-expanded", String(isOpen));
  });

  navLinks.addEventListener("click", (event) => {
    if (event.target.matches("a")) {
      navLinks.classList.remove("is-open");
      menuButton.setAttribute("aria-expanded", "false");
    }
  });
}

const staticSortForm = document.querySelector(".static-sort-form");
const staticSortSelect = staticSortForm?.querySelector("select[name='orden']");
const shopGrid = document.querySelector(".shop-product-grid");
const categoryLinks = document.querySelectorAll(".shop-categories [data-category]");
const toolbarCount = document.querySelector(".shop-toolbar p");

function productCards() {
  return Array.from(document.querySelectorAll("[data-product-card]"));
}

function productPrice(card) {
  const text = card.querySelector(".price")?.textContent || "0";
  return Number(text.replace(/[^0-9.]/g, "")) || 0;
}

function productName(card) {
  return card.querySelector("h3")?.textContent.trim().toLowerCase() || "";
}

function updateVisibleCount() {
  if (!toolbarCount) return;
  const total = productCards().filter((card) => card.style.display !== "none").length;
  toolbarCount.innerHTML = `Mostrando <strong>${total}</strong> resultado${total === 1 ? "" : "s"}`;
}

if (staticSortForm && staticSortSelect && shopGrid) {
  staticSortForm.addEventListener("submit", (event) => event.preventDefault());
  staticSortSelect.addEventListener("change", () => {
    const sorted = productCards().sort((a, b) => {
      if (staticSortSelect.value === "precio_asc") return productPrice(a) - productPrice(b);
      if (staticSortSelect.value === "precio_desc") return productPrice(b) - productPrice(a);
      if (staticSortSelect.value === "nombre") return productName(a).localeCompare(productName(b));
      return 0;
    });
    sorted.forEach((card) => shopGrid.appendChild(card));
  });
}

if (categoryLinks.length) {
  categoryLinks.forEach((link) => {
    link.addEventListener("click", (event) => {
      event.preventDefault();
      const category = link.dataset.category;
      categoryLinks.forEach((item) => item.classList.remove("active"));
      link.classList.add("active");
      productCards().forEach((card) => {
        const productCategory = card.querySelector(".product-category")?.textContent.trim();
        card.style.display = category === "all" || productCategory === category ? "" : "none";
      });
      updateVisibleCount();
    });
  });
}
