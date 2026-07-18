// student_fines.js - Load and display student's fines
document.addEventListener("DOMContentLoaded", function() {
    loadFines();
    
    // Check authentication
    fetch("check_auth.php")
        .then(res => res.json())
        .then(user => {
            if (user.status !== "ok") {
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
                // No fines - show success message
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="empty-state">
                            <i class="bi bi-check-circle-fill"></i>
                            <h4>No Due Fines!</h4>
                            <p>You have no pending fines. Great job returning books on time!</p>
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
                        <td><strong>${escapeHtml(fine.book_title)}</strong></td>
                        <td class="fine-amount">${fine.amount} TK</td>
                        <td>${fine.created_at || 'N/A'}</td>
                        <td><span class="status-pending">Pending</span></td>
                    </tr>
                `;
            });
            
            // Add total row
            tbody.innerHTML += `
                <tr class="total-fine-row">
                    <td><strong>Total</strong></td>
                    <td><strong>Due Fines</strong></td>
                    <td class="fine-amount"><strong>${totalFines} TK</strong></td>
                    <td colspan="2"></td>
                </tr>
            `;
        })
        .catch(error => {
            console.error("Error:", error);
            document.getElementById("fineTable").innerHTML = `
                <tr>
                    <td colspan="5" class="error-state">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <h5>Error Loading Fines</h5>
                        <p>Unable to load your fine details. Please try again later.</p>
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