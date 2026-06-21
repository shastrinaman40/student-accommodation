<?php
require_once __DIR__ . '/../backend/connection.php';
session_start();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Your Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container my-4">
  <h2>Your Profile</h2>
  <div id="profile-area">
    <div class="mb-3"><label class="form-label">Name</label><input id="pf-name" class="form-control"></div>
    <div class="mb-3"><label class="form-label">Email</label><input id="pf-email" class="form-control" disabled></div>
    <div class="mb-3"><label class="form-label">Phone</label><input id="pf-phone" class="form-control"></div>
    <button id="save-profile" class="btn btn-primary">Save</button>
    <div id="pf-msg" class="mt-2"></div>
  </div>
</div>
<script>
$(function(){
  $.getJSON('/backend/api.php?action=me', function(resp){
    if (!resp.success) { $('#profile-area').html('<p>Please login first.</p>'); return; }
    $('#pf-name').val(resp.user.name);
    $('#pf-email').val(resp.user.email);
    $('#pf-phone').val(resp.user.phone);
  });

  $('#save-profile').on('click', function(){
    $.post('/backend/api.php?action=profile_update', {name: $('#pf-name').val(), phone: $('#pf-phone').val()}, function(r){
      if (r.success) $('#pf-msg').text('Saved').addClass('text-success'); else $('#pf-msg').text('Error').addClass('text-danger');
    }, 'json');
  });
});
</script>
</body>
</html>
