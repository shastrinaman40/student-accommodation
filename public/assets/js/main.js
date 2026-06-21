function showLoading(el){ el.innerHTML = '<div class="loading">Loading...</div>'; }

function fetchList(){
  const city = $('#filter-city').val();
  const gender = $('#filter-gender').val();
  const max_price = $('#filter-price').val();
  $('#listing').html('<div class="loading">Loading...</div>');
  $.getJSON('/backend/api.php?action=list', {city, gender, max_price}, function(resp){
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
  });
}

$(function(){
  fetchList();
  $('#apply-filters').on('click', fetchList);
  $('#listing').on('click', '.interest-btn', function(){
    const pid = $(this).data('id');
    const btn = $(this);
    btn.prop('disabled', true).text('...');
    $.post('/backend/api.php?action=toggle_interest', {user_id:1, property_id: pid}, function(resp){
      if (resp.success) btn.text(resp.interested ? 'Interested ✓' : '♡ Interested');
      btn.prop('disabled', false);
    }, 'json');
  });
});
