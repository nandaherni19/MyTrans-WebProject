function togglePassword(id, el) {
    const input = document.getElementById(id);

    if (input.type === "password") {
        input.type = "text";
        el.textContent = "visibility_off";
    } else {
        input.type = "password";
        el.textContent = "visibility";
    }
}

// validasi sederhana
document.addEventListener("DOMContentLoaded", () => {
    const phone = document.getElementById("phone_number");

    phone.addEventListener("input", function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
});