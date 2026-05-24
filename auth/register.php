<?php
session_start();
require "../config/Database.php";

$db = (new Database())->connect();


$fullname = "";
$email = "";
$phone = "";
$role = "";

$errors = [];


if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $fullname = trim($_POST['fullname']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $password = trim($_POST['password']);
    $role     = trim($_POST['role']);


    if(empty($fullname)){

        $errors['fullname'] = "Full name is required";

    }else{

        $nameParts = array_filter(explode(" ", $fullname));

        if(count($nameParts) < 2){
            $errors['fullname'] = "Enter your full name (2 words minimum)";
        }
    }


    if(empty($email)){

        $errors['email'] = "Email is required";

    }elseif(!preg_match("/^[a-zA-Z0-9._%+-]+@gmail\.com$/", $email)){

        $errors['email'] = "Email must end with @gmail.com";
    }


    if(empty($phone)){

        $errors['phone'] = "Phone number is required";

    }elseif(!preg_match('/^[0-9]{10}$/', $phone)){

        $errors['phone'] = "Phone number must contain exactly 10 digits";
    }

    if(empty($password)){

        $errors['password'] = "Password is required";

    }elseif(strlen($password) < 6){

        $errors['password'] = "Password must be at least 6 characters";
    }


    if(empty($role)){

        $errors['role'] = "Please select a role";
    }


    if(empty($errors)){

        $stmt = $db->prepare("
            SELECT id 
            FROM users 
            WHERE email = ?
        ");

        $stmt->execute([$email]);

        if($stmt->rowCount() > 0){

            $errors['email'] = "This email already exists";
        }
    }


    if(empty($errors)){

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare("
            INSERT INTO users
            (fullname,email,phone,password,role)
            VALUES (?,?,?,?,?)
        ");

        $stmt->execute([
            $fullname,
            $email,
            $phone,
            $hashed,
            $role
        ]);

        $_SESSION['user'] = [
            "id"       => $db->lastInsertId(),
            "fullname" => $fullname,
            "email"    => $email,
            "role"     => $role
        ];

        header("Location: ../index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Register</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link rel="stylesheet"
href="../assets/css/nav.css">

<link rel="stylesheet"
href="../assets/css/signup_.css">

<link rel="stylesheet"
href="../assets/css/footer.css">

</head>

<body>

<?php include "../includes/navbar.php"; ?>

<div class="form-container">

    <div class="form-box fade-in">

        <a href="/rental-management-system/index.php"
        class="back-link">

            <i class="fa fa-arrow-left"></i>
            Back

        </a>

        <h2>Create Account</h2>

        <p class="form-subtitle">
            Join RentFlow and start renting easily
        </p>

        <form method="POST">


            <div class="form-group">

                <?php if(isset($errors['fullname'])): ?>

                    <label class="error-label">
                        <i class="fa fa-circle-exclamation"></i>
                        <?= $errors['fullname'] ?>
                    </label>

                <?php else: ?>

                    <label>Full Name</label>

                <?php endif; ?>

                <input
                    type="text"
                    name="fullname"
                    placeholder="Enter your full name"
                    value="<?= htmlspecialchars($fullname) ?>"
                    class="<?= isset($errors['fullname']) ? 'input-error' : '' ?>"
                >

            </div>

           
            <div class="form-group">

                <?php if(isset($errors['email'])): ?>

                    <label class="error-label">
                        <i class="fa fa-circle-exclamation"></i>
                        <?= $errors['email'] ?>
                    </label>

                <?php else: ?>

                    <label>Email</label>

                <?php endif; ?>

                <input
                    type="text"
                    name="email"
                    placeholder="example@gmail.com"
                    value="<?= htmlspecialchars($email) ?>"
                    class="<?= isset($errors['email']) ? 'input-error' : '' ?>"
                >

            </div>

    

            <div class="form-group">

                <?php if(isset($errors['phone'])): ?>

                    <label class="error-label">
                        <i class="fa fa-circle-exclamation"></i>
                        <?= $errors['phone'] ?>
                    </label>

                <?php else: ?>

                    <label>Phone</label>

                <?php endif; ?>

                <input
                    type="text"
                    name="phone"
                    placeholder="0912345678"
                    value="<?= htmlspecialchars($phone) ?>"
                    class="<?= isset($errors['phone']) ? 'input-error' : '' ?>"
                >

            </div>


            <div class="form-group">

                <?php if(isset($errors['password'])): ?>

                    <label class="error-label">
                        <i class="fa fa-circle-exclamation"></i>
                        <?= $errors['password'] ?>
                    </label>

                <?php else: ?>

                    <label>Password</label>

                <?php endif; ?>

                <div class="password-wrapper">

                    <input
                        type="password"
                        name="password"
                        id="password"
                        placeholder="Minimum 6 characters"
                        class="<?= isset($errors['password']) ? 'input-error' : '' ?>"
                    >

                    <span
                    class="toggle-password"
                    onclick="togglePassword(event)">

                        <i class="fa-solid fa-eye"></i>

                    </span>

                </div>

            </div>

        

            <div class="form-group">

                <?php if(isset($errors['role'])): ?>

                    <label class="error-label">
                        <i class="fa fa-circle-exclamation"></i>
                        <?= $errors['role'] ?>
                    </label>

                <?php else: ?>

                    <label>Role</label>

                <?php endif; ?>

                <select
                    name="role"
                    class="<?= isset($errors['role']) ? 'input-error' : '' ?>"
                >

                    <option value="">Select Role</option>

                    <option value="renter"
                    <?= $role == 'renter' ? 'selected' : '' ?>>
                        Renter
                    </option>

                    <option value="owner"
                    <?= $role == 'owner' ? 'selected' : '' ?>>
                        Owner
                    </option>

                </select>

            </div>

            <button type="submit">
                Create Account
            </button>

        </form>

        <div class="form-footer">

            Already have an account?

            <a href="login.php">
                Login
            </a>

        </div>

    </div>

</div>

<?php include "../includes/footer.php"; ?>

<script>

function togglePassword(event){

    const input =
    document.getElementById("password");

    const icon =
    event.currentTarget.querySelector("i");

    if(input.type === "password"){

        input.type = "text";

        icon.classList.remove("fa-eye");

        icon.classList.add("fa-eye-slash");

    }else{

        input.type = "password";

        icon.classList.remove("fa-eye-slash");

        icon.classList.add("fa-eye");
    }
}

</script>

</body>
</html>