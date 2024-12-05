import { hideSpinner, showSpinner, showModal, debounce, initializeNewSelectizes } from './bootstrap';

const bodyInner = document.querySelector('.body__inner');

const UPDATE_BODY_WIDTH_SETTINGS_URL = '/body-width';
const GET_PRODUCTS_SIMILAR_RECORDS_URL = '/products/get-similar-records';
const GET_KVPP_SIMILAR_RECORDS_URL = '/kvpp/get-similar-records';
const GET_PROCESSES_CREATE_STAGE_INPUTS_URL = '/processes/get-create-form-stage-inputs';
const GET_PROCESSES_CREATE_FORECAST_INPUTS_URL = '/processes/get-create-form-forecast-inputs';
const GET_PROCESSES_EDIT_STAGE_INPUTS_URL = '/processes/get-edit-form-stage-inputs';
const UPDATE_PROCESSES_CONTRACTED_IN_PLAN_URL = '/processes/update-contracted-in-plan-value';
const UPDATE_PROCESSES_REGISTERED_IN_PLAN_URL = '/processes/update-registered-in-plan-value';
const MARK_PROCESS_AS_READY_FOR_ORDER_URL = '/processes/mark-as-ready-for-order';
const GET_ORDERS_CREATE_PRODUCT_INPUTS_URL = '/orders/get-create-product-inputs';
const GET_ORDER_PRODUCTS_LIST_ON_INVOICE_CREATE = '/invoices/get/order-product-lists-on-create';

// Colors
const rootStyles = getComputedStyle(document.documentElement);
const theme = rootStyles.getPropertyValue('--theme-name').trim();
const mainColor = rootStyles.getPropertyValue('--theme-main-color').trim();
const textColor = rootStyles.getPropertyValue('--theme-text-color').trim();
const boxBackgroundColor = rootStyles.getPropertyValue('--theme-background-color').trim();
const chartLabelBackgroundColor = rootStyles.getPropertyValue('--theme-chart-label-background-color').trim();
const chartSplitlinesColor = rootStyles.getPropertyValue('--theme-chart-splitlines-color').trim();

// Globals
let countryCodesSelectize; // used only on processes create form
let processesCountChart, activeManufacturersChart;
let orderCreateProductIndex = 0;
let invoiceCreateOtherPaymentsIndex = 0;

window.addEventListener('load', () => {
    bootstrapComponents();
    bootstrapForms();
    bootstrapECharts();
    boostrapProcessesPlanCheckboxes();
    boostrapOrderingProcessesCheckboxes();
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

    // ========== Handling processes CRUID ==========
    const processesCreatePage = document.querySelector('.processes-create');
    const processesEditPage = document.querySelector('.processes-edit');
    const processesDuplicatePage = document.querySelector('.processes-duplicate');

    if (processesCreatePage) {
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

    // Update stage inputs on status single select change
    if (processesEditPage || processesDuplicatePage) {
        $('.statuses-selectize').selectize({
            plugins: ["auto_position"],
            onChange(value) {
                updateProcessesEditStageInputs(value);
            }
        });
    }

    // Update historical process inputs on value change
    if (processesDuplicatePage) {
        $('.historical-process-selectize').selectize({
            plugins: ["auto_position"],
            onChange(value) {
                validateHistoricalProcessDateInput(value);
            }
        });
    }

    /**
     * Validates and toggles the visibility and required attribute of the historical process date input.
     *
     * Used on processes create and duplicate
     *
     * @param {boolean} isHistorical - Determines if the process is historical.
     */
    function validateHistoricalProcessDateInput(isHistorical) {
        showSpinner();

        const inputContainer = document.querySelector('.historical-process-date-container');
        const input = inputContainer.querySelector('input[name="historical_date"]');

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
            'product_id': document.querySelector('input[name="product_id"]').value,
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
                const stageInputsContainer = document.querySelector('.processes-create__stage-inputs-container')
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
            'status_id': document.querySelector('select[name="status_id"]').value,
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

    function updateProcessesEditStageInputs(status_id) {
        showSpinner();

        // Prepare data to be sent in the AJAX request
        const data = {
            'process_id': document.querySelector('input[name="process_id"]').value,
            'product_id': document.querySelector('input[name="product_id"]').value,
            'duplicating': document.querySelector('input[name="duplicating"]').value, // duplicating page
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
                const stageInputsContainer =
                    document.querySelector('.processes-edit__stage-inputs-container, .processes-duplicate__stage-inputs-container');

                stageInputsContainer.innerHTML = response.data;

                // Initialize the new selectize inputs
                initializeNewSelectizes();
            })
            .finally(function () {
                hideSpinner();
            });
    }
}

// ========== ECharts ==========
function bootstrapECharts() {
    bootstrapStatisticCharts();
}

function bootstrapStatisticCharts() {
    if (document.querySelector('.statistics-index')) {
        bootstrapProcessesCountChart();
        bootstrapActiveManufacturersChart();

        // Custom saving of charts as image
        document.querySelector('.processes-count-chart__download-btn').addEventListener('click', downloadProcessesCountChart);
        document.querySelector('.active-manufacturers-chart__download-btn').addEventListener('click', downloadActiveManufacturersChart);

        // Resize chart when window is resized
        window.addEventListener('resize', function () {
            processesCountChart.resize();
            activeManufacturersChart.resize();
        });
    }
}

function bootstrapProcessesCountChart() {
    // Find the container element for the chart
    const container = document.querySelector('.processes-count-chart');

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
                    backgroundColor: chartLabelBackgroundColor, // Background color of the label box
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
    processesCountChart = echarts.init(container, theme, {
        renderer: 'canvas', // Use canvas renderer for better performance
        useDirtyRect: false, // Disable dirty rectangle optimization
        backgroundColor: boxBackgroundColor // Set chart background color
    });

    // Define the main configuration options for the chart
    const option = {
        backgroundColor: boxBackgroundColor, // Overall background color
        title: {
            text: 'Ключевые показатели по тщательной обработке продуктов по месяцам', // Chart title
            padding: [24, 60, 24, 30], // Padding around the title
            textStyle: {
                fontSize: 14, // Font size in pixels
                fontFamily: ['Fira Sans', 'sans-serif'],
                fontWeight: '500', // Optional: Font weight (e.g., 'normal', 'bold', '600')
                color: textColor // Optional: Font color
            }
        },
        grid: {
            left: '60px',
            right: '60px',
            top: '112px',
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
                // dataView: { show: true, readOnly: false }, // Enable data view tool
                magicType: { show: true, type: ['line', 'bar'] }, // Enable switch between line and bar
                restore: { show: true }, // Enable restore button
                saveAsImage: { show: true } // Enable save as image button
            }
        },
        legend: {
            padding: [52, 0, 20], // Padding around legend
            itemGap: 12, // Gap between legend items
            itemWidth: 20, // Width of legend item symbol
            itemHeight: 14, // Height of legend item symbol
            textStyle: {
                fontSize: 14, // Font size in pixels
                fontFamily: ['Fira Sans', 'sans-serif'],
                fontWeight: '400', // Optional: Font weight (e.g., 'normal', 'bold', '600')
                color: textColor // Optional: Font color
            }
        },
        xAxis: [
            {
                type: 'category', // Category axis type
                data: months.map(obj => obj.name), // Data for x-axis categories
                axisPointer: {
                    type: 'shadow' // Pointer type for axis
                },
            }
        ],
        yAxis: [
            {
                type: 'value', // Value axis type
                splitLine: {
                    lineStyle: {
                        color: chartSplitlinesColor, // Color of the horizontal grid lines
                    }
                }
            },
            {}, // Empty placeholder for secondary y-axis if needed
        ],
        series: series, // Assign prepared series data to chart
    };

    // Set chart configuration options
    processesCountChart.setOption(option);
}

function downloadProcessesCountChart() {
    // Get the chart image data URL
    const imageDataURL = processesCountChart.getConnectedDataURL({
        type: 'image/png',   // Can also be 'image/jpeg' or 'image/svg+xml'
        pixelRatio: 2,       // Adjust pixel ratio for higher quality if needed
        // backgroundColor: '#fff'  // Set background color if needed
    });

    // Create a temporary anchor element for download
    let downloadLink = document.createElement('a');
    downloadLink.href = imageDataURL;
    downloadLink.download = 'processes-count-chart.png';  // Filename when downloaded

    // Append anchor to body and trigger the download
    document.body.appendChild(downloadLink);
    downloadLink.click();

    // Clean up
    document.body.removeChild(downloadLink);
}

function bootstrapActiveManufacturersChart() {
    // Find the container element for the chart
    const container = document.querySelector('.active-manufacturers-chart');

    // Initialize ECharts instance with specified options
    activeManufacturersChart = echarts.init(container, theme, {
        renderer: 'canvas', // Use canvas renderer for better performance
        useDirtyRect: false, // Disable dirty rectangle optimization
        backgroundColor: boxBackgroundColor // Set chart background color
    });

    // Define the main configuration options for the chart
    const option = {
        backgroundColor: boxBackgroundColor, // Overall background color
        title: {
            text: 'Количество активных производителей по месяцам', // Chart title
            padding: [24, 60, 24, 30], // Padding around the title
            textStyle: {
                fontSize: 14, // Font size in pixels
                fontFamily: ['Fira Sans', 'sans-serif'],
                fontWeight: '500', // Optional: Font weight (e.g., 'normal', 'bold', '600')
                color: textColor // Optional: Font color
            }
        },
        grid: {
            left: '60px',
            right: '60px',
            top: '68px',
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
                magicType: { show: true, type: ['line', 'bar'] }, // Enable switch between line and bar
                restore: { show: true }, // Enable restore button
                saveAsImage: { show: true } // Enable save as image button
            }
        },
        xAxis: {
            type: 'category',
            data: months.map(obj => obj.name), // Data for x-axis categories
        },
        yAxis: {
            type: 'value',
            splitLine: {
                lineStyle: {
                    color: chartSplitlinesColor, // Color of the horizontal grid lines
                }
            }
        },
        series: [
            {
                data: months.map(obj => obj.active_manufacturers_count), // Data for x-axis categories
                type: 'bar',
                symbol: 'circle',
                symbolSize: 10,
                color: mainColor,
                label: {
                    show: true,
                    position: 'top', // Label position
                    color: textColor,
                    rich: {
                        labelBox: {
                            backgroundColor: chartLabelBackgroundColor, // Background color of the label box
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
            }
        ]
    };

    // Set chart configuration options
    activeManufacturersChart.setOption(option);
}

function downloadActiveManufacturersChart() {
    // Get the chart image data URL
    const imageDataURL = activeManufacturersChart.getConnectedDataURL({
        type: 'image/png',   // Can also be 'image/jpeg' or 'image/svg+xml'
        pixelRatio: 2,       // Adjust pixel ratio for higher quality if needed
        // backgroundColor: '#fff'  // Set background color if needed
    });

    // Create a temporary anchor element for download
    let downloadLink = document.createElement('a');
    downloadLink.href = imageDataURL;
    downloadLink.download = 'active-manufacturers-chart.png';  // Filename when downloaded

    // Append anchor to body and trigger the download
    document.body.appendChild(downloadLink);
    downloadLink.click();

    // Clean up
    document.body.removeChild(downloadLink);
}


// ========== Processes plan (SPG) boolean togglers ==========
function boostrapProcessesPlanCheckboxes() {
    // Contacted toggling
    document.querySelectorAll('[data-toggle-action="toggle-process-contracted-boolean"]')
        .forEach((chbs) => chbs.addEventListener('change', function (evt) {
            showSpinner();

            const chb = evt.target;
            const processID = chb.dataset.processId;

            const data = {
                'contracted': chb.checked,
                'process_id': processID,
            };

            axios.post(UPDATE_PROCESSES_CONTRACTED_IN_PLAN_URL, data, {
                headers: {
                    'Content-Type': 'application/json'
                }
            })
                .then(response => {
                    // console.log(response)
                })
                .finally(function () {
                    // Hide any loading spinner after the request is complete
                    hideSpinner();
                });
        }));

    // Registered toggling

    document.querySelectorAll('[data-toggle-action="toggle-process-registered-boolean"]')
        .forEach((chbs) => chbs.addEventListener('change', function (evt) {
            showSpinner();

            const chb = evt.target;
            const processID = chb.dataset.processId;

            const data = {
                'registered': chb.checked,
                'process_id': processID,
            };

            axios.post(UPDATE_PROCESSES_REGISTERED_IN_PLAN_URL, data, {
                headers: {
                    'Content-Type': 'application/json'
                }
            })
                .then(response => {
                    // console.log(response)
                })
                .finally(function () {
                    // Hide any loading spinner after the request is complete
                    hideSpinner();
                });
        }));
}


// ========== Send process for application checkboxes ==========
function boostrapOrderingProcessesCheckboxes() {
    // Contacted toggling
    document.querySelectorAll('[data-check-action="mark-process-as-ready-for-order"]')
        .forEach((chbs) => chbs.addEventListener('change', function (evt) {
            showSpinner();

            const chb = evt.target;
            const processID = chb.dataset.processId;

            const data = {
                'process_id': processID,
            };

            axios.post(MARK_PROCESS_AS_READY_FOR_ORDER_URL, data, {
                headers: {
                    'Content-Type': 'application/json'
                }
            })
                .then(response => {
                    if (!response.data.success) {
                        alert(response.data.message);
                    }
                })
                .finally(function () {
                    chb.checked = true;
                    chb.disabled = true;

                    // Hide any loading spinner after the request is complete
                    hideSpinner();
                });
        }));
}


document.querySelectorAll('.orders-create__add-product-btn').forEach((btn) => {
    btn.addEventListener('click', function (evt) {
        evt.preventDefault();
        showSpinner();

        const manufacturerID = document.querySelector('select[name="manufacturer_id"]').value;
        const countryID = document.querySelector('select[name="country_code_id"]').value;
        const productsList = document.querySelector('.orders-create__products-list');

        const data = {
            'manufacturer_id': manufacturerID,
            'country_code_id': countryID,
            'product_index': orderCreateProductIndex,
        };

        axios.post(GET_ORDERS_CREATE_PRODUCT_INPUTS_URL, data, {
            headers: {
                'Content-Type': 'application/json'
            }
        })
            .then(response => {
                const template = document.createElement('template');
                template.innerHTML = response.data;
                const element = template.content.firstChild;

                productsList.appendChild(element);
                initializeNewSelectizes();
                initializeOrdersCreateProductDeleteButtons();
                orderCreateProductIndex++;
            })
            .finally(function () {
                // Hide any loading spinner after the request is complete
                hideSpinner();
            });
    })
});

function initializeOrdersCreateProductDeleteButtons() {
    document.querySelectorAll('.orders-create__delete-product-btn').forEach((btn) => {
        btn.addEventListener('click', function (evt) {
            evt.currentTarget.closest('.form__section').remove();
        })
    });
}

document.querySelector('.invoices-create-goods .create-form')?.addEventListener('submit', function (evt) {
    // Check submit action
    const submitButtonText = evt.currentTarget.querySelector('.form__submit .button__text');

    // Load order products lists if not loeaded yet
    if (submitButtonText.textContent === 'Load products') {
        loadInvoiceOrderProductListsOnCreate(evt, submitButtonText);
    }
});

function loadInvoiceOrderProductListsOnCreate(evt, submitButtonText) {
    evt.preventDefault();

    const ordersSelect = document.querySelector('select[name="order_ids[]"]');
    const orderIDs = Array.from(ordersSelect.selectedOptions).map(option => option.value);
    const paymentTypeID = document.querySelector('select[name="payment_type_id"]').value;

    const data = {
        'order_ids': orderIDs,
        'payment_type_id': paymentTypeID,
    }

    axios.post(GET_ORDER_PRODUCTS_LIST_ON_INVOICE_CREATE, data, {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    })
        .then(response => {
            const productsListWrapper = document.querySelector('.invoices-create-goods__products-list-wrapper');
            productsListWrapper.innerHTML = response.data;
            submitButtonText.textContent = 'Store';
            initializeInvoicesCreateOtherPaymentsAddButton();
        })
        .finally(function () {
            // Hide any loading spinner after the request is complete
            hideSpinner();
        });
}


function initializeInvoicesCreateOtherPaymentsAddButton() {
    document.querySelector('.invoices-create__add-other-payments-btn')?.addEventListener('click', function (evt) {
        showSpinner();

        const data = {
            'payment_index': invoiceCreateOtherPaymentsIndex,
        };

        axios.post('/invoices/get/other-payments-list-on-create', data, {
            headers: {
                'Content-Type': 'application/json'
            }
        })
            .then(response => {
                const template = document.createElement('template');
                template.innerHTML = response.data;
                const element = template.content.firstChild;

                const paymentsList = document.querySelector('.invoices-create__other-payments-list');
                paymentsList.appendChild(element);
                initializeInvoicesCreateOtherPaymentsDeleteButtons();
                invoiceCreateOtherPaymentsIndex++;
            })
            .finally(function () {
                // Hide any loading spinner after the request is complete
                hideSpinner();
            });
    });
}

function initializeInvoicesCreateOtherPaymentsDeleteButtons() {
    document.querySelectorAll('.invoices-create__delete-other-payments-btn').forEach((btn) => {
        btn.addEventListener('click', function (evt) {
            evt.currentTarget.closest('.form__section').remove();
        })
    });
}

$('.invoices-create__payment-type-select').selectize({
    plugins: ["auto_position"],
    onChange(value) {
        handleInvoiceCreatePaymentType(value);
    }
});

function handleInvoiceCreatePaymentType(value) {
    const formGroup = document.querySelector('.invoices-create__terms-wrapper');
    const termsInput = formGroup.querySelector('.input');

    if (value == 1) {
        formGroup.style.display = '';
        termsInput.setAttribute('required', true);
    } else {
        formGroup.style.display = 'none';
        termsInput.removeAttribute('required');
    }
}
