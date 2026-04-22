import { init } from 'klinecharts'

document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('depositButton')
    const payBtn = document.getElementById('btn-pay')
    const money = document.getElementById('money')
    if (!el) return
    el.addEventListener("click", function () {
      const bankId = "970436";      // MB Bank (ví dụ)
      const accountNo = "123456789";
      const amount = money.value;         // số tiền
      const content = "Nap tien";   // nội dung

      const qrUrl = `https://img.vietqr.io/image/${bankId}-${accountNo}-compact.png?amount=${amount}&addInfo=${encodeURIComponent(content)}`;

      document.getElementById("qr-code").innerHTML =
        `<img style="width: 60%; height: 60%;" src="${qrUrl}" alt="QR Code" />`;
    });

    payBtn.addEventListener("click", function () {
      document.getElementById("qr-code").innerHTML = "";
      money.value = "";
      const modal = new bootstrap.Modal(document.getElementById("myModal"));
      modal.show();
    });
    document.querySelectorAll(".checkout-tab")&&Array.from(document.querySelectorAll(".checkout-tab")).forEach(function(t){t.querySelectorAll(".nexttab")&&t.querySelectorAll(".nexttab").forEach(function(o){var e=t.querySelectorAll('button[data-bs-toggle="pill"]');Array.from(e).forEach(function(e){e.addEventListener("show.bs.tab",function(e){e.target.classList.add("done")})}),o.addEventListener("click",function(){var e=o.getAttribute("data-nexttab");document.getElementById(e).click()})}),t.querySelectorAll(".previestab")&&Array.from(t.querySelectorAll(".previestab")).forEach(function(r){r.addEventListener("click",function(){for(var e=r.getAttribute("data-previous"),o=r.closest("form"),t=o-1;t<o;t++)r.closest("form").querySelectorAll(".custom-nav .done")[t]&&r.closest("form").querySelectorAll(".custom-nav .done")[t].classList.remove("done");document.getElementById(e).click()})});var r=t.querySelectorAll('button[data-bs-toggle="pill"]');r&&Array.from(r).forEach(function(e,o){e.setAttribute("data-position",o),e.addEventListener("click",function(){0<t.querySelectorAll(".custom-nav .done").length&&Array.from(t.querySelectorAll(".custom-nav .done")).forEach(function(e){e.classList.remove("done")});for(var e=0;e<=o;e++)r[e].classList.contains("active")?r[e].classList.remove("done"):r[e].classList.add("done")})})});var previewTemplate,dropzone,dropzonePreviewNode=document.querySelector("#dropzone-preview-list");dropzonePreviewNode&&(dropzonePreviewNode.id="",previewTemplate=dropzonePreviewNode.parentNode.innerHTML,dropzonePreviewNode.parentNode.removeChild(dropzonePreviewNode)),document.querySelector(".dropzone")&&(dropzone=new Dropzone(".dropzone",{url:"https://httpbin.org/post",method:"post",previewTemplate:previewTemplate,previewsContainer:"#dropzone-preview"}));
})