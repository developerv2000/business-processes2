import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.addEventListener('load', () => {
    setupComponents();
});

function setupComponents() {
    // ********** Dropdown **********
    // Dropdown button
    document.querySelectorAll('.dropdown__button').forEach((button) => {
        button.addEventListener('click', (evt) => {
            evt.target.closest('.dropdown').classList.toggle('dropdown--active');
        });
    });

    // Hiding dropdown
    document.addEventListener('click', function (evt) {
        document.querySelectorAll('.dropdown--active').forEach((activeDropdown) => {
            // Check if event target is outside of active dropdown
            if (!activeDropdown.contains(evt.target)) {
                activeDropdown.classList.remove('dropdown--active');
            }
        });
    });
}
