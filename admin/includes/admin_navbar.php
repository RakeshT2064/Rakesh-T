<!-- Add this to your admin navigation menu -->
<li class="nav-item">
    <a class="nav-link" href="messages.php">
        Contact Messages
        <?php
        // Display unread message count
        $stmt = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'unread'");
        $unread_count = $stmt->fetchColumn();
        if ($unread_count > 0): ?>
            <span class="badge bg-danger"><?php echo $unread_count; ?></span>
        <?php endif; ?>
    </a>
</li>