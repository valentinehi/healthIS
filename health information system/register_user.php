<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    include 'db.php';

    $data = json_decode(file_get_contents('php://input'), true);

    // Check if data is sent
    if (!$data || !isset($data['name'], $data['email'], $data['password'])) {
        echo json_encode(["status" => "error", "message" => "Incomplete data"]);
        exit;
    }

    $name = $data['name'];
    $email = $data['email'];
    $password = password_hash($data['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
    exit;
}
?>


<!DOCTYPE html>
<html>
<head><title>Register</title></head>
<style>
  /* Reset and base styles */
body {
  margin: 0;
  padding: 0;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background-color: #ffffff; /* White background */
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}

/* Container styling */
.login-container {
  background-color: #ffffff;
  padding: 40px;
  border-radius: 12px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 420px;
  text-align: center;
}

/* Title */
.login-container h2 {
 
  font-size: 28px;
  color: rgb(64, 119, 179);
}

/* Input fields */
.login-container input {
  width: 100%;
  padding: 14px;
  margin: 12px 0;
  border: 1px solid #ccc;
  border-radius: 8px;
  font-size: 16px;
  outline: none;
}

/* Button */
.login-container button {
  width: 100%;
  padding: 14px;
  background-color: rgb(56, 112, 172);
  color: white;
  font-size: 16px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.login-container button:hover {
  background-color: #0056b3;
}

/* Link */
.login-container a {
  display: block;
  margin-top: 15px;
  color: #007bff;
  text-decoration: none;
  font-size: 14px;
}

.login-container a:hover {
  text-decoration: underline;
}
input {
    width: 100%;
    border: 1px solid #ccc;
    padding: 10px;
    font-size: 16px;
    background: transparent;
    color: #333;
    margin-top: 5px; /* Add space between input and label */
}
</style>
<body>
  <h2>Register</h2>
  <form onsubmit="register(event)">
    <input type="text" id="name" placeholder="Name" required><br>
    <input type="email" id="email" placeholder="Email" required><br>
    <input type="password" id="password" placeholder="Password" required><br>
    <button>Register</button>
    <p><a href="login.php">Already have an account? Login</a></p>
  </form>

  <script>
    function register(e) {
      e.preventDefault();
      fetch("register_user.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          name: document.getElementById("name").value,
          email: document.getElementById("email").value,
          password: document.getElementById("password").value
        })
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === "success") {
          alert("Registered successfully!");
          location.href = "login_user.php";
        } else {
          alert("Error: " + (data.message || "Something went wrong"));
        }
      });
    }
  </script>
</body>
</html>
