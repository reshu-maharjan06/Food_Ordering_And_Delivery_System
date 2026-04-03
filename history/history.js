(function () {
  const user = (() => {
    try {
      return JSON.parse(sessionStorage.getItem("sauni_user"));
    } catch {
      return null;
    }
  })();

  const logoutLink = document.getElementById("logout-link");
  if (logoutLink) {
    logoutLink.addEventListener("click", (e) => {
      e.preventDefault();
      sessionStorage.removeItem("sauni_user");
      location.href = "../login/index.html";
    });
  }
})();

async function loadHistory() {
  try {
    const res = await fetch("../api.php?action=customer_orders");
    const data = await res.json();
    const box = document.getElementById("histBody");
    const table = document.getElementById("histTable");

    if (!data.length) {
      table.style.display = "none";
      document.querySelector(".pg-wrap").insertAdjacentHTML(
        "beforeend",
        `
                <div class="empty-state">
                    <h2>No Adventures Yet.</h2>
                    <p>Your culinary journey starts with your first order.</p>
                    <a href="../landing/landing.html" class="btn-action">Return to Kitchen</a>
                </div>`,
      );
      return;
    }

    box.innerHTML = data
      .map((o) => {
        const items = JSON.parse(o.items_json || "[]");
        const date = new Date(o.created_at).toLocaleDateString(undefined, {
          month: "short",
          day: "numeric",
          year: "numeric",
        });
        const isActive = !["delivered", "cancelled"].includes(o.status);
        const statusClass =
          o.status === "delivered" ? "delivered" : isActive ? "active" : "";

        // Action cell: track if active, review button if done & unreviewed, badge if reviewed
        const actionCell = isActive
          ? `<a href="../add-to-cart/tracker.html" class="btn-action">Track Live</a>`
          : Number(o.has_reviewed)
            ? `<span class="o-status delivered">Reviewed ✓</span>`
            : `<button class="btn-action" style="background:#4b5563" onclick="openReviewModal(${o.id})">Leave Review</button>`;

        return `
                <tr class="order-row">
                    <td class="o-id">#${o.id}</td>
                    <td class="o-date">${date}</td>
                    <td class="o-items">${items.map((i) => `${i.qty}× ${i.name}`).join(", ")}</td>
                    <td class="o-total"><span>Rs.</span>${o.total_amount}</td>
                    <td><span class="o-status ${statusClass}">${o.status}</span></td>
                    <td>${actionCell}</td>
                </tr>`;
      })
      .join("");
  } catch (err) {
    console.error("Failed to load history:", err);
  }
}

let activeOrderId = null;
let currentStars = 5;

function openReviewModal(oid) {
  activeOrderId = oid;
  currentStars = 5;
  document.getElementById("targetOrderId").textContent = oid;
  document.getElementById("revComment").value = "";
  document.getElementById("reviewModal").classList.add("open");
  updateStars();
}

function closeReviewModal() {
  document.getElementById("reviewModal").classList.remove("open");
  document.getElementById("revComment").value = "";
}

// Star click handlers
document.querySelectorAll(".star").forEach((s) => {
  s.addEventListener("click", function () {
    currentStars = parseInt(this.dataset.val);
    updateStars();
  });
  // Hover preview
  s.addEventListener("mouseenter", function () {
    const val = parseInt(this.dataset.val);
    document.querySelectorAll(".star").forEach((st) => {
      st.classList.toggle("on", parseInt(st.dataset.val) <= val);
    });
  });
});

document.getElementById("starWrap").addEventListener("mouseleave", updateStars);

function updateStars() {
  document.querySelectorAll(".star").forEach((s) => {
    s.classList.toggle("on", parseInt(s.dataset.val) <= currentStars);
  });
}

// Close modal when clicking backdrop
document.getElementById("reviewModal").addEventListener("click", (e) => {
  if (e.target === e.currentTarget) closeReviewModal();
});

async function submitReview() {
  const comment = document.getElementById("revComment").value;
  try {
    const res = await fetch("../api.php?action=rate_order", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        order_id: activeOrderId,
        rating: currentStars,
        comment,
      }),
    });
    const d = await res.json();
    if (d.success) {
      closeReviewModal();
      loadHistory();
    } else {
      alert(d.error || "Failed to submit review");
    }
  } catch (err) {
    alert("Network error. Please try again.");
  }
}

loadHistory();
