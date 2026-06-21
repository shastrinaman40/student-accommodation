<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Student Accommodation - Listings</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Student Accommodation</a>
    <div id="nav-user-area" class="d-flex ms-auto">
      <button id="login-btn" class="btn btn-outline-primary btn-sm me-2">Login</button>
      <button id="signup-btn" class="btn btn-primary btn-sm">Sign up</button>
    </div>
  </div>
</nav>

<div class="container my-4">
  <div class="row mb-3">
    <div class="col-md-4">
      <input id="search-q" class="form-control" placeholder="Search properties by name, city or description">
    </div>
    <div class="col-md-3">
      <select id="filter-city" class="form-select">
        <option value="">All Cities</option>
        <option value="Delhi">Delhi</option>
        <option value="Mumbai">Mumbai</option>
        <option value="Bangalore">Bangalore</option>
      </select>
    </div>
    <div class="col-md-3">
      <select id="filter-gender" class="form-select">
        <option value="">Any Gender</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
        <option value="Unisex">Unisex</option>
      </select>
    </div>
    <div class="col-md-3">
      <input id="filter-price" class="form-control" placeholder="Max price" type="number">
    </div>
    <div class="col-md-3">
      <button id="apply-filters" class="btn btn-primary">Apply</button>
    </div>
  </div>

  <div id="listing" class="row"></div>
  <nav><ul id="pagination" class="pagination justify-content-center"></ul></nav>
</div>

<!-- Login Modal -->
<div class="modal" tabindex="-1" id="loginModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Login</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="mb-3"><input id="login-email" class="form-control" placeholder="Email"></div>
        <div class="mb-3"><input id="login-password" type="password" class="form-control" placeholder="Password"></div>
        <div id="login-error" class="text-danger small"></div>
      </div>
      <div class="modal-footer"><button id="login-submit" class="btn btn-primary">Login</button></div>
    </div>
  </div>
</div>

<!-- Signup Modal -->
<div class="modal" tabindex="-1" id="signupModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Sign up</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="mb-3"><input id="signup-name" class="form-control" placeholder="Full name"></div>
        <div class="mb-3"><input id="signup-email" class="form-control" placeholder="Email"></div>
        <div class="mb-3"><input id="signup-password" type="password" class="form-control" placeholder="Password"></div>
        <div id="signup-error" class="text-danger small"></div>
      </div>
      <div class="modal-footer"><button id="signup-submit" class="btn btn-primary">Create account</button></div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/main.js"></script>

<!-- React shortlist component (built) -->
<div id="shortlist-root" class="container my-4"></div>
<?php
  // include built react assets from public/react
  $assetsDir = __DIR__ . '/react/assets';
  if (is_dir($assetsDir)) {
    $files = scandir($assetsDir);
    foreach ($files as $f){
      if (preg_match('/^index-.*\\.css$/', $f)) {
        echo "<link rel=\"stylesheet\" href=\"/react/assets/$f\">\n";
      }
    }
    foreach ($files as $f){
      if (preg_match('/^index-.*\\.js$/', $f)) {
        echo "<script type=\"module\" src=\"/react/assets/$f\"></script>\n";
      }
    }
  }
?>

</body>
</html>