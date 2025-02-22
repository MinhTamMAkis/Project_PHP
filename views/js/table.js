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

        // Thêm buttonupdate và buttondelete vào class
        this.buttonupdate = options.buttonupdate;
        this.buttondelete = options.buttondelete;

        this.init();
    }

    init() {
        this.loadData(this.currentPage);
        this.addEventListeners();
    }

    loadData(page) {
        const url = `${this.url}?page=${page}`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                const responseData = data[this.dataProperty] || [];
                this.currentPage = data[this.currentPageProperty] || 1;
                this.totalPages = data[this.totalPagesProperty] || 1;

                this.renderTable(responseData);
                this.updatePagination();
            })
            .catch(error => {
                console.error("Error fetching data:", error);
                alert('Error loading data');
            });
    }

    renderTable(data) {
        const table = document.querySelector(this.tableId);
        const tableHead = table.querySelector('thead tr');
        const tableBody = table.querySelector('tbody');
    
        tableBody.innerHTML = ''; // Xóa dữ liệu cũ
    
        // Kiểm tra nếu chưa có cột Action trong thead thì thêm vào
        if (!tableHead.querySelector('.a-column')) {
            const actionHeader = document.createElement('th');
            actionHeader.textContent = '     ';
            actionHeader.classList.add('a-column');
            tableHead.appendChild(actionHeader);
        }
    
        if (data.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="5">No users found</td></tr>';
        } else {
            data.forEach(item => {
                const row = document.createElement('tr');
                row.dataset.item = JSON.stringify(item); // Lưu toàn bộ dữ liệu vào dataset (để dùng sau này)
    
                this.columns.forEach(col => {
                    const cell = document.createElement('td');
    
                    if (col.key === 'image') {
                        const img = document.createElement('img');
                        img.src = item[col.key] ? `${_WEB_HOST}/uploads/${item[col.key]}` : 'default.jpg';
                        img.width = 100;
                        img.onerror = function () {
                            this.style.display = 'none';
                            const noImageText = document.createElement('span');
                            noImageText.textContent = 'Không tìm thấy ảnh';
                            cell.appendChild(noImageText);
                        };
                        cell.appendChild(img);
                    } else {
                        cell.textContent = item[col.key] || '--';
                    }
    
                    if (col.columnClass) {
                        cell.classList.add(col.columnClass);
                    }
                    if (col.checkboxId) {
                        const checkbox = document.getElementById(col.checkboxId);
                        if (checkbox && !checkbox.checked) {
                            cell.style.display = 'none';
                        }
                    }
                    row.appendChild(cell);
                });
    
                // Thêm cột chứa nút Cập Nhật và Xóa vào mỗi hàng dữ liệu
                const actionCell = document.createElement('td');
                actionCell.classList.add('a-column');
    
                if (this.buttonupdate) {
                    const updateBtn = document.createElement('button');
                    updateBtn.textContent = 'Cập Nhật';
                    updateBtn.classList.add('update-btn');
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
                tableBody.appendChild(row);
            });
        }
    }
    

    updatePagination() {
        const pageInfo = document.querySelector(`${this.paginationId} #pageInfo`);
        pageInfo.textContent = `Page ${this.currentPage} of ${this.totalPages}`;

        const prevPageBtn = document.querySelector(`${this.paginationId} #prevPage`);
        const nextPageBtn = document.querySelector(`${this.paginationId} #nextPage`);

        prevPageBtn.disabled = this.currentPage === 1;
        nextPageBtn.disabled = this.currentPage === this.totalPages;
    }

    addEventListeners() {
        const prevPageBtn = document.querySelector(`${this.paginationId} #prevPage`);
        const nextPageBtn = document.querySelector(`${this.paginationId} #nextPage`);

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

        this.columns.forEach(col => {
            const checkbox = document.getElementById(col.checkboxId);
            if (checkbox) {
                checkbox.addEventListener('change', () => {
                    this.toggleColumn(col.checkboxId, col.columnClass);
                });
            }
        });
    }

    toggleColumn(checkboxId, columnClass) {
        const checkbox = document.getElementById(checkboxId);
        const columns = document.querySelectorAll(`.${columnClass}`);

        columns.forEach(col => {
            col.style.display = checkbox.checked ? 'table-cell' : 'none';
        });
    }
}
