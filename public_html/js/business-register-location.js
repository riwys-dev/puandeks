async function getCoordinatesFromAddress() {
  const fullAddress = `${document.getElementById('street_name').value} No:${document.getElementById('building_number').value}, ${document.getElementById('ilce').value}, ${document.getElementById('il').value}, ${document.getElementById('ulke').value}`;
  document.getElementById('address').value = fullAddress;

  try {
    const response = await fetch('/get-coordinates.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ address: fullAddress })
    });

    const data = await response.json();

    if (data.latitude && data.longitude) {
      document.getElementById('latitude').value = data.latitude;
      document.getElementById('longitude').value = data.longitude;
      return true;
    } else {
      alert("Konum alınamadı. Lütfen adresi kontrol edin.");
      return false;
    }
  } catch (error) {
    console.error('Koordinat alma hatasıfet:', error);
    alert("Sunucuya bağlanılamadı.");
    return false;
  }
}

document.getElementById('registerForm').addEventListener('submit', async function (e) {
  e.preventDefault();

  const isValid = await getCoordinatesFromAddress();
  if (!isValid) return;

  this.submit(); // Formu normal ekilde gönder
});
