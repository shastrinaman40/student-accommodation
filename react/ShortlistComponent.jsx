function ShortlistComponent(){
  const [items, setItems] = React.useState([]);
  React.useEffect(()=>{
    // demo: fetch all properties then filter by interested for user_id=1
    fetch('/backend/api.php?action=list').then(r=>r.json()).then(data=>{
      if (!data.success) return;
      // In a real app, call an endpoint to fetch user's shortlist. Here we simulate by fetching interested_users table via a new API or assume none.
      setItems([]);
    });
  }, []);
  return (
    <div className="card">
      <div className="card-body">
        <h5 className="card-title">Your Shortlist</h5>
        <p className="card-text">Shortlist loads here (demo component).</p>
      </div>
    </div>
  );
}

const root = ReactDOM.createRoot(document.getElementById('shortlist-root'));
root.render(<ShortlistComponent />);
