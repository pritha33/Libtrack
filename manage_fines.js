// manage_fines.js - Load and display fines
document.addEventListener("DOMContentLoaded", function() {
    loadFines();
    
    // Check authentication
    fetch("check_auth.php")
        .then(res => res.json())
        .then(user => {
            if (user.role !== "admin") {
                window.location.href = "login.html";
            }
        });
});

function loadFines() {
    fetch("get_fines.php")
        .then(res => res.json())
        .then(fines => {
            const tbody = document.getElementById("fineTable");
            tbody.innerHTML = "";
            
            if (fines.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="empty-state">
                            <i class="bi bi-check-circle"></i>
                            <h5>No Due Fines</h5>
                            <p>All students have returned books on time</p>
                        </td>
                    </tr>
                `;
                return;
            }
            
            let totalFines = 0;
            
            fines.forEach((fine, index) => {
                totalFines += parseFloat(fine.amount);
                tbody.innerHTML += `
                    <tr>
                        <td>${index + 1}</td>
                        <td><strong>${escapeHtml(fine.user_name)}</strong></td>
                        <td>${escapeHtml(fine.book_title)}</strong></td>
                        <td class="fine-amount">${fine.amount} TK</td>
                        <td>${fine.created_at}</td>
                        <td><span class="status-pending">Pending</span></td>
                    </tr>
                `;
            });
            
            // Add total row
            tbody.innerHTML += `
                <tr class="total-fine-row">
                    <td><strong>Total</strong></td>
                    <td><td colspan="2"><strong>Due Fines</strong></td>
                    <td class="fine-amount"><strong>${totalFines} TK</strong></td>
                    <td><td colspan="2"></td>
                </tr>
            `;
        })
        .catch(error => {
            console.error("Error:", error);
            document.getElementById("fineTable").innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-danger py-4">
                        <i class="bi bi-exclamation-triangle"></i> Error loading fines
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