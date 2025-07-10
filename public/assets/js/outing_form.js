function init() {
    const selectLocation = document.getElementById('select-location');
    const addLocationBtn = document.getElementById('add-location');
    const newLocationForm = document.getElementById('new-location-form');
    const locationDetails = document.getElementById('location-details');

    if (selectLocation) {
        selectLocation.addEventListener('change', function () {
            const locationId = this.value;

            if (locationId) {
                fetch(`/location/${locationId}`)
                    .then(response => response.json())
                    .then(data => {
                        locationDetails.style.display = 'block';

                        document.getElementById('location_street').value = data.street;
                        document.getElementById('location_city').value = data.city;
                        document.getElementById('location_zipCode').value = data.postalCode;
                        document.getElementById('location_latitude').value = data.latitude;
                        document.getElementById('location_longitude').value = data.longitude;
                    })
                    .catch(error => console.error('Erreur : ', error));
            } else {
                locationDetails.style.display = 'none';

                document.getElementById('location_street').value = '';
                document.getElementById('location_city').value = '';
                document.getElementById('location_zipCode').value = '';
                document.getElementById('location_latitude').value = '';
                document.getElementById('location_longitude').value = '';
            }
        });
    }


    if (addLocationBtn) {
        addLocationBtn.addEventListener('click', function () {
            newLocationForm.style.display = 'block';
            if (selectLocation) selectLocation.value = '';
            locationDetails.style.display = 'none';
        });
    }
}

document.addEventListener('DOMContentLoaded', init);

