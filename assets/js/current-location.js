function getCurrentLocation() {
    console.log('yes');
    return new Promise((resolve, reject) => {
        if (!navigator.geolocation) {
            reject({
                code: 'NOT_SUPPORTED',
                message: 'Geolocation is not supported by your browser'
            });
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const coordinates = {
                    lat: position.coords.latitude,
                    lon: position.coords.longitude,
                    accuracy: position.coords.accuracy
                };
                resolve(coordinates);
            },
            (error) => {
                reject(error);
            },
            {
                enableHighAccuracy: false,
                timeout: 10000,
                maximumAge: 0
            }
        );
    });
}

export async function fetchCoordinates() {
    try{
    const coordinates = await getCurrentLocation();
    console.log('Current coordinates:', coordinates);
    const response = await fetch('/map/', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(coordinates)
    });
        const data = await response.json();
        console.log(data);

    } catch (error) {
        console.error('Geolocation Error:', error.code, error.message);
    }
}

