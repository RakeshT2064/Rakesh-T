<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['showtime_id'])) {
    header("Location: index.php");
    exit();
}

$showtime_id = $_GET['showtime_id'];
// Update the SQL query to include movie poster
$stmt = $pdo->prepare("
    SELECT s.*, m.title, m.poster_url, t.name as theater_name, t.seats_capacity 
    FROM showtimes s 
    JOIN movies m ON s.movie_id = m.movie_id 
    JOIN theaters t ON s.theater_id = t.theater_id 
    WHERE s.showtime_id = ?
");
$stmt->execute([$showtime_id]);
$showtime = $stmt->fetch();

// Get booked seats for this showtime
$stmt = $pdo->prepare("
    SELECT seat_numbers 
    FROM bookings 
    WHERE showtime_id = ?
");
$stmt->execute([$showtime_id]);
$bookings = $stmt->fetchAll(PDO::FETCH_COLUMN);

$booked_seats = [];
foreach ($bookings as $booking) {
    $booked_seats = array_merge($booked_seats, explode(',', $booking));
}

if (isset($_POST['book'])) {
    $selected_seats = explode(',', $_POST['seats']);
    $total_amount = count($selected_seats) * $showtime['price'];
    
    $_SESSION['booking_data'] = [
        'showtime_id' => $showtime_id,
        'seats' => $selected_seats,
        'total_amount' => $total_amount,
        'movie_title' => $showtime['title'],
        'theater_name' => $showtime['theater_name'],
        'show_date' => $showtime['show_date'],
        'show_time' => $showtime['show_time']
    ];
    
    header("Location: confirm_booking.php");
    exit();
}
require_once 'includes/headers.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Seats - Movie Booking System</title>
    <link rel="icon" type="image/logo.png" href="images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .seat-layout {
            display: grid;
            grid-template-columns: repeat(14, 1fr);
            gap: 12px;
            margin: 30px 0;
            justify-content: center;
        }

        .seat {
            padding: 12px 0;
            text-align: center;
            font-weight: 500;
            background-color:rgba(15, 193, 54, 0.9); /* Changed to green for available seats */
            border: 2px solidrgb(0, 0, 0);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            user-select: none;
            color: white;
        }

        .seat.booked {
            background-color:rgb(244, 0, 0); /* Changed to grey for booked seats */
            broder-color:rgb(0, 0, 0);
            color: white;
            cursor: not-allowed;
            opacity: 0.8;
        }

        .seat.selected {
            background-color:rgb(253, 0, 0); /* Blue for selected seats */
            border-color:rgb(0, 0, 0);
            color: white;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0, 123, 255, 0.4);
        }

        .seat-legend {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 20px 0;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .legend-box {
            width: 20px;
            height: 20px;
            border-radius: 4px;
        }

        .highlight-text {
        background-color: rgba(243, 243, 243, 0.48);
        color:rgb(0, 0, 0);
        padding: 5px 15px;
        border-radius: 5px;
        font-weight: bold;
        font-size: 1.1em;
        display: inline-block;
        min-width: 80px;
        text-align: center;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .seat.selected {
        background-color: rgb(87, 87, 87);
        border-color: rgb(0, 0, 0);
        color: white;
        font-weight: bold;
        box-shadow: 0 2px 4px rgba(0, 123, 255, 0.4);
        transform: scale(1.1);
    }

        .screen {
    background-color: rgba(59, 59, 59, 0.33);
    padding: 16px;
    text-align: center;
    margin-bottom: 25px;
    border-radius: 6px;
    font-weight: bold;
    font-size: 16px;
    letter-spacing: 1px;
    color:rgb(0, 0, 0);
}

    </style>

    <!-- Add this after the screen div -->
    <div class="seat-legend">
        <div class="legend-item">
            <div class="legend-box" style="background-color:rgb(29, 187, 50);"></div>
            <span>Available</span>
        </div>
        <div class="legend-item">
            <div class="legend-box" style="background-color:rgb(128, 132, 128);"></div>
            <span>Selected</span>
        </div>
        <div class="legend-item">
            <div class="legend-box" style="background-color:rgb(253, 0, 0);"></div>
            <span>Booked</span>
        </div>
    </div>

</head>
<body>
    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-md-3">
                <?php if($showtime['poster_url']): ?>
                    <img src="<?php echo htmlspecialchars($showtime['poster_url']); ?>" 
                         alt="<?php echo htmlspecialchars($showtime['title']); ?>"
                         class="img-fluid rounded shadow"
                         style="width: 100%; max-height: 400px; object-fit: cover;">
                <?php else: ?>
                    <div class="bg-secondary text-white rounded" style="width: 100%; height: 400px; display: flex; align-items: center; justify-content: center;">
                        <span>No Poster Available</span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-9">
                <h2><?php echo htmlspecialchars($showtime['title']); ?></h2>
                <p>
                <i class="bi bi-building"></i>
                   <strong>Theater:</strong> <?php echo htmlspecialchars($showtime['theater_name']); ?><br><br>
                <i class="bi bi-calendar"></i>
                   <strong>Date:</strong> <?php echo date('F d, Y', strtotime($showtime['show_date'])); ?><br><br>
                <i class="bi bi-clock"></i>
                   <strong>Time:</strong> <?php echo date('h:i A', strtotime($showtime['show_time'])); ?><br><br>
                <i class="bi bi-chair"></i>
                   <strong>Price per ticket:</strong> ₹<?php echo number_format($showtime['price'], 2); ?><br><br>
                <i class="bi bi-ticket-perforated"></i>
                   <strong>Available Tickets:</strong> <?php echo $showtime['seats_capacity'] - count($booked_seats); ?> out of <?php echo $showtime['seats_capacity']; ?><br><br>
                    <strong class="text-danger">*Maximum 6 tickets per booking allowed</strong>
                </p>
            </div>
        </div>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="screen">
            SCREEN
        </div>
<center><h5> All Eyes Here </h5></center>
        <form method="POST" id="bookingForm">
            <div class="seat-layout">
                <?php for($i = 1; $i <= $showtime['seats_capacity']; $i++): ?>
                    <div class="seat <?php echo in_array($i, $booked_seats) ? 'booked' : ''; ?>" 
                         data-seat="<?php echo $i; ?>"
                         onclick="selectSeat(this)">
                        <?php echo $i; ?>
                    </div>
                <?php endfor; ?>
            </div>

            <div class="mb-3">
                <p><strong>Selected Seats:</strong> <span id="selectedSeatsText" class="highlight-text">None</span></p>
                <p><strong>Total Amount:</strong> ₹<span id="totalAmount" class="highlight-text">0.00</span></p>
            </div>
            <input type="hidden" name="seats" id="selectedSeats" value="">
            <button type="submit" name="book" class="btn btn-primary" id="bookButton" disabled>Confirm Booking</button>
            <a href="movie_details.php?id=<?php echo $showtime['movie_id']; ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <script>
        let selectedSeats = [];
        const pricePerSeat = <?php echo $showtime['price']; ?>;
        const MAX_TICKETS = 6;

        function selectSeat(seatElement) {
            if (seatElement.classList.contains('booked')) return;

            const seatNumber = seatElement.dataset.seat;
            const seatIndex = selectedSeats.indexOf(seatNumber);

            if (seatIndex === -1) {
                if (selectedSeats.length >= MAX_TICKETS) {
                    alert('Maximum 6 tickets allowed per booking!');
                    return;
                }
                selectedSeats.push(seatNumber);
                seatElement.classList.add('selected');
            } else {
                selectedSeats.splice(seatIndex, 1);
                seatElement.classList.remove('selected');
            }

            updateSelection();
        }

        function updateSelection() {
            const selectedSeatsText = document.getElementById('selectedSeatsText');
            const totalAmount = document.getElementById('totalAmount');
            const bookButton = document.getElementById('bookButton');
            const selectedSeatsInput = document.getElementById('selectedSeats');

            if (selectedSeats.length > 0) {
                selectedSeats.sort((a, b) => a - b);
                selectedSeatsText.textContent = selectedSeats.join(', ');
                totalAmount.textContent = (selectedSeats.length * pricePerSeat).toFixed(2);
                bookButton.disabled = false;
                selectedSeatsInput.value = selectedSeats.join(',');
            } else {
                selectedSeatsText.textContent = 'None';
                totalAmount.textContent = '0.00';
                bookButton.disabled = true;
                selectedSeatsInput.value = '';
            }
        }
    </script>
     <?php require_once 'includes/footer.php'; ?>
</body>
</html>

<!-- Add this CSS in the head section -->
<style>
    .seat-preview {
        position: fixed;
        top: 100px;
        right: 20px;
        width: 300px;
        background: white;
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    .preview-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 8px;
        margin-bottom: 15px;
    }
    .selected-seats-info {
        background: #f8f9fa;
        padding: 10px;
        border-radius: 5px;
    }
</style>

<!-- Add this after your seat selection grid -->
<div class="seat-preview" id="seatPreview">
    <img src="images/theater.png" alt="Theater Preview" class="preview-image">
    <div class="selected-seats-info">
        <h5>View of Theater</h5>
        <hr>
    </div>
</div>

<!-- Add this JavaScript before closing body tag -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const seatCheckboxes = document.querySelectorAll('.seat-checkbox');
    const selectedSeatsDisplay = document.getElementById('selectedSeatsDisplay');
    const totalAmountDisplay = document.getElementById('totalAmount');
    const pricePerSeat = <?php echo json_encode($showtime['price']); ?>;

    function updatePreview() {
        const selectedSeats = [];
        let totalAmount = 0;

        seatCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                selectedSeats.push(checkbox.value);
                totalAmount += parseFloat(pricePerSeat);
            }
        });

        if (selectedSeats.length > 0) {
            selectedSeatsDisplay.textContent = selectedSeats.join(', ');
            totalAmountDisplay.textContent = '₹' + totalAmount.toFixed(2);
        } else {
            selectedSeatsDisplay.textContent = 'No seats selected';
            totalAmountDisplay.textContent = '₹0.00';
        }
    }

    seatCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updatePreview);
    });
});
</script>
