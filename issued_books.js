// issued_books.js - Load all issued books
document.addEventListener("DOMContentLoaded", function() {
    loadIssuedBooks();
    
    // Check authentication
    fetch("check_auth.php")
        .then(res => res.json())
        .then(user => {
            if (user.role !== "admin") {
                window.location.href = "login.html";
            }
        });
});

function loadIssuedBooks() {
    fetch("get_issued_books.php")
        .then(res => res.json())
        .then(books => {
            const tbody = document.getElementById("issuedBooksTable");
            tbody.innerHTML = "";
            
            if (books.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="bi bi-inbox" style="font-size: 48px; color: #1ABC9C;"></i>
                            <h5 class="mt-2">No Issued Books</h5>
                            <p class="text-muted">No books have been issued yet</p>
                        </td>
                    </tr>
                `;
                return;
            }
            
            books.forEach((book, index) => {
                // Determine status badge
                let statusBadge = '';
                let statusClass = '';
                
                if (book.status === 'issued') {
                    // Check if overdue
                    const today = new Date();
                    const dueDate = new Date(book.due_date);
                    if (dueDate < today) {
                        statusBadge = '<span class="status-overdue">Overdue</span>';
                    } else {
                        statusBadge = '<span class="status-issued">Issued</span>';
                    }
                } else {
                    statusBadge = '<span class="status-returned">Returned</span>';
                }
                
                tbody.innerHTML += `
                    <tr>
                        <td>${index + 1}</td>
                        <td><strong>${escapeHtml(book.student_name)}</strong><br>
                            <small class="text-muted">${escapeHtml(book.email)}</small>
                        </td>
                        <td><strong>${escapeHtml(book.book_title)}</strong><br>
                            <small class="text-muted">ISBN: ${escapeHtml(book.isbn || '-')}</small>
                        </td>
                        <td>${escapeHtml(book.author)}</td>
                        <td>${book.issue_date}</td>
                        <td>${book.due_date}</td>
                        <td>${statusBadge}</td>
                    </tr>
                `;
            });
        })
        .catch(error => {
            console.error("Error:", error);
            document.getElementById("issuedBooksTable").innerHTML = `
                <tr>
                    <td colspan="7" class="text-center text-danger py-4">
                        <i class="bi bi-exclamation-triangle"></i> Error loading issued books
                    </td>
                </tr>
            `;
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