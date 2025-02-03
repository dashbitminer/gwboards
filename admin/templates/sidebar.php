<div class="sidebar">
    <div class="text-white text-center mb-4">
        <h5>Panel Administrativo</h5>
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'usuarios.php' ? 'active' : ''; ?>" href="usuarios.php">
                <i class="bi bi-people"></i> Gestión de Usuarios
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo in_array(basename($_SERVER['PHP_SELF']), ['boards.php', 'board_form.php', 'board_permisos.php']) ? 'active' : ''; ?>" href="boards.php">
                <i class="bi bi-layout-text-window"></i> Boards
            </a>
        </li>
    </ul>
</div> 