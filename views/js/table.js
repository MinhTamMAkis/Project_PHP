// Lưu ý: Cần import file table.css để hiển thị đẹp
class TablePagination {
    constructor(options) {
        this.url = options.url;
        this.tableId = options.tableId;
        this.paginationId = options.paginationId;
        this.currentPage = options.currentPage || 1;
        this.totalPages = options.totalPages || 1;
        this.dataProperty = options.dataProperty || 'data';
        this.currentPageProperty = options.currentPageProperty || 'currentPage';
        this.totalPagesProperty = options.totalPagesProperty || 'totalPages';
        this.columns = options.columns || [];
        this.pageSize = options.pageSize || 5; // Mặc định 10 bản ghi mỗi trang
        this.limit = options.limit || 5; // Mặc định limit là 3
        this.buttonupdate = options.buttonupdate;
        this.buttondelete = options.buttondelete;
        this.sortOrder = 'asc';
        this.sortKey = 'id';
        this.searchQuery = options.searchQuery || '';
        this.isLoading = false;
        this.isSorting = false;
        if (options.enableSearch) {
            this.createSearchContainer();
            this.initSearch('searchInput1'); // Khởi tạo sự kiện tìm kiếm
        }
        this.init();
    }

    init() {
        this.createPageSizeSelect(); // Tạo và chèn dropdown pageSizeSelect
        this.createPaginationControls(); // Tạo và chèn các nút phân trang
        this.loadData(this.currentPage);
        this.addEventListeners();
    }
    createPaginationControls() {
        const paginationContainer = document.querySelector(this.paginationId);
        if (!paginationContainer) {
            console.error('Pagination container not found');
            return;
        }

        const paginationControls = document.createElement('div');
        paginationControls.innerHTML = `
            <button id="firstPage">First</button>
            <button id="prevPage">Previous</button>
            <span id="pageNumbers"></span>
            <button id="nextPage">Next</button>
            <button id="lastPage">Last</button>
        `;

        paginationContainer.appendChild(paginationControls);
    }
    createPageSizeSelect() {
        // Tạo container cho pageSizeSelect
        const pageSizeContainer = document.createElement('div');
        pageSizeContainer.classList.add('page-size-container');
    
        // Tạo dropdown chọn số lượng bản ghi hiển thị
        const pageSizeSelect = document.createElement('select');
        pageSizeSelect.id = 'pageSize';
        pageSizeSelect.innerHTML = `
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="50">50</option>
        `;
        pageSizeSelect.value = this.pageSize;
    
        // Thêm dropdown vào container
        pageSizeContainer.appendChild(pageSizeSelect);
    
        // Chèn container vào trước bảng
        const table = document.querySelector(this.tableId);
        table.parentNode.insertBefore(pageSizeContainer, table);
    
        // Thêm sự kiện change cho dropdown
        pageSizeSelect.addEventListener('change', (event) => {
            this.limit = parseInt(event.target.value, 10); // Cập nhật giá trị limit
            this.loadData(1); // Load lại dữ liệu từ trang 1
        });
    }
    async loadData(page) {
        if (this.isLoading) return;
        this.isLoading = true;
    
        if (this.abortController) {
            this.abortController.abort();
        }
        this.abortController = new AbortController();
    
        let url = `${this.url}${this.url.includes('?') ? '&' : '?'}page=${page}&limit=${this.limit}&sort=${this.sortKey}&order=${this.sortOrder}`;
        if (this.searchQuery) {
            url += `&searchkey=${encodeURIComponent(this.searchQuery)}`;
        }
    
        try {
            //Sử dụng async/await sẽ làm cho code dễ đọc hơn và tránh được callback hell.
            const response = await fetch(url, { signal: this.abortController.signal });
            const data = await response.json();
            this.isLoading = false;
            const responseData = data[this.dataProperty] || [];
            this.currentPage = data[this.currentPageProperty] || 1;
            this.totalPages = data[this.totalPagesProperty] || 1;
            this.renderTable(responseData);
            this.updatePagination();
        } catch (error) {
            if (error.name === 'AbortError') {
                console.log('Fetch aborted');
            } else {
                this.isLoading = false;
                console.error("Error fetching data:", error);
                alert('Error loading data');
            }
        }
    }
    

    renderTable(data) {
        const table = document.querySelector(this.tableId);
        const tableHead = table.querySelector('thead tr');
        const tableBody = table.querySelector('tbody');
        tableBody.innerHTML = ''; // Xóa dữ liệu cũ

        // Thêm tiêu đề cột
        tableHead.innerHTML = ''; // Xóa tiêu đề cũ
        this.columns.forEach(col => {
            const th = document.createElement('th');
            th.textContent = col.label || col.key;
            if (col.width) {
                th.style.width = col.width;
            }
            if (col.toggleable) {
                th.classList.add(col.columnClass); // Thêm class để có thể ẩn/hiện
                th.style.display = 'none'; // Ẩn tiêu đề mặc định
            }
            if (col.sortable) {
                const sortBtn = document.createElement('button');
                sortBtn.textContent = '↑↓';
                sortBtn.classList.add('sort-btn');
                sortBtn.dataset.key = col.key;
    
                // Thêm mũi tên để chỉ ra thứ tự sắp xếp
                if (this.sortKey === col.key) {
                    sortBtn.textContent = this.sortOrder === 'asc' ? '↑' : '↓';
                }
    
                th.appendChild(sortBtn);
            }
            tableHead.appendChild(th);
        });

        // Thêm dropdown menu
        if (this.buttonupdate || this.buttondelete) {
            const actionHeader = document.createElement('th');
            actionHeader.innerHTML = `
                <div class="dropdown">
                    <button class="dropbtn" onfocus="showSelect()" >Select</button>
                    <div class="dropdown-content-table"  tabindex="0">
                        ${this.columns
                            .filter(col => col.toggleable)
                            .map(col => `
                                <div style="display: flex; align-items: center;">
                                    <input type="checkbox" data-column="${col.columnClass}"> ${col.label}
                                </div>
                            `).join('')}
                    </div>
                </div>
            `;
            tableHead.appendChild(actionHeader);
            const dropbtn = document.querySelector('.dropbtn');
            const dropdownContent = document.querySelector('.dropdown-content-table');
        
            if (dropbtn && dropdownContent) {
                dropbtn.addEventListener('click', (event) => {
                    event.stopPropagation(); // Ngăn sự kiện click lan ra document
                    showSelect();
                });
        
                // Đóng dropdown khi click ra ngoài
                document.addEventListener('click', (event) => {
                    if (!dropdownContent.contains(event.target) && !dropbtn.contains(event.target)) {
                        hideSelect();
                    }
                });
            } else {
                console.error('Dropbtn or dropdown content not found in the DOM.');
            }

        
        }
        const fragment = document.createDocumentFragment();

        // Render dữ liệu vào bảng
        if (data.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="5">No users found</td></tr>';
        } else {
            data.forEach(item => {
                const row = document.createElement('tr');
                row.dataset.item = JSON.stringify(item);

                // Render các cột thông thường
                this.columns.forEach(col => {
                    const cell = document.createElement('td');
                    if (col.key === 'image') {
                        const img = document.createElement('img');
                        img.src = item[col.key] ? `${_WEB_HOST}/uploads/${item[col.key]}` : 'default.jpg';
                        img.width = 150;
                        img.style.verticalAlign = 'middle';
                        img.onerror = function () {
                            this.style.display = 'none';
                            const noImageText = document.createElement('span');
                            noImageText.textContent = 'Không tìm thấy ảnh';
                            cell.appendChild(noImageText);
                        };
                        cell.appendChild(img);
                    }else if (col.key === 'status') {
                        // Xử lý cột status
                        const statusValue = item[col.key];
                        if (statusValue === 0) {
                            cell.textContent = 'Đang hoạt động';
                            cell.style.color = 'green'; // Có thể thêm màu để phân biệt
                        } else if (statusValue === 1) {
                            cell.textContent = 'Chưa kích hoạt';
                            cell.style.color = 'red'; // Có thể thêm màu để phân biệt
                        } else {
                            cell.textContent = 'Không xác định';
                        }
                     }  else if (col.key === 'note_category') {
                        const maxLength = 250;
                        const cellContent = item[col.key] || '';
                        if (cellContent.length > maxLength) {
                            const truncatedContent = cellContent.substring(0, maxLength) + '...';
                            cell.textContent = truncatedContent;
                            cell.title = cellContent;
                        } else {
                            cell.textContent = cellContent;
                        }
                    }else {
                        const cellContent = item[col.key] || '<!--------!>';
                        if (col.textlimit) {
                            const maxLength = 250; // Độ dài tối đa
                            if (cellContent.length > maxLength) {
                                const truncatedContent = cellContent.substring(0, maxLength) + '...';
                                cell.textContent = truncatedContent;
                                cell.title = cellContent; // Thêm tooltip hiển thị toàn bộ nội dung
                            } else {
                                cell.textContent = cellContent;
                            }
                        } else {
                            cell.textContent = cellContent;
                        }
                    }
                    if (col.columnClass) {
                        cell.classList.add(col.columnClass);
                        if (col.toggleable) {
                            cell.style.display = 'none'; // Ẩn cột mặc định
                        }
                    }
                    row.appendChild(cell);
                });

                // Thêm cột "Action" và "Status" vào tbody
                if (this.buttonupdate || this.buttondelete) {
                    const actionCell = document.createElement('td');
                    actionCell.classList.add('a-column');
                    actionCell.style.width = '0%';
                    if (this.buttonupdate) {
                        const updateBtn = document.createElement('button');
                        updateBtn.textContent = 'Xem';
                        updateBtn.classList.add('view-btn');
                        updateBtn.dataset.id = item.id;
                        actionCell.appendChild(updateBtn);
                    }
                    if (this.buttondelete) {
                        const deleteBtn = document.createElement('button');
                        deleteBtn.textContent = 'Xóa';
                        deleteBtn.classList.add('delete-btn');
                        deleteBtn.dataset.id = item.id;
                        actionCell.appendChild(deleteBtn);
                    }
                    row.appendChild(actionCell);
                }
                fragment.appendChild(row);
            });
            tableBody.appendChild(fragment);

        }
    }

    updatePagination() {
    const pageNumbers = document.querySelector(`${this.paginationId} #pageNumbers`);
    const prevPageBtn = document.querySelector(`${this.paginationId} #prevPage`);
    const nextPageBtn = document.querySelector(`${this.paginationId} #nextPage`);
    const firstPageBtn = document.querySelector(`${this.paginationId} #firstPage`);
    const lastPageBtn = document.querySelector(`${this.paginationId} #lastPage`);

    if (!pageNumbers || !prevPageBtn || !nextPageBtn || !firstPageBtn || !lastPageBtn) {
        console.error('Pagination elements not found');
        return;
    }

    // Hiển thị tối đa 6 số trang
    let startPage = Math.max(1, this.currentPage - 3);
    let endPage = Math.min(this.totalPages, startPage + 5);

    if (endPage - startPage < 5) {
        startPage = Math.max(1, endPage - 5);
    }

    let pageNumbersHtml = '';

    
    // Thêm các nút số trang
    for (let i = startPage; i <= endPage; i++) {
        if (i === this.currentPage) {
            pageNumbersHtml += `<button class="page-number active" data-page="${i}">${i}</button>`;
        } else {
            pageNumbersHtml += `<button class="page-number" data-page="${i}">${i}</button>`;
        }
    }

    
    // Cập nhật nội dung của pageNumbers
    pageNumbers.innerHTML = pageNumbersHtml;

    // Thêm event listener cho các nút số trang
    pageNumbers.querySelectorAll('.page-number').forEach(button => {
        button.addEventListener('click', () => {
            const page = parseInt(button.dataset.page, 10);
            this.loadData(page);
        });
    });
}
    
    addEventListeners() {
        const prevPageBtn = document.querySelector(`${this.paginationId} #prevPage`);
        const nextPageBtn = document.querySelector(`${this.paginationId} #nextPage`);
        const firstPageBtn = document.querySelector(`${this.paginationId} #firstPage`);
        const lastPageBtn = document.querySelector(`${this.paginationId} #lastPage`);
        const pageSizeSelect = document.querySelector(`${this.paginationId} #pageSize`);
        const table = document.querySelector(this.tableId);

        if (prevPageBtn) {
            prevPageBtn.addEventListener('click', () => {
                if (this.currentPage > 1) {
                    this.loadData(this.currentPage - 1);
                }
            });
        }

        if (nextPageBtn) {
            nextPageBtn.addEventListener('click', () => {
                if (this.currentPage < this.totalPages) {
                    this.loadData(this.currentPage + 1);
                }
            });
        }

        if (firstPageBtn) {
            firstPageBtn.addEventListener('click', () => {
                this.loadData(1);
            });
        }

        if (lastPageBtn) {
            lastPageBtn.addEventListener('click', () => {
                this.loadData(this.totalPages);
            });
        }

        if (pageSizeSelect) {
            pageSizeSelect.addEventListener('change', (event) => {
                this.limit = parseInt(event.target.value, 10); // Cập nhật giá trị limit
                this.loadData(1); // Load lại dữ liệu từ trang 1
            });
        }
    
        console.log('Adding event listeners...'); // Debug

        // Sử dụng event delegation để gắn sự kiện
        document.addEventListener('change', (event) => {
            if (event.target.matches('.dropdown-content-table input[type="checkbox"]')) {
                console.log('Checkbox changed:', event.target.checked); // Debug
                const columnClass = event.target.dataset.column;
                const isVisible = event.target.checked;
                this.toggleColumn(columnClass, isVisible);
            }
        });
        // click vào nút sắp xếp
        table.addEventListener('click', (event) => {
            if (event.target.classList.contains('sort-btn')) {
                const key = event.target.dataset.key;
                this.sortData(key);
            }
        });
        
    }
    sortData(key) {
        const data = this.currentData; // Lưu trữ dữ liệu hiện tại
        data.sort((a, b) => {
            if (this.sortOrder === 'asc') {
                return a[key] > b[key] ? 1 : -1;
            } else {
                return a[key] < b[key] ? 1 : -1;
            }
        });
        this.renderTable(data);
    }
    
    
    createSearchContainer() {
        // Tạo HTML cho phần tìm kiếm
        const searchContainer = document.createElement('div');
        searchContainer.classList.add('search-container');
    
        const tooltip = document.createElement('div');
        tooltip.id = 'tooltip';
        tooltip.classList.add('tooltip');
        tooltip.textContent = 'Vui lòng nhập từ khóa bạn muốn tìm kiếm vào ô này.';
    
        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.id = 'searchInput1';
        searchInput.placeholder = 'Nhập từ khóa 1';
        searchInput.addEventListener('focus', () => this.showTooltip());
        searchInput.addEventListener('blur', () => this.hideTooltip());
    
        const searchBtn = document.createElement('button');
        searchBtn.id = 'searchBtn';
        searchBtn.textContent = 'Tìm kiếm';
        searchBtn.addEventListener('click', () => this.handleSearch(searchInput.value.trim()));
    
        // Gắn các phần tử vào container
        searchContainer.appendChild(tooltip);
        searchContainer.appendChild(searchInput);
        searchContainer.appendChild(searchBtn);
    
        // Gắn container vào trước bảng
        const table = document.querySelector(this.tableId);
        table.parentNode.insertBefore(searchContainer, table);
    }

    showTooltip() {
        const tooltip = document.getElementById('tooltip');
        if (tooltip) {
            tooltip.style.display = 'block';
        }
    }

    hideTooltip() {
        const tooltip = document.getElementById('tooltip');
        if (tooltip) {
            tooltip.style.display = 'none';
        }
    }

    initSearch(searchInputId) {
        const searchInput = document.getElementById(searchInputId);
        if (!searchInput) {
            console.error(`Search input with ID "${searchInputId}" not found.`);
            return;
        }

        let searchTimeout;

        // Gắn sự kiện input cho ô tìm kiếm
        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimeout); // Xóa timeout trước đó
            searchTimeout = setTimeout(() => {
                this.handleSearch(searchInput.value.trim()); // Gọi handleSearch sau khi người dùng dừng nhập
            }, 300); // Chờ 300ms sau khi dừng nhập
        });
        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }
    }

    handleSearch(searchKey) {
        if (searchKey !== this.searchQuery) { // Chỉ gọi API nếu searchKey thay đổi
            this.searchQuery = searchKey;
            this.loadData(1); // Load lại dữ liệu từ trang 1
        }
    }
    
    
    toggleColumn(columnClass, isVisible) {
        console.log(`Toggling column: ${columnClass}, Visible: ${isVisible}`); // Debug

        // Ẩn/hiện tiêu đề cột (th)
        const headerColumns = document.querySelectorAll(`th.${columnClass}`);
        console.log('Header columns:', headerColumns); // Debug
        headerColumns.forEach(th => {
            th.style.display = isVisible ? 'table-cell' : 'none';
        });

        // Ẩn/hiện dữ liệu cột (td)
        const dataColumns = document.querySelectorAll(`td.${columnClass}`);
        console.log('Data columns:', dataColumns); // Debug
        dataColumns.forEach(td => {
            td.style.display = isVisible ? 'table-cell' : 'none';
        });

        // Cập nhật width của "a-column"
        const aColumn = document.querySelectorAll(".a-column");
        let visibleColumns = document.querySelectorAll("th:not([style*='display: none'])").length;
        // aColumn.forEach(col => {
        //     col.style.width = visibleColumns > 4 ? "4%" : "7%";
        // });
    }
}

// Hàm hiển thị dropdown
function showSelect() {
    const dropdownContent = document.querySelector('.dropdown-content-table');
    if (dropdownContent) {
        dropdownContent.style.display = 'block';
    }
}

// Hàm ẩn dropdown
function hideSelect() {
    const dropdownContent = document.querySelector('.dropdown-content-table');
    if (dropdownContent) {
        dropdownContent.style.display = 'none';
    }
}
class SlugGenerator {
    constructor(inputSelector, outputSelector) {
        this.inputElement = document.querySelector(inputSelector);
        this.outputElement = document.querySelector(outputSelector);

        if (this.inputElement && this.outputElement) {
            this.init();
        }
    }

    removeVietnameseTones(str) {
        return str
            .normalize("NFD") // Tách dấu tiếng Việt
            .replace(/[\u0300-\u036f]/g, "") // Xóa dấu
            .replace(/đ/g, "d") // Chuyển đ → d
            .replace(/Đ/g, "D"); // Chuyển Đ → D
    }

    generateSlug(text) {
        return this.removeVietnameseTones(text)
            .toLowerCase()
            .trim()
            .replace(/[\s]+/g, "-") // Thay khoảng trắng thành "-"
            .replace(/[^a-z0-9\-]/g, ""); // Xóa ký tự đặc biệt
    }

    init() {
        this.inputElement.addEventListener("input", () => {
            this.outputElement.value = this.generateSlug(this.inputElement.value);
        });
    }
}


// Sử dụng class cho trường "name_category" và "slug_category"
