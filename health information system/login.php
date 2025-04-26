<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    include 'db.php';

    $data = json_decode(file_get_contents('php://input'), true);

    $email = $data['email'] ?? null;
    $password = $data['password'] ?? null;

    if (!$email || !$password) {
        echo json_encode(["status" => "error", "message" => "Missing email or password"]);
        exit;
    }

    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid password"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "User not found"]);
    }
    exit;
}
?>

<!-- HTML PART BELOW -->
<!DOCTYPE html>
<html>
  <style>
    body {
  margin: 0;
  padding: 0;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background-color: #ffffff; /* White background */
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh; /* Ensure the page takes full viewport height */
}

.login-container {
  background-color: #ffffff;
  padding: 40px;
  border-radius: 12px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
  width: 100%;
  max-width: 400px; /* Set max width to avoid it getting too wide on large screens */
  text-align: center;
}

.login-container h2 {
  margin-bottom: 20px;
  color: rgb(64, 119, 179); /* Heading color */
  font-size: 1.8rem; /* Increased size of the heading */
  font-weight: bold; /* Make the heading bold */
}

.login-container input {
  width: 100%;
  padding: 16px; /* Increased padding to make input fields bigger */
  margin: 15px 0; /* Increased margin for better spacing */
  border: 1px solid #ddd;
  border-radius: 8px;
  outline: none;
  font-size: 18px; /* Larger text inside the input fields */
}

.login-container input:focus {
  border: 2px solid rgb(56, 112, 172); /* Change border color when focused */
}

.login-container button {
  width: 100%;
  padding: 16px; /* Increased padding for button */
  background-color: rgb(56, 112, 172);
  color: white;
  font-size: 18px; /* Larger font size for the button */
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.login-container button:hover {
  background-color: #0056b3;
}

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




/* Ensure that the form is responsive on smaller screens */
@media (max-width: 480px) {
  .login-container {
    padding: 20px;
    width: 90%;
    max-width: 300px;
  }

  .login-container h2 {
    font-size: 1.5rem;
  
  }

  .login-container input, .login-container button {
    font-size: 16px;
    padding: 14px;
  }
}


</style>
<head><title>Login</title></head>
<body>
  <h2>Login</h2>
  <form onsubmit="login(event)">
    <input type="email" id="email" placeholder="Email" required><br>
    <input type="password" id="password" placeholder="Password" required><br>
    <button>Login</button>
    <p><a href="register_user.php">Don't have an account? Register</a></p>
  </form>

  <script>
    function login(e) {
      e.preventDefault();
      fetch("login.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          email: document.getElementById("email").value,
          password: document.getElementById("password").value
        })
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === "success") {
          alert("Login successful!");
          location.href = "dashboard.php";
        } else {
          alert("Login failed: " + (data.message || "Unknown error"));
        }
      });
    }
  </script>
</body>
</html>
