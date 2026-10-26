document.addEventListener("DOMContentLoaded", () => {
    function wirePreview(inputId, previewId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        if (!input || !preview) return;

        input.addEventListener("change", () => {
            const file = input.files && input.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = (e) => {
                preview.src = e.target.result;
                preview.style.display = "block";
            };
            reader.readAsDataURL(file);
        });
    }

    wirePreview("bs_logo_input", "bs_logo_preview");
    wirePreview("bs_flyerbg_input", "bs_flyerbg_preview");

    // ----- Toasts -----
    const stack = document.getElementById("kToastStack");
    if (stack) {
        stack.addEventListener("click", (e) => {
            if (e.target.classList.contains("k-toast__close")) {
                const toast = e.target.closest(".k-toast");
                if (toast) toast.classList.add("k-toast--hide");
            }
        });

        setTimeout(() => {
            stack.querySelectorAll(".k-toast").forEach((t) => {
                t.classList.add("k-toast--hide");
            });
        }, 4000);
    }
});
