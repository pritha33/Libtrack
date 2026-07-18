// admin_users.js - Manage users with proper delete button
document.addEventListener("DOMContentLoaded", function() {
    loadUsers();
    
    // Add user form submit
    document.getElementById("addUserForm").addEventListener("submit", function(e) {
        e.preventDefault();
        addUser();
    });
    
    // Check authentication
    fetch("check_auth.php")
        .then(res => res.json())
        .then(user => {
            if (user.role !== "admin") {
                window.location.href = "login.html";
            }
        });
});

function loadUsers() {
    fetch("get_users.php")
        .then(res => res.json())
        .then(users => {
            const tbody = document.getElementById("userTable");
            tbody.innerHTML = "";
            
            if (users.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <i class="bi bi-people" style="font-size: 48px; color: #1ABC9C;"></i>
                            <h5 class="mt-2">No Users Found</h5>
                            <p class="text-muted">Click "Add User" to create new users</p>
                        </td>
                    </tr>
                `;
                return;
            }
            
            users.forEach((user, index) => {
                const roleBadge = user.role === 'admin' 
                    ? '<span class="badge-admin">Admin</span>'
                    : '<span class="badge-student">Student</span>';
                
                tbody.innerHTML += `
                    <tr>
                        <td>${index + 1}</td>
                        <td><strong>${escapeHtml(user.fullname)}</strong></td>
                        <td>${escapeHtml(user.email)}</td>
                        <td>${roleBadge}</td>
                        <td>
                            <button class="btn-delete" onclick="deleteUser(${user.id})">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </td>
                    </tr>
                `;
            });
        })
        .catch(error => {
            console.error("Error:", error);
            document.getElementById("userTable").innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-danger py-4">
                        <i class="bi bi-exclamation-triangle"></i> Error loading users
                    </td>
                </tr>
            `;
        });
}

function addUser() {
    const fullname = document.getElementById("fullname").value.trim();
    const email = document.getElementById("email").value.trim();
    const role = document.getElementById("role").value;
    
    if (!fullname || !email) {
        alert("Please fill all fields");
        return;
    }
    
    fetch("add_user.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `fullname=${encodeURIComponent(fullname)}&email=${encodeURIComponent(email)}&role=${encodeURIComponent(role)}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            alert("User added successfully. Default password: student123");
            document.getElementById("fullname").value = "";
            document.getElementById("email").value = "";
            bootstrap.Modal.getInstance(document.getElementById("addUserModal")).hide();
            loadUsers();
        } else {
            alert(data.message || "Failed to add user");
        }
    })
    .catch(error => {
        alert("Error adding user");
    });
}

function deleteUser(id) {
    if (confirm("Delete this user? This action cannot be undone.")) {
        fetch(`delete_user.php?id=${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === "success") {
                    alert("User deleted successfully");
                    loadUsers();
                } else {
                    alert(data.message || "Failed to delete user");
                }
            })
            .catch(error => {
                alert("Error deleting user");
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