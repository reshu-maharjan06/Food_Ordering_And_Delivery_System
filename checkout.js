document.addEventListener("DOMContentLoaded", () => {
  // Payment Method Selection
  const pmOptions = document.querySelectorAll(".pm-opt");
  pmOptions.forEach((opt) => {
    opt.addEventListener("click", () => {
      pmOptions.forEach((o) => o.classList.remove("active"));
      opt.classList.add("active");
    });
  });

  // Modal Logic
  const confirmBtn = document.getElementById("btn-confirm");
  const modal = document.getElementById("confirm-modal");
  const btnBack = document.getElementById("btn-modal-back");
  const btnPlace = document.getElementById("btn-modal-place");

  confirmBtn.addEventListener("click", () => {
    // Find which payment method is active
    const isCod = document
      .getElementById("pm-cod")
      .classList.contains("active");

    if (isCod) {
      // Update modal text for COD
      modal.querySelector("p").innerHTML =
        `Are you sure you want to place this order using <br><b>Cash on Delivery</b>? You will need to pay the total <br>amount upon arrival.`;
    } else {
      // Update modal text for eSewa
      modal.querySelector("p").innerHTML =
        `Are you sure you want to place this order using <br><b>eSewa Payment</b>? You will be redirected to the<br>eSewa portal to complete payment.`;
    }

    modal.classList.add("active");
  });

  btnBack.addEventListener("click", () => {
    modal.classList.remove("active");
  });

  btnPlace.addEventListener("click", () => {
    const isCod = document
      .getElementById("pm-cod")
      .classList.contains("active");
    if (isCod) {
      btnPlace.innerHTML = "Processing...";
      setTimeout(() => {
        window.location.href = "payment-success.html?method=cod";
      }, 1000);
    } else {
      btnPlace.innerHTML = "Redirecting...";
      setTimeout(() => {
        window.location.href = "esewa-mock.html?amt=700.00";
      }, 1000);
    }
  });

  // Initialize Map (Leaflet)
  if (typeof L !== "undefined") {
    // Map of Kathmandu roughly
    const map = L.map("map", {
      zoomControl: false, // custom position or styling if needed
    }).setView([27.7172, 85.324], 14);

    L.tileLayer(
      "https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png",
      {
        attribution:
          '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: "abcd",
        maxZoom: 20,
      },
    ).addTo(map);

    // Add Zoom Control to bottom right
    L.control
      .zoom({
        position: "bottomright",
      })
      .addTo(map);

    // Add Marker
    const customIcon = L.divIcon({
      html: `<svg viewBox="0 0 24 24" width="36" height="36" fill="#111"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>`,
      className: "",
      iconSize: [36, 36],
      iconAnchor: [18, 36],
    });

    const marker = L.marker([27.7172, 85.324], {
      icon: customIcon,
      draggable: true,
    }).addTo(map);

    // Update marker on click
    map.on("click", function (e) {
      marker.setLatLng(e.latlng);
    });
  }
});
