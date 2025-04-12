<?php
// Initialize message and flag to reset the form
$message = '';
$resetForm = false;

// Check if the form was submitted using POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Connect to the MySQL database
  $conn = new mysqli("localhost", "root", "", "restaurant");

  // Check for connection error
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  // Get form input values
  $name   = $_POST['username'];
  $tables = (int) $_POST['total_no']; // Convert table count to integer
  $date   = $_POST['date'];
  $time   = $_POST['time'];
  $phone  = $_POST['phone_no'];
  $email  = $_POST['email'];

  // Maximum number of tables available per time slot
  $maxTables = 10;

  // Check how many tables are already booked at the selected date and time
  $stmt = $conn->prepare("SELECT SUM(tables) AS booked FROM reservations WHERE date = ? AND time = ?");
  $stmt->bind_param("ss", $date, $time); // Bind parameters to query
  $stmt->execute();
  $result = $stmt->get_result();
  $booked = $result->fetch_assoc()['booked'] ?? 0; // Get total booked tables or 0 if none

  // Check if requested tables exceed availability
  if ($booked + $tables > $maxTables) {
    $available = $maxTables - $booked;
    $message = "Sorry, only $available tables available at that time!";
  } else {
    // Proceed to insert reservation into database
    $stmt = $conn->prepare("INSERT INTO reservations (username, tables, date, time, phone_no, email) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sissss", $name, $tables, $date, $time, $phone, $email);

    // Check if reservation was successful
    if ($stmt->execute()) {
      $message = "Reservation Successful!";
      $resetForm = true; // Set flag to reset form after success
    } else {
      $message = "Error while booking: " . $conn->error;
    }
  }

  // Close the database connection
  $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Flavour Fusion - Book a Table</title>
  <link rel="shortcut icon" href="./assets/images/logo.jpg" type="image/x-icon">
  <link rel="stylesheet" href="./assets/css/reservation.css" />
</head>
<body>
  <!-- Back Icon to Home Page -->
<a href="index.html" class="back-icon" title="Back to Home">
  &#8592;
</a>
  <div class="form-bg">
    <!-- Reservation Form Starts Here -->
    <form method="POST" id="reservationForm">
      <div class="form-container">
        <div class="form-logo">
          <img src="./assets/images/logo.jpg" alt="logo" />
        </div>

        <h2>BOOK YOUR TABLE NOW!</h2>

        <!-- Input field for name -->
        <div class="input-item">
          <input type="text" name="username" placeholder="Enter Name" required />
        </div>

        <!-- Input for number of tables -->
        <div class="input-item">
          <input type="number" name="total_no" placeholder="Enter No. of Tables Want To Book" required min="1" />
        </div>

        <!-- Input for reservation date -->
        <div class="input-item">
          <input type="date" name="date" id="date" required onclick="this.showPicker()" />
        </div>

        <!-- Dropdown for selecting reservation time -->
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
          </select>
        </div>

        <!-- Input for phone number -->
        <div class="input-item">
          <input type="tel" name="phone_no" placeholder="Enter Phone No." required pattern="[0-9]{10}" />
        </div>

        <!-- Input for email address -->
        <div class="input-item">
          <input type="email" name="email" placeholder="Enter Email ID" required />
        </div>

        <!-- Submit button -->
        <div class="btn-box">
          <button type="submit" class="btn-reserve">Confirm Reservation</button>
        </div>
        <div class="btn-box">
          <a href="cancel.php" class="btn-reserve" style="text-align: center; display: block; text-decoration: none;">Cancel Reservation</a>
        </div>
      </div>
    </form>
    <!-- Toast message for feedback -->
    <div id="toast" class="toast"><?= $message ?></div>
  </div>

  <script>
    // Set minimum selectable date to today
    const today = new Date().toISOString().split("T")[0];
    document.getElementById("date").setAttribute("min", today);

    // Display toast message if there's a PHP message set
    <?php if (!empty($message)) : ?>
      const toast = document.getElementById("toast");
      toast.classList.add("show"); // Show toast

      // Set minimum date for cancellation date picker too
document.getElementById("cancel_date").setAttribute("min", today);

      // Hide toast after 3 seconds
      setTimeout(() => {
        toast.classList.remove("show");
      }, 3000);

      // If reservation is successful, reset the form
      <?php if ($resetForm): ?>
        document.getElementById("reservationForm").reset();
      <?php endif; ?>
    <?php endif; ?>
  </script>
</body>
</html>
