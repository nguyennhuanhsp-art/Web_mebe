if (window.location.hash === "#admin") {
  window.location.replace("/wp-admin/edit.php?post_type=product");
}

document.querySelectorAll(".hero-slides").forEach((box) => {
  const shots = Array.from(box.querySelectorAll("img"));
  if (!shots.length) return;
  let index = Math.floor(Math.random() * shots.length);
  const show = (next) => {
    shots.forEach((img, i) => img.classList.toggle("is-on", i === next));
    box.classList.add("is-ready");
  };
  show(index);
  if (shots.length < 2 || window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;
  setInterval(() => {
    index = (index + 1) % shots.length;
    show(index);
  }, 4000);
});

const pages = [
  ["Trang chủ", "index.html", "Thanh Lan Spa chăm sóc mẹ và bé trước và sau sinh"],
  ["Giới thiệu", "gioi-thieu.html", "Tô Lan đại diện thương hiệu Thanh Lan Spa"],
  ["Dịch Vụ", "dich-vu.html", "Các dịch vụ chăm sóc tại nhà"],
  ["Tắm bé sơ sinh", "tam-be.html", "Tắm và massage bé sơ sinh tại nhà"],
  ["Chăm sóc mẹ bé", "cham-soc-me-be.html", "Chăm sóc mẹ sau sinh"],
  ["Ở cữ tại nhà", "o-cu-tai-nha.html", "Đồng hành những tuần ở cữ tại nhà"],
  ["Chăm sóc bầu tại nhà", "cham-soc-bau.html", "Massage và nghỉ ngơi cho mẹ bầu"],
  ["Thông tắc tia sữa", "thong-tac-tia-sua.html", "Hỗ trợ thông tắc tia sữa tại nhà"],
  ["Giảm eo sau sinh", "giam-eo.html", "Lấy lại dáng nhẹ nhàng sau sinh"],
  ["Cửa hàng", "cua-hang.html", "Sản phẩm minh họa cho mẹ bầu mẹ và bé"],
  ["Chăm sóc bầu", "san-pham-bau.html", "Dầu massage bầu"],
  ["Chăm sóc bé", "san-pham-be.html", "Dầu tắm bé"],
  ["Chăm sóc mẹ", "san-pham-me.html", "Muối thảo dược"],
  ["Chăm sóc tóc", "san-pham-toc.html", "Dầu dưỡng tóc sau sinh"],
  ["Feedback", "feedback.html", "Nhật ký mẹ sau buổi chăm"],
  ["Kiến thức", "kien-thuc.html", "Bài viết minh họa trước và sau sinh"],
  ["Hướng dẫn chăm sóc bé", "cham-soc-be.html", "Massage bụng và giấc ngủ của bé"],
  ["Hướng dẫn mẹ", "huong-dan-me.html", "Việc mẹ làm trong tuần đầu"],
  ["Mẹ bầu cần biết", "me-bau.html", "Nghỉ ngơi những tuần cuối"],
  ["Mẹ sau sinh cần biết", "me-sau-sinh.html", "Ở cữ và tắc tia sữa"],
  ["Dấu ấn Thanh Lan Spa", "dau-an.html", "Hành trình Tô Lan và Thanh Lan Spa"],
  ["Nhận tư vấn", "tu-van.html", "Đặt lịch và nhận cẩm nang"]
];

function digitsOk(value) {
  const n = value.replace(/\D/g, "");
  return n.length >= 9 && n.length <= 12;
}

function postLead(fields) {
  if (!window.thanhLanLead) {
    return Promise.resolve({ ok: false, offline: true, message: "Đã lưu trên máy này." });
  }
  const body = new URLSearchParams(fields);
  body.set("action", "thanh_lan_lead");
  body.set("nonce", thanhLanLead.nonce);
  return fetch(thanhLanLead.ajax, {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body
  }).then((response) => response.json()).catch(() => ({ ok: false, message: "Mạng đang bận. Chị thử lại hoặc gọi 0975.399.319." }));
}

document.querySelectorAll(".menu-btn").forEach((button) => {
  button.addEventListener("click", () => {
    const menu = document.getElementById("menu");
    const open = menu.classList.toggle("open");
    button.setAttribute("aria-expanded", String(open));
  });
});

document.querySelectorAll(".sub-btn").forEach((button) => {
  button.addEventListener("click", () => {
    const item = button.closest("li");
    const open = item.classList.toggle("open");
    button.setAttribute("aria-expanded", String(open));
  });
});

document.querySelectorAll("[data-book]").forEach((button) => {
  button.addEventListener("click", () => {
    const modal = document.getElementById("booking");
    document.getElementById("bookService").value = button.dataset.book;
    document.getElementById("bookNote").classList.remove("show");
    document.getElementById("bookPhoneError").textContent = "";
    modal.classList.add("open");
    document.getElementById("bookName").focus();
  });
});

const booking = document.getElementById("booking");
document.getElementById("closeBooking").addEventListener("click", () => booking.classList.remove("open"));
booking.addEventListener("click", (event) => {
  if (event.target === booking) booking.classList.remove("open");
});
document.addEventListener("keydown", (event) => {
  if (event.key === "Escape") booking.classList.remove("open");
});

document.getElementById("bookingForm").addEventListener("submit", (event) => {
  event.preventDefault();
  const phone = document.getElementById("bookPhone").value;
  const error = document.getElementById("bookPhoneError");
  if (!digitsOk(phone)) {
    error.textContent = "Nhập số từ 9 đến 12 chữ số.";
    return;
  }
  error.textContent = "";
  const item = {
    service: document.getElementById("bookService").value,
    name: document.getElementById("bookName").value.trim(),
    phone: phone.trim(),
    date: document.getElementById("bookDate").value
  };
  const note = document.getElementById("bookNote");
  const website = document.getElementById("bookWebsite");
  const bookEmail = document.getElementById("bookEmail");
  postLead({
    name: item.name,
    phone: item.phone,
    email: bookEmail ? bookEmail.value.trim() : "",
    service: item.service,
    date: item.date,
    stage: "Đặt lịch",
    source: "Đặt lịch",
    website: website ? website.value : ""
  }).then((result) => {
    if (result.offline) {
      const list = JSON.parse(localStorage.getItem("thanhLanBookings") || "[]");
      list.push(item);
      localStorage.setItem("thanhLanBookings", JSON.stringify(list));
      note.textContent = "Đã lưu trên máy này. Thanh Lan sẽ gọi hoặc nhắn Zalo " + item.phone + ".";
    } else {
      note.textContent = result.message || "Đã nhận.";
    }
    note.classList.add("show");
  });
});

const handbook = document.getElementById("leadForm") || document.getElementById("handbookForm");
if (handbook) {
  const guides = {
    "Mẹ bầu": "Nằm nghiêng khi nghỉ, chuẩn bị túi đồ sinh, và nhắn Tô Lan để hẹn chăm sóc tuần cuối.",
    "Tuần đầu sau sinh": "Theo dõi rốn bé, nghỉ khi bé ngủ, và gọi nếu ngực căng quá một ngày.",
    "Bé 1 đến 6 tháng": "Giữ nhịp bú và ngủ, xoa bụng nhẹ khi bé đầy hơi sau khi đã ợ."
  };
  handbook.addEventListener("submit", (event) => {
    event.preventDefault();
    const phone = document.getElementById("leadPhone").value;
    const error = document.getElementById("leadPhoneError");
    if (!digitsOk(phone)) {
      error.textContent = "Nhập số từ 9 đến 12 chữ số.";
      return;
    }
    error.textContent = "";
    const emailInput = document.getElementById("leadEmail");
    const emailError = document.getElementById("leadEmailError");
    const email = emailInput ? emailInput.value.trim() : "";
    if (emailInput && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      if (emailError) emailError.textContent = "Nhập email để nhận thư cảm ơn.";
      return;
    }
    if (emailError) emailError.textContent = "";
    const when = document.getElementById("leadWhen");
    const note = document.getElementById("leadNote");
    if (when && !when.value) {
      note.textContent = "Chọn ngày giờ có thể nghe máy.";
      note.classList.add("show");
      return;
    }
    const name = document.getElementById("leadName").value.trim();
    const stage = document.getElementById("leadStage").value;
    const service = document.getElementById("leadService");
    const district = document.getElementById("leadDistrict");
    const message = document.getElementById("leadMessage");
    const website = document.getElementById("leadWebsite");
    postLead({
      name,
      phone: phone.trim(),
      email,
      stage,
      service: service ? service.value : stage,
      district: district ? district.value : "",
      message: message ? message.value : "",
      when: when ? when.value : "",
      source: "Form tư vấn",
      website: website ? website.value : ""
    }).then((result) => {
      if (result.offline) {
        const leads = JSON.parse(localStorage.getItem("thanhLanLeads") || "[]");
        leads.push({ name, phone: phone.trim(), stage, at: new Date().toISOString() });
        localStorage.setItem("thanhLanLeads", JSON.stringify(leads));
        const preview = document.getElementById("handbookPreview");
        const text = document.getElementById("handbookText");
        if (preview && text && guides[stage]) {
          text.textContent = name + " ơi, với giai đoạn " + stage + ": " + guides[stage];
          preview.hidden = false;
        }
        note.textContent = "Đã lưu trên máy này.";
      } else {
        note.textContent = result.message || "Đã nhận.";
      }
      note.classList.add("show");
    });
  });
}

const resultBox = document.getElementById("searchResults");
if (resultBox) {
  const params = new URLSearchParams(location.search);
  const query = (params.get("q") || "").trim().toLowerCase();
  const input = document.getElementById("q");
  if (input) input.value = params.get("q") || "";
  const found = pages.filter((item) => query && (item[0] + " " + item[2]).toLowerCase().includes(query));
  resultBox.innerHTML = "";
  if (!query) {
    resultBox.textContent = "Nhập từ khóa ở ô tìm kiếm trên menu.";
  } else if (!found.length) {
    resultBox.textContent = "Chưa có trang nào khớp “" + query + "”.";
  } else {
    found.forEach((item) => {
      const a = document.createElement("a");
      a.href = item[1];
      a.className = "card";
      a.style.padding = "14px 16px";
      a.innerHTML = "<h3></h3><p></p>";
      a.querySelector("h3").textContent = item[0];
      a.querySelector("p").textContent = item[2];
      resultBox.appendChild(a);
    });
  }
}
