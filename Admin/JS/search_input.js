document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("buscador");
    const tableBody = document.getElementById("table_cat");

    searchInput.addEventListener("input", async () => {
        const query = searchInput.value.trim();

        if (query.length > 0) {
            try {
                const response = await fetch(`/search?query=${encodeURIComponent(query)}`);
                if (response.ok) {
                    const results = await response.json();
                    updateTable(results);
                } else {
                    console.error("Error fetching search results");
                }
            } catch (error) {
                console.error("Error:", error);
            }
        } else {
            clearTable();
        }
    });

    function updateTable(data) {
        tableBody.innerHTML = "";
        data.forEach(row => {
            const tr = document.createElement("tr");
            Object.values(row).forEach(cellData => {
                const td = document.createElement("td");
                td.textContent = cellData;
                tr.appendChild(td);
            });
            tableBody.appendChild(tr);
        });
    }

    function clearTable() {
        tableBody.innerHTML = "";
    }
});
