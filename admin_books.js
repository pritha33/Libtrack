// admin_books.js
document.addEventListener("DOMContentLoaded", () => {
    loadBooks();
    document.getElementById("addBookForm").addEventListener("submit", addBook);
});

function loadBooks() {
    fetch("get_books.php")
        .then(res => res.json())
        .then(books => {
            let rows = "";
            books.forEach((book, index) => {
                rows += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${escapeHtml(book.title)}</td>
                        <td>${escapeHtml(book.author)}</td>
                        <td>${escapeHtml(book.category || '-')}</td>
                        <td>${escapeHtml(book.isbn || '-')}</td>
                        <td>${book.copies}</td>
                        <td>
                            <button class="action-btn delete" onclick="deleteBook(${book.id})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            document.getElementById("adminBookTable").innerHTML = rows;
        });
}

function addBook(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    
    fetch("manage_books.php?action=add", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(() => {
        e.target.reset();
        bootstrap.Modal.getInstance(document.getElementById("addBookModal")).hide();
        loadBooks();
    });
}

function deleteBook(id) {
    if (!confirm("Delete this book?")) return;
    
    fetch(`manage_books.php?action=delete&id=${id}`)
        .then(res => res.text())
        .then(() => loadBooks());
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