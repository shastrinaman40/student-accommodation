function showLoading(el){ el.innerHTML = '<div class="loading">Loading...</div>'; }

function fetchList(){
  const city = $('#filter-city').val();
  const gender = $('#filter-gender').val();
  const max_price = $('#filter-price').val();
  const q = $('#search-q').val();
  $('#listing').html('<div class="loading">Loading...</div>');
  $.getJSON('/backend/api.php?action=list', {city, gender, max_price, q, page: currentPage, per_page: perPage}, function(resp){
    if (!resp.success) { $('#listing').html('Error'); return; }
    const data = resp.data;
    if (!data.length) { $('#listing').html('<p>No properties found.</p>'); return; }
    const html = data.map(p=>{
      const img = (p.images && p.images.length) ? ('/'+p.images[0].trim()) : 'https://via.placeholder.com/300x180';
      return `
      <div class="col-md-4 mb-4">
        <div class="card">
          <img src="${img}" class="card-img-top">
          <div class="card-body">
            <h5 class="card-title">${p.name}</h5>
            <p class="card-text">${p.city} · ₹${p.price} · ${p.gender}</p>
            <a href="/property.php?id=${p.id}" class="btn btn-sm btn-primary">View</a>
            <button class="btn btn-sm btn-outline-success ms-2 interest-btn" data-id="${p.id}">♡ Interested</button>
          </div>
        </div>
      </div>
      `;
    }).join('');
    $('#listing').html(html);
    // pagination
    const total = resp.total || 0;
    const pages = Math.max(1, Math.ceil(total / resp.per_page));
    const pagHtml = [];
    for (let i=1;i<=pages;i++){
      pagHtml.push(`<li class="page-item ${i===resp.page?'active':''}"><a class="page-link pag-link" href="#" data-page="${i}">${i}</a></li>`);
    }
    $('#pagination').html(pagHtml.join(''));
  });
}

$(function(){
  let currentPage = 1;
  const perPage = 6;
  $(document).on('click','.pag-link', function(e){ e.preventDefault(); currentPage = parseInt($(this).data('page')); fetchList(); });
  let currentUser = null;
  function loadMe(){
    $.getJSON('/backend/api.php?action=me', function(resp){
      if (resp.success) {
        currentUser = resp.user;
        $('#nav-user-area').html(`<div class="me-2">Hello, ${resp.user.name}</div><button id="logout-btn" class="btn btn-outline-secondary btn-sm">Logout</button>`);
      } else {
        $('#nav-user-area').html('<button id="login-btn" class="btn btn-outline-primary btn-sm me-2">Login</button><button id="signup-btn" class="btn btn-primary btn-sm">Sign up</button>');
      }
    });
  }
  loadMe();

  fetchList();
  $('#search-q').on('keypress', function(e){ if (e.key === 'Enter') { currentPage=1; fetchList(); } });
  $('#apply-filters').on('click', fetchList);
  $('#listing').on('click', '.interest-btn', function(){
    const pid = $(this).data('id');
    const btn = $(this);
    if (!currentUser) { $('#loginModal').modal('show'); return; }
    btn.prop('disabled', true).text('...');
    $.post('/backend/api.php?action=toggle_interest', {property_id: pid}, function(resp){
      if (resp.success) btn.text(resp.interested ? 'Interested ✓' : '♡ Interested');
      btn.prop('disabled', false);
      loadMe();
    }, 'json');
  });

  // handle login/signup buttons via delegation
  $(document).on('click', '#login-btn', function(){ $('#loginModal').modal('show'); });
  $(document).on('click', '#signup-btn', function(){ $('#signupModal').modal('show'); });
  $(document).on('click', '#logout-btn', function(){ $.getJSON('/backend/api.php?action=logout', function(){ currentUser=null; loadMe(); }); });

  $('#login-submit').on('click', function(){
    const email = $('#login-email').val();
    const password = $('#login-password').val();
    $.post('/backend/api.php?action=login', {email, password}, function(resp){
      if (resp.success) { $('#loginModal').modal('hide'); loadMe(); fetchList(); }
      else $('#login-error').text('Invalid credentials');
    }, 'json');
  });

  $('#signup-submit').on('click', function(){
    const name = $('#signup-name').val();
    const email = $('#signup-email').val();
    const password = $('#signup-password').val();
    $.post('/backend/api.php?action=signup', {name, email, password}, function(resp){
      if (resp.success) { $('#signupModal').modal('hide'); loadMe(); fetchList(); }
      else $('#signup-error').text('Error creating account');
    }, 'json');
  });
});
