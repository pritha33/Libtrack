// books.js - Load and display available books
document.addEventListener("DOMContentLoaded", function() {
    loadBooks();
    
    // Check authentication
    fetch("check_auth.php")
        .then(res => res.json())
        .then(user => {
            if (user.status !== "ok") {
                window.location.href = "login.html";
            }
        });
});

function loadBooks() {
    fetch("get_books.php")
        .then(res => res.json())
        .then(books => {
            const tbody = document.getElementById("bookTable");
            tbody.innerHTML = "";
            
            if (books.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="empty-state">
                            <i class="bi bi-book"></i>
                            <h5>No Books Available</h5>
                            <p>Check back later for new books</p>
                        </td>
                    </tr>
                `;
                return;
            }
            
            books.forEach((book, index) => {
                const status = book.available_copies > 0 
                    ? `<span class="badge-available">Available (${book.available_copies})</span>`
                    : `<span class="badge-unavailable">Not Available</span>`;
                
                const actionBtn = book.available_copies > 0
                    ? `<button class="btn-request" onclick="requestBook(${book.id})">Request Book</button>`
                    : `<button class="btn-unavailable" disabled>Unavailable</button>`;
                
                tbody.innerHTML += `
                    <tr>
                        <td>${index + 1}</td>
                        <td><strong>${escapeHtml(book.title)}</strong></td>
                        <td>${escapeHtml(book.author)}</td>
                        <td>${escapeHtml(book.category || '-')}</td>
                        <td>${escapeHtml(book.isbn || '-')}</td>
                        <td>${status}</td>
                        <td>${actionBtn}</td>
                    </tr>
                `;
            });
        })
        .catch(error => {
            console.error("Error:", error);
            document.getElementById("bookTable").innerHTML = `
                <tr>
                    <td colspan="7" class="text-center text-danger py-4">
                        <i class="bi bi-exclamation-triangle"></i> Error loading books
                    </td>
                <tr>
            `;
        });
}

function requestBook(bookId) {
    if (!confirm("Request this book? Admin will review your request.")) {
        return;
    }
    
    fetch("request_book.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "book_id=" + bookId
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.status === "success") {
            loadBooks(); // Refresh the list
        }
    })
    .catch(error => {
        alert("Error sending request");
    });
}

function escapeHtml(str) {
    if (!str) return "";
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}