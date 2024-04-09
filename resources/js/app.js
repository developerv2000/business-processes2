import './bootstrap';

const UPDATE_BODY_WIDTH_SETTINGS_URL = '/body-width';
const bodyInner = document.querySelector('.body__inner');

window.addEventListener('load', () => {
    setupDifferentEventActions();
});

function debounce(callback, timeoutDelay = 500) {
    let timeoutId;

    return (...rest) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => callback.apply(this, rest), timeoutDelay);
    };
}

function setupDifferentEventActions() {
    // ********** Leftbar Toggler **********
    // Toggle leftbar visibility
    document.querySelector('.leftbar-toggler').addEventListener('click', () => {
        axios.patch(UPDATE_BODY_WIDTH_SETTINGS_URL)
            .then(response => {
                bodyInner.classList.toggle('body__inner--shrinked');
            })
            .catch(error => {
                // Handle error
                console.error('Error:', error);
            });
    });
}
