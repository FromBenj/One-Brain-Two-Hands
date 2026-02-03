export function enterPositionPage() {
    const positionButton = document.getElementById('home-position-button');
    if (!positionButton) {
        return;
    }
    positionButton.addEventListener('touchstart', async (e) => {
        e.defaultPrevented;
        await getCurrentPosition();
    })
}

async function getCurrentPosition() {
    if (!navigator.geolocation) {
        console.log({
            code: 'NOT_SUPPORTED',
            message: 'Geolocation is not supported by your browser'
        });
    }
    navigator.geolocation.getCurrentPosition(
        async (position) => {
            const coordinates = {
                lat: position.coords.latitude,
                lon: position.coords.longitude,
                accuracy: position.coords.accuracy
            };
            await setUserPosition(coordinates);
        },
        (error) => {
            console.log(error);
        },
        {
            enableHighAccuracy: true,
            maximumAge: 30000,
        }
    );
}

async function setUserPosition(coordinates) {
    const formData = new FormData();
    formData.append('lat', coordinates.lat);
    formData.append('lon', coordinates.lon);
    formData.append('accuracy', coordinates.accuracy);

    fetch('/map/you', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(result => {
            console.log(result);
            window.location.href = result.redirect;
        })
        .catch(error => console.error('Error:', error));
}
