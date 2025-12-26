import "preline";

import "flowbite";

import "select2";
import "select2/dist/css/select2.min.css";

import { Modal } from "flowbite";

const $modalElement = document.querySelector("#modalEl");

const modalOptions = {
    placement: "bottom-right",
    backdrop: "dynamic",
    backdropClasses: "bg-gray-900/50 dark:bg-gray-900/80 fixed inset-0 z-40",
    onHide: () => {
        console.log("modal is hidden");
    },
    onShow: () => {
        console.log("modal is shown");
    },
    onToggle: () => {
        console.log("modal has been toggled");
    },
};

const modal = new Modal($modalElement, modalOptions);

modal.show();

// Initialize Preline components on Livewire load and after DOM updates
document.addEventListener("livewire:load", () => {
    if (
        window.HSStaticMethods &&
        typeof window.HSStaticMethods.autoInit === "function"
    ) {
        window.HSStaticMethods.autoInit();
    }

    // Initialize Select2
    initSelect2();

    if (window.Livewire && typeof window.Livewire.hook === "function") {
        window.Livewire.hook("message.processed", () => {
            if (
                window.HSStaticMethods &&
                typeof window.HSStaticMethods.autoInit === "function"
            ) {
                window.HSStaticMethods.autoInit();
            }
            // Re-init Select2 after Livewire updates
            initSelect2();
        });
    }
});

function initSelect2() {
    if (typeof window.$ !== "undefined") {
        $(".select2").each(function () {
            const $this = $(this);
            if ($this.hasClass("select2-hidden-accessible")) {
                $this.select2("destroy");
            }
            $this
                .select2({
                    placeholder: "Tafuta mwanachama...",
                    allowClear: true,
                    width: "100%",
                })
                .on("change", function () {
                    const event = new Event("change", { bubbles: true });
                    this.dispatchEvent(event);
                });
        });
    }
}
