// manage_requests.js - Fixed version
document.addEventListener("DOMContentLoaded", function() {
    loadRequests();
});

function loadRequests() {
    fetch("get_requests.php?" + new Date().getTime())
        .then(res => res.json())
        .then(requests => {
            const tbody = document.getElementById("requestTable");
            tbody.innerHTML = "";
            
            console.log("Requests loaded:", requests); // Debug log
            
            if (!requests || requests.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <i class="bi bi-inbox" style="font-size: 48px; color: #1ABC9C;"></i>
                            <h5 class="mt-2">No Pending Requests</h5>
                            <p class="text-muted">All book requests have been processed</p>
                        </td>
                    </tr>
                `;
                return;
            }
            
            requests.forEach(request => {
                // Only show pending requests
                if (request.status === 'pending') {
                    const row = tbody.insertRow();
                    row.innerHTML = `
                        <td><strong>${escapeHtml(request.user_name)}</strong><br>
                            <small class="text-muted">${escapeHtml(request.email)}</small></td>
                        <td><strong>${escapeHtml(request.book_title)}</strong><br>
                            <small class="text-muted">by ${escapeHtml(request.author)}</small></td>
                        <td>${request.request_date}</td>
                        <td><span class="status-pending">Pending</span></td>
                        <td>
                            <button class="btn-approve" onclick="approveRequest(${request.id})">
                                <i class="bi bi-check-circle"></i> Approve
                            </button>
                            <button class="btn-reject" onclick="rejectRequest(${request.id})">
                                <i class="bi bi-x-circle"></i> Reject
                            </button>
                        </td>
                    `;
                }
            });
            
            // If no pending requests found
            if (tbody.rows.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <i class="bi bi-check-circle" style="font-size: 48px; color: #28a745;"></i>
                            <h5 class="mt-2">No Pending Requests</h5>
                            <p class="text-muted">All requests have been processed</p>
                        </td>
                    </tr>
                `;
            }
        })
        .catch(error => {
            console.error("Error:", error);
            document.getElementById("requestTable").innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-danger py-4">
                        <i class="bi bi-exclamation-triangle"></i> Error loading requests
                    </td>
                </tr>
            `;
        });
}

function approveRequest(requestId) {
    if (!confirm("Approve this request and issue the book to student?")) {
        return;
    }
    
    const formData = new FormData();
    formData.append("request_id", requestId);
    
    fetch("approve_request.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            alert("✓ " + data.message);
            loadRequests(); // Refresh the list
        } else {
            alert("✗ Error: " + data.message);
        }
    })
    .catch(error => {
        alert("Error: Could not process request");
        console.error("Error:", error);
    });
}

function rejectRequest(requestId) {
    if (!confirm("Reject this request?")) {
        return;
    }
    
    const formData = new FormData();
    formData.append("request_id", requestId);
    
    fetch("reject_request.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            alert("✓ Request rejected");
            loadRequests(); // Refresh the list
        } else {
            alert("✗ Error: " + data.message);
        }
    })
    .catch(error => {
        alert("Error: Could not reject request");
        console.error("Error:", error);
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