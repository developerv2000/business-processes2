import { hideSpinner, showSpinner, showModal } from './bootstrap';

const UPDATE_BODY_WIDTH_SETTINGS_URL = '/body-width';
const bodyInner = document.querySelector('.body__inner');

window.addEventListener('load', () => {
    bootstrapComponents();
    bootstrapForms();
});

function bootstrapComponents() {
    // ========== Leftbar Toggler ==========
    // Toggle leftbar visibility
    document.querySelector('.leftbar-toggler').addEventListener('click', () => {
        axios.patch(UPDATE_BODY_WIDTH_SETTINGS_URL)
            .then(response => {
                bodyInner.classList.toggle('body__inner--shrinked');
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });

    // ========== Table columns edit form width trackbar ==========
    // Increase & decrase trackbar width
    document.querySelectorAll('.sortable-columns__width-input').forEach(trackbar => {
        trackbar.addEventListener('input', (evt) => {
            const sortableItem = evt.target.closest('.sortable-columns__item');
            const widthDiv = sortableItem.querySelector('.sortable-columns__width');
            widthDiv.style.width = evt.target.value + 'px';
        });
    });

    // ========== Main Table Select all toggler ==========
    document.querySelector('.th__select-all')?.addEventListener('click', () => {
        let checkboxes = document.querySelectorAll('.main-table .td__checkbox');
        let checkedAll = document.querySelector('.main-table .td__checkbox:not(:checked)') ? false : true;

        // toggle checkbox statements
        checkboxes.forEach((checkbox) => {
            checkbox.checked = !checkedAll;
        });
    });

    // ========== Tables limited text overflow toggler ==========
    document.querySelector('.main-table')?.addEventListener('click', (evt) => {
        const target = evt.target;

        if (target.dataset.onClick == 'toggle-text-limit') {
            target.classList.toggle('td__limited-text');
        }
    });

    // ========== Sortable columns ==========
    $('.sortable-columns').sortable();
}

function bootstrapForms() {
    // ========== Table columns edit form ==========
    document.querySelector('.table-columns-edit-form')?.addEventListener('submit', (evt) => {
        evt.preventDefault();
        showSpinner();

        const form = evt.target;
        const table = form.querySelector('input[name="table"]').value;
        const columns = Array.from(form.querySelectorAll('.sortable-columns__item')).map(mapTableColumnData);

        axios.patch(form.action, { columns, table }, {
            headers: {
                'Content-Type': 'application/json'
            }
        })
            .then(response => {
                window.location.reload();
            })
            .finally(hideSpinner);
    });

    function mapTableColumnData(item, index) {
        const column = {};
        column.name = item.dataset.columnName;
        column.order = index + 1;
        column.width = parseInt(item.querySelector('.sortable-columns__width-input').value);
        column.visible = item.querySelector('.switch').checked ? 1 : 0;
        return column;
    }

    // ========== Filter Form ==========
    const filterForm = document.querySelector('.filter-form');

    if (filterForm) {
        // Remove unnecessary fields before submit
        filterForm.addEventListener('submit', (evt) => {
            // Remove empty input fields
            evt.target.querySelectorAll('input').forEach((input) => {
                if (!input.value) {
                    input.remove();
                }
            });

            // Remove empty select fields
            evt.target.querySelectorAll('.singular-selectize').forEach((select) => {
                if (!select.value) {
                    select.remove();
                }
            });
        });

        // Move active filters to the top
        // Multiple selects
        filterForm.querySelectorAll('div.multiple-selectize--highlight').forEach((select) => {
            const formGroup = select.closest('.form-group');
            filterForm.insertBefore(formGroup, filterForm.firstChild);
        });

        // Single selects
        filterForm.querySelectorAll('div.singular-selectize--highlight').forEach((select) => {
            const formGroup = select.closest('.form-group');
            filterForm.insertBefore(formGroup, filterForm.firstChild);
        });

        // Inputs
        filterForm.querySelectorAll('.input--highlight').forEach((input) => {
            const formGroup = input.closest('.form-group');
            filterForm.insertBefore(formGroup, filterForm.firstChild);
        });

        // ========== Appending inputs before form submit ==========
        /**
         * Handles the form submission event by appending inputs to the form.
         * Especially Used in multiple restore & delete table actions
         */

        document.querySelectorAll('[data-before-submit="appends-inputs"]').forEach((form) => {
            form.addEventListener('submit', (evt) => {
                // Prevent default form submission
                evt.preventDefault();

                const targ = evt.target;
                const inputs = document.querySelectorAll(targ.dataset.inputsSelector);

                // Append each input to the form
                const hiddenInputsContainer = targ.querySelector('.form__hidden-inputs-container');

                inputs.forEach((input) => {
                    hiddenInputsContainer.appendChild(input);
                });

                targ.submit();
            });
        });
    }
}
