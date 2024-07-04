import { hideSpinner, showSpinner, showModal, debounce, initializeNewSelectizes } from './bootstrap';

const UPDATE_BODY_WIDTH_SETTINGS_URL = '/body-width';
const GET_PRODUCTS_SIMILAR_RECORDS_URL = '/products/get-similar-records';
const GET_KVPP_SIMILAR_RECORDS_URL = '/kvpp/get-similar-records';
const GET_PROCESSES_CREATE_STAGE_INPUTS_URL = '/processes/get-create-form-stage-inputs';
const GET_PROCESSES_CREATE_FORECAST_INPUTS_URL = '/processes/get-create-form-forecast-inputs';
const GET_PROCESSES_EDIT_STAGE_INPUTS_URL = '/processes/get-edit-form-stage-inputs';
const bodyInner = document.querySelector('.body__inner');

// Colors
const rootStyles = getComputedStyle(document.documentElement);
const mainColor = rootStyles.getPropertyValue('--main-color').trim();
const textColor = rootStyles.getPropertyValue('--text-color').trim();

let countryCodesSelectize; // used as global to access it locally (used only on processes create form)

window.addEventListener('load', () => {
    bootstrapComponents();
    bootstrapForms();
    bootstrapECharts();
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

    // ========== Targeted modals ==========
    document.querySelectorAll('[data-click-action="show-targeted-modal"]').forEach((button) => {
        button.addEventListener('click', (evt) => {
            // Find the modal element based on the provided selector
            const modalSelector = evt.currentTarget.dataset.modalSelector;
            const modal = document.querySelector(modalSelector);

            // Find the input element inside the modal to update its value
            const idInput = modal.querySelector('input[name="id"]');

            // Update the input value with the target ID from the button
            idInput.value = evt.currentTarget.dataset.targetId;

            showModal(modal);
        });
    });
}

function bootstrapForms() {
    // ========== Displaying spinner on form submits ==========
    document.querySelectorAll('[data-on-submit="show-spinner"]').forEach((form) => {
        form.addEventListener('submit', showSpinner);
    })

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
                // Clone the input element
                const inputCopy = input.cloneNode(true);
                // Append the copy to the hidden inputs container

                hiddenInputsContainer.appendChild(inputCopy);
            });

            targ.submit();
        });
    });

    // ========== Specific input validations for inputs like dosage, pack etc ==========
    document.querySelectorAll('[data-on-input="validate-specific-input"]').forEach((input) => {
        input.addEventListener('input', debounce((evt) => {
            let targ = evt.target;

            targ.value = targ.value
                // Add spaces before and after '*', '+', '%' and '/' symbols
                .replace(/([+%/*])/g, ' $1 ')
                // Replace consecutive whitespaces with a single space
                .replace(/\s+/g, ' ')
                // Separate letters from numbers
                .replace(/(\d+)([a-zA-Z]+)/g, '$1 $2')
                .replace(/([a-zA-Z]+)(\d+)/g, '$1 $2')
                // Remove non-English characters
                .replace(/[^a-zA-Z0-9\s!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/g, '')
                // Remove inner whitespaces
                .replace(/\s+(?=\S)/g, ' ')
                // Replace symbols ',' with '.'
                .replace(/,/g, '.')
                // Convert the entire string to uppercase
                .toUpperCase();
        }));
    });

    // ========== Excape multiple export action ==========
    document.querySelectorAll('.export-form').forEach((form) => {
        form.addEventListener('submit', (evt) => {
            const submit = evt.target.querySelector('button[type="submit"]');
            submit.disabled = true;
        });
    });

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
    }

    // ========== Displaying similar records on products create form ==========
    // Check if the page is the products create page
    if (document.querySelector('.products-create')) {
        // Select the container where similar records will be displayed
        const similarRecordsContainer = document.querySelector('.similar-records');

        // Select the dropdowns for manufacturer, inn, and form
        let manufacturerSelect = document.querySelector('select[name="manufacturer_id"]');
        let innSelect = document.querySelector('select[name="inn_id"]');
        let formSelect = document.querySelector('select[name="form_id"]');

        // Create an array of all select dropdowns
        let selects = [innSelect, manufacturerSelect, formSelect];

        // Attach change event listeners to all select dropdowns
        for (let select of selects) {
            select.selectize.on('change', function (value) {
                displayProductsSimilarRecords();
            });
        }

        // Function to display similar records based on selected options
        function displayProductsSimilarRecords() {
            const manufacturerID = manufacturerSelect.value;
            const innID = innSelect.value;
            const formID = formSelect.value;

            // Return if any required fields are empty
            if (manufacturerID == '' || innID == '' || formID == '') {
                similarRecordsContainer.innerHTML = '';
                return;
            }

            // Prepare data to be sent in the AJAX request
            const data = {
                'manufacturer_id': manufacturerID,
                'inn_id': innID,
                'form_id': formID,
            };

            // Send a POST request to the server to get similar records
            axios.post(GET_PRODUCTS_SIMILAR_RECORDS_URL, data, {
                headers: {
                    'Content-Type': 'application/json'
                }
            })
                .then(response => {
                    // Display the similar records in the container
                    similarRecordsContainer.innerHTML = response.data;
                })
                .finally(function () {
                    // Hide any loading spinner after the request is complete
                    hideSpinner();
                });
        }
    }

    // ========== Displaying similar records on kvpp create form ==========
    // Check if the page is the kvpp create page
    if (document.querySelector('.kvpp-create')) {
        // Select the container where similar records will be displayed
        const similarRecordsContainer = document.querySelector('.similar-records');

        // Select the dropdowns for inn, form and country_code_id
        let innSelect = document.querySelector('select[name="inn_id"]');
        let formSelect = document.querySelector('select[name="form_id"]');
        let countryCodeSelect = document.querySelector('select[name="country_code_id"]');

        // Create an array of all select dropdowns
        let selects = [innSelect, formSelect, countryCodeSelect];

        // Attach change event listeners to all select dropdowns
        for (let select of selects) {
            select.selectize.on('change', function (value) {
                displayKvppSimilarRecords();
            });
        }

        // Select inputs
        let dosageInput = document.querySelector('input[name="dosage"]');
        let packInput = document.querySelector('input[name="pack"]');

        // Create an array of all inputs
        let inputs = [dosageInput, packInput];

        // Attach change event listeners to all inputs
        for (let input of inputs) {
            // delay 1000 is used because input values are also formatted via debounce
            input.addEventListener('input', debounce((evt) => {
                displayKvppSimilarRecords();
            }, 1000));
        }

        // Function to display similar records based on selected options
        function displayKvppSimilarRecords() {
            const innID = innSelect.value;
            const formID = formSelect.value;
            const countryCodeID = countryCodeSelect.value;
            const dosage = dosageInput.value;
            const pack = packInput.value;

            // Return if any required fields are empty
            if (innID == '' || formID == '' || countryCodeID == '') {
                similarRecordsContainer.innerHTML = '';
                return;
            }

            // Prepare data to be sent in the AJAX request
            const data = {
                'inn_id': innID,
                'form_id': formID,
                'country_code_id': countryCodeID,
                'dosage': dosage,
                'pack': pack,
            };

            // Send a POST request to the server to get similar records
            axios.post(GET_KVPP_SIMILAR_RECORDS_URL, data, {
                headers: {
                    'Content-Type': 'application/json'
                }
            })
                .then(response => {
                    // Display the similar records in the container
                    similarRecordsContainer.innerHTML = response.data;
                })
                .finally(function () {
                    // Hide any loading spinner after the request is complete
                    hideSpinner();
                });
        }
    }
    // ========== Handling processes create ==========
    const processesCreateForm = document.querySelector('.processes-create');

    if (processesCreateForm) {
        // Update historical process inputs on value change
        $('.historical-process-selectize').selectize({
            plugins: ["auto_position"],
            onChange(value) {
                validateHistoricalProcessDateInput(value);
            }
        });

        // Update stage inputs on status single select change
        $('.statuses-selectize').selectize({
            plugins: ["auto_position"],
            onChange(value) {
                updateProcessesCreateStageInputs(value);
            }
        });

        // Update forecast inputs on search countries multiple select change
        countryCodesSelectize = $('.country-codes-selectize').selectize({
            plugins: ["auto_position"],
            onChange(values) {
                updateProcessesCreateForecastInputs(values);
            }
        });
    }

    /**
     * Validates and toggles the visibility and required attribute of the historical process date input.
     *
     * @param {boolean} isHistorical - Determines if the process is historical.
     */
    function validateHistoricalProcessDateInput(isHistorical) {
        showSpinner();

        const inputContainer = document.querySelector('.historical-process-date-container');
        const input = inputContainer.querySelector('input[name="historical_date"]');
        console.log(isHistorical);

        // If the process is historical, make the input container visible and remove the required attribute
        if (isHistorical == true) {
            inputContainer.classList.add('historical-process-date-container--visible');
            input.setAttribute('required', 'required');
        } else {
            // If the process is not historical, hide the input container and add the required attribute
            inputContainer.classList.remove('historical-process-date-container--visible');
            input.removeAttribute('required');
        }

        hideSpinner();
    }

    function updateProcessesCreateStageInputs(status_id) {
        showSpinner();

        // Prepare data to be sent in the AJAX request
        const data = {
            'product_id': processesCreateForm.querySelector('input[name="product_id"]').value,
            'status_id': status_id,
        }

        // Send a POST request to the server to get updated stage inputs
        axios.post(GET_PROCESSES_CREATE_STAGE_INPUTS_URL, data, {
            headers: {
                'Content-Type': 'application/json'
            }
        })
            .then(response => {
                // Replace old inputs with the new ones received from the server
                const stageInputsContainer = processesCreateForm.querySelector('.processes-create__stage-inputs-container')
                stageInputsContainer.innerHTML = response.data;

                // Initialize the new selectize inputs
                initializeNewSelectizes();

                // refresh forecast inputs
                const selectedCountryCodes = countryCodesSelectize[0].selectize.getValue();
                updateProcessesCreateForecastInputs(selectedCountryCodes);
            })
            .finally(function () {
                hideSpinner();
            });
    }

    function updateProcessesCreateForecastInputs(values) {
        showSpinner();

        // Prepare data to be sent in the AJAX request
        const data = {
            'country_code_ids': values,
            'status_id': processesCreateForm.querySelector('select[name="status_id"]').value,
        }

        // Send a POST request to the server to get updated forecast inputs
        axios.post(GET_PROCESSES_CREATE_FORECAST_INPUTS_URL, data, {
            headers: {
                'Content-Type': 'application/json'
            }
        })
            .then(response => {
                // Replace old inputs with the new ones received from the server
                document.querySelector('.processes-create__forecast-inputs-container').innerHTML = response.data;
            })
            .finally(function () {
                hideSpinner();
            });
    }

    // ========== Handling processes create ==========
    const processesEditForm = document.querySelector('.processes-edit');

    if (processesEditForm) {
        // Update stage inputs on status single select change
        $('.statuses-selectize').selectize({
            plugins: ["auto_position"],
            onChange(value) {
                updateProcessesEditStageInputs(value);
            }
        });
    }

    function updateProcessesEditStageInputs(status_id) {
        showSpinner();

        // Prepare data to be sent in the AJAX request
        const data = {
            'process_id': processesEditForm.querySelector('input[name="process_id"]').value,
            'product_id': processesEditForm.querySelector('input[name="product_id"]').value,
            'status_id': status_id,
        }

        // Send a POST request to the server to get updated stage inputs
        axios.post(GET_PROCESSES_EDIT_STAGE_INPUTS_URL, data, {
            headers: {
                'Content-Type': 'application/json'
            }
        })
            .then(response => {
                // Replace old inputs with the new ones received from the server
                const stageInputsContainer = processesEditForm.querySelector('.processes-edit__stage-inputs-container')
                stageInputsContainer.innerHTML = response.data;

                // Initialize the new selectize inputs
                initializeNewSelectizes();
            })
            .finally(function () {
                hideSpinner();
            });
    }
}

function bootstrapECharts() {
    bootstrapStatisticCharts();
}

/**
 * Initializes and renders statistic charts based on global data.
 * Charts are rendered using ECharts library on the statistics index page.
 *
 * @function bootstrapStatisticCharts
 */
function bootstrapStatisticCharts() {
    // Check if we are on the statistics index page
    if (document.querySelector('.statistics-index')) {
        // Find the container element for the chart
        const container = document.querySelector('.statistics-chart1');

        // Prepare series data for bars based on general statuses
        let series = [];
        generalStatuses.forEach(status => {
            series.push({
                name: status.name,
                type: 'bar',
                data: Object.keys(status.months).map(key => ({
                    value: status.months[key].current_processes_count,
                    label: {
                        show: true,
                        position: 'top',
                        color: textColor,
                        formatter: function (params) {
                            return params.value;
                        }
                    }
                })),
            });
        });

        // Add a line series for 'Total' data
        series.push({
            name: 'Total',
            data: months.map(obj => obj.all_current_process_count),
            type: 'line',
            symbol: 'circle',
            symbolSize: 10,
            color: mainColor,
            label: {
                show: true,
                position: 'top', // Label position
                color: textColor,
                rich: {
                    labelBox: {
                        backgroundColor: '#fff', // Background color of the label box
                        borderRadius: 4, // Border radius
                        padding: [6, 10], // Padding inside the box
                        shadowBlur: 5, // Shadow blur radius
                        shadowOffsetX: 3, // Shadow offset X
                        shadowOffsetY: 3, // Shadow offset Y
                        shadowColor: 'rgba(0, 0, 0, 0.3)' // Shadow color
                    },
                    value: {
                        color: textColor
                    }
                },
                // Formatter function for customizing label content and style
                formatter: function (params) {
                    return '{labelBox|' + params.value + '}';
                },
                align: 'center',
                verticalAlign: 'bottom'
            }
        });

        // Initialize ECharts instance with specified options
        const chart = echarts.init(container, null, {
            renderer: 'canvas', // Use canvas renderer for better performance
            useDirtyRect: false, // Disable dirty rectangle optimization
            backgroundColor: 'white' // Set chart background color
        });

        // Define the main configuration options for the chart
        const option = {
            backgroundColor: '#ffffff', // Overall background color
            title: {
                text: 'Chart title', // Chart title
                padding: [28, 60, 24, 30], // Padding around the title
                textStyle: {
                    fontSize: 14, // Font size in pixels
                    fontWeight: 'bold', // Optional: Font weight (e.g., 'normal', 'bold', '600')
                    color: textColor // Optional: Font color
                }
            },
            grid: {
                left: '80px',
                right: '80px',
                top: '80px',
                bottom: '40px',
            },
            tooltip: {
                trigger: 'axis', // Tooltip trigger type
                axisPointer: {
                    type: 'cross', // Cross style for axis pointer
                    crossStyle: {
                        color: '#999' // Color of the cross style
                    }
                }
            },
            toolbox: {
                feature: {
                    dataView: { show: true, readOnly: false }, // Enable data view tool
                    magicType: { show: true, type: ['line', 'bar'] }, // Enable switch between line and bar
                    restore: { show: true }, // Enable restore button
                    saveAsImage: { show: true } // Enable save as image button
                }
            },
            legend: {
                padding: [20, 0, 20], // Padding around legend
                itemGap: 20, // Gap between legend items
                itemWidth: 20, // Width of legend item symbol
                itemHeight: 14, // Height of legend item symbol
            },
            xAxis: [
                {
                    type: 'category', // Category axis type
                    data: months.map(obj => obj.name), // Data for x-axis categories
                    axisPointer: {
                        type: 'shadow' // Pointer type for axis
                    }
                }
            ],
            yAxis: [
                {
                    type: 'value' // Value axis type
                },
                {}, // Empty placeholder for secondary y-axis if needed
            ],
            series: series, // Assign prepared series data to chart
        };

        // Set chart configuration options
        chart.setOption(option);

        // Resize chart when window is resized
        window.addEventListener('resize', function () {
            chart.resize();
        });
    }
}
