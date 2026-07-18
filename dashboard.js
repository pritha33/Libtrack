fetch("check_auth.php")
    .then(res => res.json())
    .then(user => {
        if (user.role !== "admin") {
            window.location.href = "login.html";
        }
    });

function loadStats() {
    fetch("admin_stats.php")
        .then(res => res.json())
        .then(data => {
            document.getElementById("booksCount").innerText = data.books || 0;
            document.getElementById("usersCount").innerText = data.users || 0;
            document.getElementById("issuedCount").innerText = data.issued || 0;
            document.getElementById("finesCount").innerText = data.fines || 0;
        });
}

loadStats();