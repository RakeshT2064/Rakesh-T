<footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>My Cinema</h5>
                    <p>Your one-stop destination for movie tickets</p>
                </div>
                <div class="col-md-3">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="/mycinema/index.php" class="text-light">Home</a></li>
                        <li><a href="/mycinema/movies.php" class="text-light">Movies</a></li>
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <li><a href="/mycinema/my_bookings.php" class="text-light">My Bookings</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Contact</h5>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-envelope"></i> contact@mycinema.com</li>
                        <li><i class="bi bi-phone"></i> +91 9876543210</li>
                    </ul>
                </div>
            </div>
            <hr class="mt-4">
            <div class="text-center">
                <p>&copy; <?php echo date('Y'); ?> My Cinema. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>