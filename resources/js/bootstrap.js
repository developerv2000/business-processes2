import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.addEventListener('load', () => {
    setupComponents();
});

// Modal helpers
function showModal(modal) {
    modal.classList.add('modal--visible');
}

function hideModal(modal) {
    modal.classList.remove('modal--visible');
}

function hideAllActiveModals() {
    document.querySelectorAll('.modal--visible').forEach(hideModal);
}

// FullSreen helpers
const exitFullscreen = (target) => {
    target.classList.remove('fullscreen');
    if (document.exitFullscreen) {
        document.exitFullscreen();
    } else if (document.webkitExitFullscreen) {
        document.webkitExitFullscreen();
    } else if (document.msExitFullscreen) {
        document.msExitFullscreen();
    }
};

const enterFullscreen = (target) => {
    if (target.requestFullscreen) {
        target.requestFullscreen();
    } else if (target.webkitRequestFullscreen) {
        target.webkitRequestFullscreen();
    } else if (target.msRequestFullscreen) {
        target.msRequestFullscreen();
    }
};

const toggleFullscreenClass = (target) => {
    if (document.fullscreenElement) {
        target.classList.add('fullscreen');
    } else {
        target.classList.remove('fullscreen');
    }
};

function setupComponents() {
    // ********** Dropdown **********
    // Event listener for dropdown buttons
    document.querySelectorAll('.dropdown__button').forEach((button) => {
        button.addEventListener('click', (evt) => {
            evt.currentTarget.closest('.dropdown').classList.toggle('dropdown--active');
            evt.stopPropagation(); // Prevents event propagation to document
        });
    });

    // Event listener to hide dropdowns when clicking outside
    document.addEventListener('click', (evt) => {
        document.querySelectorAll('.dropdown--active').forEach((activeDropdown) => {
            if (!activeDropdown.contains(evt.target)) {
                activeDropdown.classList.remove('dropdown--active');
            }
        });
    });

    // ********** Modal **********
    // Event listener for showing modal
    document.querySelectorAll('[data-click-action="show-modal"]').forEach((item) => {
        item.addEventListener('click', (evt) => {
            hideAllActiveModals();
            showModal(document.querySelector(evt.currentTarget.dataset.modalSelector));
        });
    });

    // Event listener for hiding modals
    document.querySelectorAll('[data-click-action="hide-active-modals"]').forEach((item) => {
        item.addEventListener('click', hideAllActiveModals);
    });

    // ********** Fullscreen **********
    // Toggle fullscren of selector
    document.querySelectorAll('[data-click-action="request-fullscreen"]').forEach((fullscreenButton) => {
        const fullscreenTarget = document.querySelector(fullscreenButton.dataset.targetSelector);

        fullscreenButton.addEventListener('click', () => {
            document.fullscreenElement ? exitFullscreen(fullscreenTarget) : enterFullscreen(fullscreenTarget);
        });

        fullscreenTarget.addEventListener('fullscreenchange', () => {
            toggleFullscreenClass(fullscreenTarget);
        });
    });
}
