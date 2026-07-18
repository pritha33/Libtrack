document.querySelector("form").addEventListener("submit", function(e){
    e.preventDefault();

    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();

    if(!email || !password){
        alert("Fill all fields");
        return;
    }

    fetch("login.php", {
        method: "POST",
        headers: {"Content-Type":"application/x-www-form-urlencoded"},
        body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === "success"){
            if(data.role === "admin"){
                window.location.href = "dashboard.html";
            } else {
                window.location.href = "books.html";
            }
        } else {
            alert(data.message);
        }
    });
});