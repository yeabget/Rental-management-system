<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<?php include "../includes/navbar.php"; ?>

<!-- REGISTER CONTAINER -->
<div class="form-container">

    <div class="form-box fade-in">

        <h2>Create Account</h2>

        <p class="form-subtitle">
            Join RentFlow and start renting easily
        </p>

        <!-- ERROR MESSAGE -->
        <?php if(isset($_GET['error'])): ?>
            <div class="error-box">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <!-- SUCCESS MESSAGE -->
        <?php if(isset($_GET['success'])): ?>
            <div class="success-box">
                <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php endif; ?>

        <!-- FORM -->
        <form action="register_process.php" method="POST">

            <!-- FULL NAME -->
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="fullname" placeholder="Enter your full name" required>
            </div>

            <!-- EMAIL -->
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>

            <!-- PHONE -->
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" placeholder="Enter your phone number" required>
            </div>

            <!-- PASSWORD -->
            <div class="form-group">
                <label>Password</label>

                <div class="password-wrapper">

                    <input
                        type="password"
                        name="password"
                        id="password"
                        placeholder="Minimum 6 characters"
                        required
                    >

                    <span class="toggle-password" onclick="togglePassword()">
                        <i class="fa-solid fa-eye"></i>
                    </span>

                </div>
            </div>

            <!-- ROLE -->
            <div class="form-group">
                <label>Role</label>

                <select name="role" required>
                    <option value="">Select Role</option>
                    <option value="renter">Renter</option>
                    <option value="owner">Owner</option>
                </select>
            </div>

            <!-- BUTTON -->
            <button type="submit">
                Create Account
            </button>

        </form>

        <!-- FOOTER -->
        <div class="form-footer">
            Already have an account?
            <a href="login.php">Login</a>
        </div>

    </div>

</div>

<!-- JS -->
<script>
function togglePassword(){
    const input = document.getElementById("password");
    const icon = event.currentTarget.querySelector("i");

    if(input.type === "password"){
        input.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}
</script>

</body>
</html>