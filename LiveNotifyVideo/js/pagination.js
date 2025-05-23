    document.addEventListener('DOMContentLoaded', function () {
    const content = document.querySelector('.content');
    const snappath = document.querySelector('#snappath');
    const itemsPerPage = 1; // set number of items per page
    let currentPage = 0;
    const items = Array.from(content.getElementsByTagName('li')).slice(0);

    function showPage(page) {
    const startIndex = page * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    items.forEach((item, index) => {
        item.classList.toggle('hidden', index < startIndex || index >= endIndex);
    });
    updateActiveButtonStates();
    }

    function createPageButtons() {
    const totalPages = Math.ceil(items.length / itemsPerPage);
    const paginationContainer = document.createElement('div');
    const paginationDiv = document.body.appendChild(paginationContainer);
    paginationContainer.classList.add('pagination');

    // Add page buttons
    for (let i = 0; i < totalPages; i++) {
        const pageButton = document.createElement('button');
        pageButton.textContent =  i + 1;
        pageButton.addEventListener('click', () => {
        currentPage = i;
        showPage(currentPage);
        updateActiveButtonStates();
        });

        snappath.appendChild(paginationContainer);
        paginationDiv.appendChild(pageButton);
        }
    }

    function updateActiveButtonStates() {
    const pageButtons = document.querySelectorAll('.pagination button');
    pageButtons.forEach((button, index) => {
        if (index === currentPage) {
        button.classList.add('active');
        } else {
        button.classList.remove('active');
        }
    });
    }

    createPageButtons(); // Call this function to create the page buttons initially
    showPage(currentPage);


    ///////////////////////////////////////////////////////////////////////////////////////

    const content1 = document.querySelector('.content1'); 
    const itemsPerPage1 = 5; // set number of items per page
    let currentPage1 = 0;
    // const items1 = Array.from(content1.getElementsByTagName('section')).slice(0);
    const items1 = Array.from(content1.getElementsByClassName('camera-img')).slice(0);

    function showPage1(page) {
    const startIndex1 = page * itemsPerPage1;
    const endIndex1 = startIndex1 + itemsPerPage1;
    items1.forEach((item, index) => {
        item.classList.toggle('hidden', index < startIndex1 || index >= endIndex1);
    });
    updateActiveButtonStates1();
    }

    function createPageButtons1() {
    const totalPages1 = Math.ceil(items1.length / itemsPerPage1);
    const paginationContainer1 = document.createElement('div');
    const paginationDiv1 = document.body.appendChild(paginationContainer1);
    paginationContainer1.classList.add('pagination1');

    // Add page buttons
    for (let i1 = 0; i1 < totalPages1; i1++) {
        const pageButton1 = document.createElement('button');
        pageButton1.textContent = i1 + 1;
        pageButton1.addEventListener('click', () => {
        currentPage1 = i1;
        showPage1(currentPage1);
        updateActiveButtonStates1();
        });

        content1.appendChild(paginationContainer1);
        paginationDiv1.appendChild(pageButton1);
        }
    }

    function updateActiveButtonStates1() {
    const pageButtons1 = document.querySelectorAll('.pagination1 button');
    pageButtons1.forEach((button, index) => {
        if (index === currentPage1) {
        button.classList.add('active');
        } else {
        button.classList.remove('active');
        }
    });
    }

    createPageButtons1(); // Call this function to create the page buttons initially
    showPage1(currentPage1);

})
