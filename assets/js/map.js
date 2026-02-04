import gsap from 'gsap';
import L from 'leaflet';
import 'leaflet.sidepanel';
import 'leaflet.sidepanel/dist/leaflet.sidepanel.css';

export function centerMapView(event) {
    const findMe = document.getElementById('find-me-btn');
    const map = document.getElementById('user-map');
    if (!findMe || !map) {
        return;
    }
    const userMap = event.detail.map;
    const center = userMap.getCenter();
    const zoom = map.zoom;

    findMe.addEventListener('touchstart', () => {
        userMap.flyTo([center.lat, center.lng], zoom, {
            duration: 1
        });
    })
}

export function mapLoader(event) {
    const loaderContainer = document.getElementById('map-loader-container');
    if (!loaderContainer) return;
    const map = event.detail.map;
    map.whenReady(() => {
        gsap.to(loaderContainer, {
            opacity: 0,
            duration: 1.5,
            onComplete: () => {
                loaderContainer.classList.add('d-none');
            }
        });
    });
}

export function mapSidePanel(event) {
    const sidePanel = document.getElementById('map-slide-panel');
    if (!sidePanel) return;
    const map = event.detail.map;
    map.whenReady(() => {
        const panel = L.control
            .sidepanel(sidePanel.id, {
                panelPosition: 'right',
                hasTabs: false,
                tabsPosition: 'top',
                pushControls: true,
                // darkMode: true,
                defaultTab: 'tab-5',
            })
            .addTo(map);
    })
}
