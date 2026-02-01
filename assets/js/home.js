import gsap from 'gsap';

export function homeElementsAppear() {
    const homeEl = document.getElementsByClassName('home-appear');
    let delay = 1;
    if (homeEl.length > 0) {
        for (let i = 0; i < homeEl.length; i++) {
            gsapMakeAppear(homeEl[i], delay + i * 0.15);
        }
    }

    function gsapMakeAppear(element, delay) {
        gsap.to(element,
            {
                duration: 0.5,
                opacity: 1,
                delay: delay,
            })
    }
}
