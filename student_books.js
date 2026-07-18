// student_books.js - Load issued books for student
document.addEventListener("DOMContentLoaded", function() {
    loadMyBooks();
    
    // Check authentication
    fetch("check_auth.php")
        .then(res => res.json())
        .then(user => {
            if (user.status !== "ok") {
                window.location.href = "login.html";
            }
        });
});

function loadMyBooks() {
    fetch("get_my_books.php")
        .then(res => res.json())
        .then(books => {
            const tbody = document.getElementById("myBooksTable");
            tbody.innerHTML = "";
            
            if (books.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="empty-state">
                            <i class="bi bi-journal-bookmark"></i>
                            <h5>No Books Issued</h5>
                            <p>Go to the Books page to request books</p>
                            <a href="books.html" class="btn-request mt-2" style="display: inline-block; text-decoration: none;">Browse Books</a>
                        </td>
                    </tr>
                `;
                return;
            }
            
            books.forEach(book => {
                const today = new Date();
                const dueDate = new Date(book.due_date);
                const isOverdue = dueDate < today;
                const diffDays = Math.ceil((dueDate - today) / (1000 * 60 * 60 * 24));
                
                let statusBadge = '';
                let daysInfo = '';
                
                if (isOverdue) {
                    statusBadge = '<span class="badge-overdue">Overdue</span>';
                    daysInfo = `<div class="days-overdue days-info">${Math.abs(diffDays)} days overdue</div>`;
                } else {
                    statusBadge = '<span class="badge-active">Active</span>';
                    daysInfo = `<div class="days-left days-info">${diffDays} days left</div>`;
                }
                
                tbody.innerHTML += `
                    <tr>
                        <td><strong>${escapeHtml(book.title)}</strong></td>
                        <td>${escapeHtml(book.author)}</td>
                        <td>${book.issue_date}</td>
                        <td>${book.due_date} ${daysInfo}</td>
                        <td>${statusBadge}</td>
                        <td><button class="btn-return" onclick="returnBook(${book.id})">Return Book</button></td>
                    </tr>
                `;
            });
        })
        .catch(error => {
            console.error("Error:", error);
            document.getElementById("myBooksTable").innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-danger py-4">
                        <i class="bi bi-exclamation-triangle"></i> Error loading your books
                    </td>
                </tr>
            `;
        });
}

function returnBook(issueId) {
    if (!confirm("Are you sure you want to return this book?")) {
        return;
    }
    
    fetch("return_book.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "issue_id=" + issueId
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.status === "success") {
            loadMyBooks();
        }
    })
    .catch(error => {
        alert("Error returning book");
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