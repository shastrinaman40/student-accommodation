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
  </div>
</nav>

<div class="container my-4">
  <div class="row mb-3">
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
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/main.js"></script>

<!-- React for shortlist component -->
<script src="https://unpkg.com/react@18/umd/react.development.js"></script>
<script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js"></script>
<script src="https://unpkg.com/babel-standalone@6/babel.min.js"></script>
<script type="text/babel" src="/react/ShortlistComponent.jsx"></script>
<div id="shortlist-root" class="container my-4"></div>

</body>
</html>