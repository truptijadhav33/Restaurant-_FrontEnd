<?php
$message = '';
$resetForm = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $conn = new mysqli("localhost", "root", "", "restaurant");
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $name   = $_POST['username'];
  $tables = (int) $_POST['total_no'];
  $date   = $_POST['date'];
  $time   = $_POST['time'];
  $phone  = $_POST['phone_no'];
  $email  = $_POST['email'];

  $maxTables = 10;

  $stmt = $conn->prepare("SELECT SUM(tables) AS booked FROM reservations WHERE date = ? AND time = ?");
  $stmt->bind_param("ss", $date, $time);
  $stmt->execute();
  $result = $stmt->get_result();
  $booked = $result->fetch_assoc()['booked'] ?? 0;

  if ($booked + $tables > $maxTables) {
    $available = $maxTables - $booked;
    $message = "Sorry, only $available tables available at that time!";
  } else {
    $stmt = $conn->prepare("INSERT INTO reservations (username, tables, date, time, phone_no, email) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sissss", $name, $tables, $date, $time, $phone, $email);
    if ($stmt->execute()) {
      $message = "Reservation Successful!";
      $resetForm = true;
    } else {
      $message = "Error while booking: " . $conn->error;
    }
  }

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
  <div class="form-bg">
    <form method="POST" id="reservationForm">
      <div class="form-container">
        <div class="form-logo">
          <img src="./assets/images/logo.jpg" alt="logo" />
        </div>

        <h2>BOOK YOUR TABLE NOW!</h2>

        <div class="input-item">
          <input type="text" name="username" placeholder="Enter Name" required />
        </div>

        <div class="input-item">
          <input type="number" name="total_no" placeholder="Enter No. of Tables Want To Book" required min="1" />
        </div>

        <div class="input-item">
          <input type="date" name="date" id="date" required onclick="this.showPicker()" />
        </div>

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

        <div class="input-item">
          <input type="tel" name="phone_no" placeholder="Enter Phone No." required pattern="[0-9]{10}" />
        </div>

        <div class="input-item">
          <input type="email" name="email" placeholder="Enter Email ID" required />
        </div>

        <div class="btn-box">
          <button type="submit" class="btn-reserve">Confirm Reservation</button>
        </div>
      </div>
    </form>

    <div id="toast" class="toast"><?= $message ?></div>
  </div>

  <script>
    const today = new Date().toISOString().split("T")[0];
    document.getElementById("date").setAttribute("min", today);

    <?php if (!empty($message)) : ?>
      const toast = document.getElementById("toast");
      toast.classList.add("show");

      setTimeout(() => {
        toast.classList.remove("show");
      }, 3000);

      <?php if ($resetForm): ?>
        document.getElementById("reservationForm").reset();
      <?php endif; ?>
    <?php endif; ?>
  </script>
</body>
</html>
