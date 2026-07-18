document.querySelector("form").addEventListener("submit", function(e){
    e.preventDefault();

    const fullname = document.getElementById("fullname").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();
    const confirmPassword = document.getElementById("confirmPassword").value.trim();
    const role = document.getElementById("role").value;

    if(!fullname || !email || !password){
        alert("All fields are required");
        return;
    }

    if(!email.endsWith("@gmail.com")){
        alert("Email must be @gmail.com");
        return;
    }

    if(password.length < 8){
        alert("Password must be at least 8 characters");
        return;
    }

    if(password !== confirmPassword){
        alert("Passwords do not match");
        return;
    }

    fetch("register.php", {
        method: "POST",
        headers: {"Content-Type":"application/x-www-form-urlencoded"},
        body: `fullname=${encodeURIComponent(fullname)}&email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}&role=${encodeURIComponent(role)}`
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === "success"){
            alert("Registration successful!");
            window.location.href = "login.html";
        } else {
            alert(data.message);
        }
    });
});