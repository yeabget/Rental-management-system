<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'renter') {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../config/Database.php";

$db = (new Database())->connect();
$user = $_SESSION['user'];

$firstLetter = strtoupper(substr($user['fullname'], 0, 1));
$currentPage = basename($_SERVER['PHP_SELF']);
$unreadChats = 0;

try {
   
    $stmt = $db->prepare("
        SELECT COUNT(*)
        FROM messages
        WHERE receiver_id = ?
        AND is_read = 0
    ");
    $stmt->execute([$user['id']]);
    $unreadChats = $stmt->fetchColumn();
} catch (PDOException $e) {
    $unreadChats = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RentFlow Platform</title>
    <link rel="stylesheet" href="../assets/css/renters_sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<header class="ecommerce-header">
    <div class="header-container">
        
        <a href="dashboard.php" class="brand-logo" title="RentFlow Home">
            <i class="fa-solid fa-bag-shopping"></i> Rent<span>Flow</span>
        </a>

        <button class="mobile-nav-toggle" id="mobileNavToggle" aria-label="Toggle Navigation">
            <i class="fas fa-bars"></i>
        </button>

        <nav class="main-nav" id="mainNav">
            <a href="../index.php" class="<?= ($currentPage == 'index.php') ? 'active' : '' ?>">
                <i class="fas fa-home"></i> Home
            </a>
            <a href="dashboard.php" class="<?= ($currentPage == 'dashboard.php') ? 'active' : '' ?>">
                <i class="fas fa-compass"></i> Explore Items
            </a>
            <a href="saved.php" class="<?= ($currentPage == 'saved.php') ? 'active' : '' ?>">
                <i class="fas fa-heart"></i> Saved Items
            </a>
        </nav>

        <div class="header-actions">
            
            <a href="chat_list.php" class="action-icon-btn <?= ($currentPage == 'chat_list.php') ? 'active' : '' ?>" title="Chat Inbox">
                <i class="fas fa-paper-plane"></i>
                <?php if ($unreadChats > 0): ?>
                    <span class="badge-count"><?= $unreadChats ?></span>
                <?php endif; ?>
            </a>

            <div class="user-profile-menu">
                <div class="header-avatar" id="profileDropdownBtn" title="Profile Options">
                    <?= $firstLetter ?>
                </div>
                
                <div class="profile-dropdown" id="profileDropdownMenu">
                    <div class="dropdown-header">
                        <h4><?= htmlspecialchars($user['fullname']) ?></h4>
                        <p>Verified Customer</p>
                    </div>
                    <div class="dropdown-divider"></div>
                    
                    <a href="../auth/logout.php" class="logout-item">
                        <i class="fas fa-arrow-right-from-bracket"></i> Logout
                    </a>
                </div>
            </div>

        </div>
    </div>
</header>

<script>
    const dropdownBtn = document.getElementById('profileDropdownBtn');
    const dropdownMenu = document.getElementById('profileDropdownMenu');
    const mobileToggle = document.getElementById('mobileNavToggle');
    const mainNav = document.getElementById('mainNav');

    if (dropdownBtn && dropdownMenu) {
        dropdownBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
        });

        document.addEventListener('click', () => {
            if (dropdownMenu.classList.contains('show')) {
                dropdownMenu.classList.remove('show');
            }
        });
    }

    if (mobileToggle && mainNav) {
        mobileToggle.addEventListener('click', () => {
            mainNav.classList.toggle('open');
            const icon = mobileToggle.querySelector('i');
            if (mainNav.classList.contains('open')) {
                icon.className = 'fas fa-times';
            } else {
                icon.className = 'fas fa-bars';
            }
        });
    }
</script>

</body>
</html>