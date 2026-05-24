<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <link rel="stylesheet" href="../assets/css/login.css">
    <link rel="stylesheet" href="../assets/css/nav.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<?php include "../includes/navbar.php"; ?>

<div class="form-container">

    <div class="form-box">
 <a href="/rental-management-system/index.php" class="back-link">
            <i class="fa fa-arrow-left"></i> Back
        </a>
        <h2>Welcome Back</h2>
        <p class="form-subtitle">Login to continue</p>

        <?php if(isset($_GET['error'])): ?>
            <div class="error-box">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <form action="/rental-management-system/auth/login_process.php" method="POST">

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit">Login</button>

        </form>

        <div class="form-footer">
            Don't have an account?
            <a href="/rental-management-system/auth/register.php">Register</a>
        </div>

    </div>

</div>
<?php include "../includes/footer.php"; ?>
</body>
</html>