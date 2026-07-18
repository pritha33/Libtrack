// forgot_password.js - Handle password reset
let userEmail = '';

// Step 1: Send reset code
document.getElementById('forgotForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const email = document.getElementById('email').value.trim();
    
    if (!email) {
        showAlert('Please enter your email address', 'error');
        return;
    }
    
    if (!email.includes('@')) {
        showAlert('Please enter a valid email address', 'error');
        return;
    }
    
    // Show loading state
    const submitBtn = document.getElementById('sendOtpBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Sending...';
    submitBtn.disabled = true;
    
    try {
        const response = await fetch('send_reset_code.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email: email })
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            userEmail = email;
            document.getElementById('resetEmail').value = email;
            showAlert(data.message, 'success');
            
            // Switch to step 2 after 2 seconds
            setTimeout(() => {
                document.getElementById('step1Form').style.display = 'none';
                document.getElementById('step2Form').style.display = 'block';
                document.getElementById('resetToken').focus();
            }, 2000);
        } else {
            showAlert(data.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Something went wrong. Please try again.', 'error');
    } finally {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
});

// Step 2: Reset password with code
document.getElementById('resetForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const token = document.getElementById('resetToken').value.trim();
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    if (!token) {
        showAlert('Please enter the reset code', 'error');
        return;
    }
    
    if (newPassword.length < 6) {
        showAlert('Password must be at least 6 characters', 'error');
        return;
    }
    
    if (newPassword !== confirmPassword) {
        showAlert('Passwords do not match', 'error');
        return;
    }
    
    // Show loading state
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Resetting...';
    submitBtn.disabled = true;
    
    try {
        const response = await fetch('reset_password.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                email: userEmail,
                token: token,
                new_password: newPassword
            })
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            showAlert(data.message, 'success');
            
            // Redirect to login after 3 seconds
            setTimeout(() => {
                window.location.href = 'login.html';
            }, 3000);
        } else {
            showAlert(data.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Something went wrong. Please try again.', 'error');
    } finally {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
});

// Show alert message
function showAlert(message, type) {
    const alertDiv = document.getElementById('alertMessage');
    alertDiv.className = `alert-message ${type}-alert show`;
    alertDiv.innerHTML = `
        <i class="bi ${type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'} me-2"></i>
        ${message}
    `;
    
    // Auto hide after 5 seconds
    setTimeout(() => {
        alertDiv.classList.remove('show');
    }, 5000);
}

// Password strength indicator
document.getElementById('newPassword')?.addEventListener('input', function(e) {
    const password = e.target.value;
    let strength = 0;
    
    if (password.length >= 6) strength++;
    if (password.match(/[a-z]+/)) strength++;
    if (password.match(/[A-Z]+/)) strength++;
    if (password.match(/[0-9]+/)) strength++;
    
    // Remove existing strength indicator
    const existingIndicator = document.querySelector('.password-strength');
    if (existingIndicator) existingIndicator.remove();
    
    // Add strength indicator
    if (password.length > 0) {
        const strengthDiv = document.createElement('div');
        strengthDiv.className = 'password-strength mt-1 small';
        
        if (strength < 2) {
            strengthDiv.innerHTML = '<i class="bi bi-shield"></i> Weak password';
            strengthDiv.style.color = '#dc3545';
        } else if (strength < 4) {
            strengthDiv.innerHTML = '<i class="bi bi-shield"></i> Medium password';
            strengthDiv.style.color = '#ffc107';
        } else {
            strengthDiv.innerHTML = '<i class="bi bi-shield-check"></i> Strong password';
            strengthDiv.style.color = '#28a745';
        }
        
        e.target.parentElement.appendChild(strengthDiv);
    }
});