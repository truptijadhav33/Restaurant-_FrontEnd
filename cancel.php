<?php
$message = '';
$resetForm = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancel'])) {
  $conn = new mysqli("localhost", "root", "", "restaurant");

  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $email = $_POST['cancel_email'];
  $date  = $_POST['cancel_date'];
  $time  = $_POST['cancel_time'];

  $stmt = $conn->prepare("DELETE FROM reservations WHERE email = ? AND date = ? AND time = ?");
  $stmt->bind_param("sss", $email, $date, $time);
  $stmt->execute();

  if ($stmt->affected_rows > 0) {
    $message = "Reservation Cancelled Successfully!";
    $resetForm = true;
  } else {
    $message = "No matching reservation found!";
  }

  $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Flavour Fusion - Cancel Reservation</title>
  <link rel="shortcut icon" href="./assets/images/logo.jpg" type="image/x-icon">
  <link rel="stylesheet" href="./assets/css/reservation.css" />
</head>
<body>
    <!-- Back Icon to Home Page -->
<a href="index.html" class="back-icon" title="Back to Home">
  &#8592;
</a>
  <div class="form-bg">
    <form method="POST" id="cancelForm">
      <div class="form-container">
        <div class="form-logo">
          <img src="./assets/images/logo.jpg" alt="logo" />
        </div>

        <h2>CANCEL YOUR RESERVATION</h2>

        <!-- Email -->
        <div class="input-item">
          <input type="email" name="cancel_email" placeholder="Enter Email ID used for Booking" required />
        </div>

        <!-- Date -->
        <div class="input-item">
          <input type="date" name="cancel_date" id="cancel_date" required onclick="this.showPicker()" />
        </div>

        <!-- Time -->
        <div class="input-item">
          <select name="cancel_time" required>
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

        <!-- Submit -->
        <div class="btn-box">
          <button type="submit" class="btn-reserve" name="cancel">Cancel Reservation</button>
        </div>

        <!-- Back to booking -->
        <div class="btn-box">
          <a href="table.php" class="btn-reserve" style="text-align: center; display: block; text-decoration: none;">Back to Booking</a>
        </div>
      </div>
    </form>

    <!-- Toast message -->
    <div id="toast" class="toast"><?= $message ?></div>
  </div>

  <script>
    // Set minimum date to today
    const today = new Date().toISOString().split("T")[0];
    document.getElementById("cancel_date").setAttribute("min", today);

    // Show toast message
    <?php if (!empty($message)) : ?>
      const toast = document.getElementById("toast");
      toast.classList.add("show");

      setTimeout(() => {
        toast.classList.remove("show");
      }, 3000);

      <?php if ($resetForm): ?>
        document.getElementById("cancelForm").reset();
      <?php endif; ?>
    <?php endif; ?>
  </script>
</body>
</html>
