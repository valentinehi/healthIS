<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Health Info System Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; }
    .card { border-radius: 1rem; }
    .nav-link.active { font-weight: bold; }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Health Dashboard</a>
      <button class="btn btn-danger ms-auto" onclick="logout()">Logout</button>
    </div>
  </nav>


  <div class="container mt-4">
    <div class="row">
      <!-- Sidebar -->
      <div class="col-md-3">
        <div class="list-group">
          <a href="#" class="list-group-item list-group-item-action active" onclick="showSection('programs')">Programs</a>
          <a href="#" class="list-group-item list-group-item-action" onclick="showSection('clients')">Clients</a>
        
          <a href="#" class="list-group-item list-group-item-action" onclick="showSection('search')">Search Client</a>
        </div>
      </div>

      <!-- Main Content -->
      <div class="col-md-9">
        <div id="programs" class="section">
          <h4>Create Health Program</h4>
          <form id="programForm">
            <input type="text" class="form-control mb-2" id="programName" placeholder="Program Name (e.g., TB, Malaria)">
            <button type="submit" class="btn btn-primary">Add Program</button>
          </form>
        </div>

        <div id="clients" class="section d-none">
          <h4>Register New Client</h4>
          <form id="clientForm">
            <input type="text" class="form-control mb-2" id="clientName" placeholder="Full Name">
            <input type="text" class="form-control mb-2" id="clientAge" placeholder="Age">
            <input type="text" class="form-control mb-2" id="clientContact" placeholder="Contact Info">
            <button type="submit" class="btn btn-success">Register Client</button>
          </form>
        </div>
        <div id="profile" class="section d-none">
  <h4>Client Profile</h4>
  <div id="profileDetails"></div>
</div>


      
        <div id="search" class="section d-none">
          <h4>Search Client</h4>
          <input type="text" id="searchInput" class="form-control mb-2" placeholder="Enter client name">
          <button class="btn btn-info" onclick="searchClient()">Search</button>
          <div id="searchResult" class="mt-3"></div>
          

        </div>
      </div>
    </div>
  </div>

  <script>
    function showSection(id) {
      document.querySelectorAll('.section').forEach(el => el.classList.add('d-none'));
      document.getElementById(id).classList.remove('d-none');
      document.querySelectorAll('.list-group-item').forEach(el => el.classList.remove('active'));
      event.target.classList.add('active');
    }
    document.getElementById("programForm").addEventListener("submit", function(e) {
  e.preventDefault();
  const name = document.getElementById("programName").value;

  fetch("create_program.php", { // Make sure the endpoint is correct
    method: "POST",
    headers: {"Content-Type": "application/json"},
    body: JSON.stringify({ name })
  })
  .then(res => res.json())
  .then(data => {
    if (data.status === "success") {
      alert("Program created!");
      document.getElementById("programName").value = "";
    } else {
      alert("Failed to create program: " + data.message);
    }
  })
  .catch(err => alert("Error: " + err));
});
document.getElementById("clientForm").addEventListener("submit", function(e) {
  e.preventDefault();

  const name = document.getElementById("clientName").value;
  const age = document.getElementById("clientAge").value;
  const contact = document.getElementById("clientContact").value;

  fetch("register_client.php", { 
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ name, age, contact })
  })
  .then(res => res.json())
  .then(data => {
    if (data.status === "success") {
      alert("Client registered successfully!");
      document.getElementById("clientForm").reset();
    } else {
      alert("Failed to register client.");
    }
  })
  .catch(err => alert("Error: " + err));
});

function searchClient() {
  const search = document.getElementById("searchInput").value;

  fetch("search_client.php", {
    method: "POST",
    headers: {"Content-Type": "application/json"},
    body: JSON.stringify({ search })
  })
  .then(res => res.json())
  .then(data => {
    const resultDiv = document.getElementById("searchResult");
    resultDiv.innerHTML = "";

    if (data.length === 0) {
      resultDiv.innerHTML = "<p>No clients found</p>";
      return;
    }

    data.forEach(client => {
      const clientHTML = `
        <div class="card p-3 mb-2">
          <h5>${client.name}</h5>
          <p>Age: ${client.age}</p>
          <p>Contact: ${client.contact}</p>
          <button onclick="viewClientProfile(${client.id})" class="btn btn-info btn-sm">View Profile</button>
        </div>
      `;
      resultDiv.innerHTML += clientHTML;
    });
  



    fetch("get_programs.php")  // Fetch all programs
      .then(res => res.json())
      .then(programs => {
        data.forEach(client => {
          const div = document.createElement("div");
          div.className = "card p-2 mb-2";
          div.innerHTML = `
            <strong>${client.name}</strong> (Age: ${client.age})<br>
            Contact: ${client.contact}<br>
            <select class="form-select mt-2" id="program-${client.id}">
              ${programs.map(p => `<option value="${p.id}">${p.name}</option>`).join('')}
            </select>
            <button class="btn btn-warning mt-2" onclick="enrollClient(${client.id})">Enroll</button>
          `;
          resultDiv.appendChild(div);
        });
      });
  });
}

function enrollClient(client_id) {
  const program_id = document.getElementById(`program-${client_id}`).value;
  fetch("enroll_clients.php", {
    method: "POST",
    headers: {"Content-Type": "application/json"},
    body: JSON.stringify({ client_id, program_id })
  })
  .then(res => res.json())
  .then(data => {
    if (data.status === "success") {
      alert("Client enrolled successfully!");
    } else {
      alert("Enrollment failed: " + data.message);
    }
  });
}
function viewClientProfile(clientId) {
  fetch("get_client_profile.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ client_id: clientId })
  })
  .then(res => res.json())
  .then(data => {
    const profileHTML = `
      <h5>Client Profile</h5>
      <p><strong>Name:</strong> ${data.client.name}</p>
      <p><strong>Age:</strong> ${data.client.age}</p>
      <p><strong>Contact:</strong> ${data.client.contact}</p>
      <h6>Enrolled Programs:</h6>
     <ul>${data.programs.map(p => `<li>${p.name}</li>`).join("")}</ul>

    `;
    document.getElementById("searchResult").innerHTML = profileHTML;
  });
}










  </script>
</body>
</html>