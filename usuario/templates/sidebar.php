<div class="sidebar">
    <div class="text-white text-center mb-4">
        <h5>Panel de Usuario</h5>
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo in_array(basename($_SERVER['PHP_SELF']), ['mis_boards.php', 'ver_board.php']) ? 'active' : ''; ?>" href="mis_boards.php">
                <i class="bi bi-layout-text-window"></i> Mis Boards
            </a>
        </li>
    </ul>
</div> 