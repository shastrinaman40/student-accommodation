import React, {useEffect, useState} from 'react'

export default function App(){
  const [items,setItems] = useState(null);
  const userId = 1; // demo

  useEffect(()=>{
    fetch(`/backend/api.php?action=shortlist&user_id=${userId}`).then(r=>r.json()).then(data=>{
      if (data.success) setItems(data.data);
      else setItems([]);
    });
  },[]);

  function remove(id){
    fetch('/backend/api.php?action=toggle_interest', {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:`user_id=${userId}&property_id=${id}`}).then(r=>r.json()).then(()=>{
      setItems(items.filter(i=>i.id!==id));
    });
  }

  if (items === null) return <div className="card"><div className="card-body">Loading shortlist...</div></div>
  return (
    <div className="card">
      <div className="card-body">
        <h5 className="card-title">Your Shortlist</h5>
        {items.length===0 && <p>No items in shortlist.</p>}
        <div className="list-group">
          {items.map(it=> (
            <div key={it.id} className="list-group-item d-flex justify-content-between align-items-center">
              <div>
                <strong>{it.name}</strong>
                <div className="small">{it.city} · ₹{it.price}</div>
              </div>
              <div>
                <a className="btn btn-sm btn-primary me-2" href={`/property.php?id=${it.id}`}>View</a>
                <button className="btn btn-sm btn-outline-danger" onClick={()=>remove(it.id)}>Remove</button>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  )
}
