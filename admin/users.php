<?php
session_start();

require "../config/Database.php";

if(
    !isset($_SESSION['user']) ||
    $_SESSION['user']['role'] !== 'admin'
){
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->connect();

$stmt = $db->query("
    SELECT * FROM users
    ORDER BY id DESC
");

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Manage Users</title>

<link rel="stylesheet" href="../assets/css/manage_users.css">

</head>

<body>

<div class="dashboard">

<?php include "includes/sidebar.php"; ?>

<div class="main">
<div class="page-top">
    <div class="welcome-box">
        <h1>Manage Users</h1>
        <p>View, suspend, or delete user accounts</p>
    </div>
</div>
    <table>

        <tr>
            <th>ID</th>
            <th>Fullname</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php foreach($users as $u): ?>

        <tr>

            <td><?= $u['id']; ?></td>

            <td><?= htmlspecialchars($u['fullname']); ?></td>

            <td><?= htmlspecialchars($u['email']); ?></td>

            <td><?= htmlspecialchars($u['role']); ?></td>

            <td>
                <span class="<?= $u['status'] === 'active' ? 'status-active' : 'status-suspended'; ?>">
                    <?= ucfirst($u['status']); ?>
                </span>
            </td>

            <td>

                <?php if($u['status'] === 'active'): ?>
                    <a href="suspend_user.php?id=<?= $u['id']; ?>">
                        Suspend
                    </a>
                <?php else: ?>
                    <a href="activate_user.php?id=<?= $u['id']; ?>">
                        Activate
                    </a>
                <?php endif; ?>

                |

                <a
                    href="delete_user.php?id=<?= $u['id']; ?>"
                    class="delete-btn"
                    onclick="return confirm('Delete this user?')"
                >
                    Delete
                </a>

            </td>

        </tr>

        <?php endforeach; ?>

    </table>

</div>

</div>

</body>
</html>