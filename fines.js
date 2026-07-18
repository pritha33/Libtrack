// fines.js
document.addEventListener("DOMContentLoaded", loadFines);

function loadFines() {
    fetch("get_fines.php")
        .then(res => res.json())
        .then(fines => {
            const tbody = document.getElementById("fineTable");
            tbody.innerHTML = "";
            
            if (fines.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center">No fines found</td></tr>';
                return;
            }
            
            fines.forEach(fine => {
                const statusClass = fine.status === 'pending' ? 'fine-unpaid' : 'fine-paid';
                const statusText = fine.status === 'pending' ? 'Unpaid' : 'Paid';
                
                tbody.innerHTML += `
                    <tr>
                        <td>${escapeHtml(fine.user_name)}</td>
                        <td>${escapeHtml(fine.book_title)}</td>
                        <td>${fine.amount} tk</td>
                        <td class="${statusClass}">${statusText}</td>
                        <td>
                            ${fine.status === 'pending' ? 
                                `<button class="btn-action btn-pay" onclick="payFine(${fine.id})">Mark Paid</button>` : 
                                '—'}
                        </td>
                    </tr>
                `;
            });
        });
}

function payFine(id) {
    if (confirm("Mark this fine as paid?")) {
        const formData = new FormData();
        formData.append("fine_id", id);
        
        fetch("pay_fine.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === "success") {
                alert("Fine marked as paid");
                loadFines();
            }
        });
    }
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