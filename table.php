<?php
// Initialize message variables
$message = '';
$resetForm = false;

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // DB connection setup
  $host = "localhost";
  $user = "root";
  $password = "";
  $dbname = "restaurant";

  // Connect to the database
  $conn = new mysqli($host, $user, $password, $dbname);
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  // Collect and sanitize POST data
  $name   = $_POST['username'];
  $tables = (int) $_POST['total_no'];
  $date   = $_POST['date'];
  $time   = $_POST['time'];
  $phone  = $_POST['phone_no'];
  $email  = $_POST['email'];

  $maxTables = 10; // Max tables available per time slot

  // Check if enough tables are available for the selected date and time
  $sql = "SELECT SUM(tables) AS booked FROM reservations WHERE date = ? AND time = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ss", $date, $time);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();
  $booked = $row['booked'] ?? 0;

  if ($booked + $tables > $maxTables) {
    // Not enough tables available
    $available = $maxTables - $booked;
    $message = "Sorry, only $available tables available at that time!";
  } else {
    // Insert reservation if tables are available
    $sql = "INSERT INTO reservations (username, tables, date, time, phone_no, email) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sissss", $name, $tables, $date, $time, $phone, $email);
    if ($stmt->execute()) {
      $message = "Reservation Successful!";
      $resetForm = true; // Reset form on success
    } else {
      $message = "Error while booking: " . $conn->error;
    }
  }

  // Close DB connection
  $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Book a Table</title>
  <link rel="stylesheet" href="./assets/css/reservation.css" />
</head>
<body>
  <div class="form-bg">
    <!-- Reservation Form -->
    <form method="POST" id="reservationForm">
      <div class="form-container">
        <!-- Logo -->
        <div class="form-logo">
          <img src="./assets/images/logo.jpg" alt="logo" />
        </div>

        <!-- Form Title -->
        <h2>BOOK YOUR TABLE NOW!</h2>

        <!-- Name Input -->
        <div class="input-item">
          <input type="text" name="username" placeholder="Enter Name" required />
        </div>

        <!-- Number of Tables Input -->
        <div class="input-item">
          <input type="number" name="total_no" placeholder="Enter No. of Tables Want To Book" required min="1" />
        </div>

        <!-- Date Input -->
        <div class="input-item">
          <input type="date" name="date" id="date" required onclick="this.showPicker()" />
        </div>

        <!-- Time Selection Dropdown -->
        <div class="input-item">
          <select id="time" name="time" required>
            <option value="" disabled selected>Select Time</option>
            <option value="10:00 AM">10:00 AM</option>
            <option value="10:30 AM">10:30 AM</option>
            <option value="11:00 AM">11:00 AM</option>
            <option value="11:30 AM">11:30 AM</option>
            <option value="12:00 PM">12:00 PM</option>
            <option value="12:30 PM">12:30 PM</option>
            <option value="1:00 PM">1:00 PM</option>
            <option value="1:30 PM">1:30 PM</option>
            <option value="2:00 PM">2:00 PM</option>
            <!-- More time slots can be added -->
          </select>
        </div>

        <!-- Phone Number Input -->
        <div class="input-item">
          <input type="tel" name="phone_no" placeholder="Enter Phone No." required pattern="[0-9]{10}" />
        </div>

        <!-- Email Input -->
        <div class="input-item">
          <input type="email" name="email" placeholder="Enter Email ID" required />
        </div>

        <!-- Submit Button -->
        <div class="btn-box">
          <button type="submit" class="btn-reserve">Confirm Reservation</button>
        </div>
      </div>
    </form>
  </div>

  <!-- Flatpickr Date Picker Script -->
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script>
    // Restrict date picker to today and future dates
    const today = new Date().toISOString().split("T")[0];
    document.getElementById("date").setAttribute("min", today);

    // Show alert if message exists (PHP-generated)
    <?php if (!empty($message)) : ?>
      alert("<?= $message ?>");
      <?php if ($resetForm): ?>
        // Reset form if booking was successful
        document.getElementById("reservationForm").reset();
      <?php endif; ?>
    <?php endif; ?>
  </script>
</body>
</html>
