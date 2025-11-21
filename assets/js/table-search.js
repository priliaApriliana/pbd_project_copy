document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");

    // deteksi otomatis tabel pertama di halaman
    const table = document.querySelector("table");
    const tableRows = table ? table.querySelectorAll("tbody tr") : [];

    if (!searchInput || tableRows.length === 0) return;

    searchInput.addEventListener("keyup", function () {
        const keyword = this.value.toLowerCase();

        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(keyword) ? "" : "none";
        });
    });
});
