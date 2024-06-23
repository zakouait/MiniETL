// Existing JavaScript code
let headers = []; // Declare headers as a global variable

window.onload = function() {
    document.getElementById('fileInput').addEventListener('change', readFile);
}

function readFile(event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(e) {
        const content = e.target.result;
        const lines = content.split('\n');
        const table = document.createElement('table');

        // Create checkboxes for column selection
        const headerRow = document.createElement('tr');
        headers = lines[0].split('|'); // Assign headers from the first line
        headers.forEach(header => {
            const th = document.createElement('th');
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.value = header;
            checkbox.checked = true;
            th.appendChild(checkbox);
            th.appendChild(document.createTextNode(header));
            headerRow.appendChild(th);
        });
        table.appendChild(headerRow);

        // Create table rows
        lines.slice(1).forEach(line => {
            const row = document.createElement('tr');
            const cells = line.split('|');
            cells.forEach(cell => {
                const td = document.createElement('td');
                td.textContent = cell.trim(); // Sanitize data by removing leading and trailing whitespace
                row.appendChild(td);
            });
            table.appendChild(row);
        });

        document.getElementById('tableContainer').innerHTML = '';
        document.getElementById('tableContainer').appendChild(table);
    };
    reader.readAsText(file);
}

// Get the modal
var modal = document.getElementById("errorModal");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

// Function to display the error modal
function showErrorModal(errorMessage) {
    var errorMessageElement = document.getElementById("errorMessage");
    errorMessageElement.textContent = errorMessage;
    modal.style.display = "block";
}



// Get all dropdown toggles
const dropdownToggles = document.querySelectorAll('.dropdown-toggle');

// Add click event listener to each dropdown toggle
dropdownToggles.forEach(toggle => {
    toggle.addEventListener('click', function() {
        // Toggle the display of the dropdown menu
        this.nextElementSibling.classList.toggle('show');
    });
});

// Close dropdown menu when clicking outside
window.addEventListener('click', function(event) {
    dropdownToggles.forEach(toggle => {
        const dropdownMenu = toggle.nextElementSibling;
        if (!event.target.matches('.dropdown-toggle') && !dropdownMenu.contains(event.target)) {
            dropdownMenu.classList.remove('show');
        }
    });
});


