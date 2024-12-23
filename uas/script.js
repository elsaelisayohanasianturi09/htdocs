async function submitForm(event) {
    event.preventDefault();

    const formData = new FormData(event.target);
    const data = Object.fromEntries(formData.entries());

    try {
        const response = await fetch('index.php', {
            method: 'POST',
            body: new URLSearchParams(data),
        });

        const result = await response.text();
        if (response.ok) {
            alert(result);  // Menampilkan pesan sukses
            loadSupportMessages();  // Memuat ulang pesan dukungan
            event.target.reset();  // Mereset form setelah pengiriman
        } else {
            alert(`Error: ${result}`);
        }
    } catch (error) {
        console.error("Error submitting form:", error);
        alert("Terjadi kesalahan saat mengirim data.");
    }
}

async function loadSupportMessages() {
    try {
        const response = await fetch('index.php'); // Mengambil data dari halaman index.php
        const data = await response.json();

        const tableBody = document.querySelector("#support-table tbody");
        tableBody.innerHTML = ""; // Bersihkan tabel sebelumnya

        data.forEach((message) => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${message.name}</td>
                <td>${message.email}</td>
                <td>${message.country}</td>
                <td>${message.birthdate}</td>
                <td>${message.message}</td>
                <td>${message.rating}</td>
                <td>${message.date}</td>
            `;
            tableBody.appendChild(row);
        });
    } catch (error) {
        console.error("Error fetching messages:", error);
        alert("Terjadi kesalahan saat memuat pesan.");
    }
}

document.querySelector("#support-form").addEventListener("submit", submitForm);
document.addEventListener("DOMContentLoaded", loadSupportMessages);
