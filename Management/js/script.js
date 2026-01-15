let projectModalInstance = null;
let locationModalInstance = null;
let cameraModalInstance = null;
let userModalInstance = null;

document.addEventListener('DOMContentLoaded', function() {
    const projModalEl = document.getElementById('projectModal');
    if (projModalEl) projectModalInstance = new bootstrap.Modal(projModalEl);
    
    const locModalEl = document.getElementById('locationModal');
    if (locModalEl) locationModalInstance = new bootstrap.Modal(locModalEl);

    const camModalEl = document.getElementById('cameraModal');
    if (camModalEl) cameraModalInstance = new bootstrap.Modal(camModalEl);

    const userModalEl = document.getElementById('userModal');
    if (userModalEl) userModalInstance = new bootstrap.Modal(userModalEl);

    initMultiFilter(); 
});

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('active');
}

function initMultiFilter() {
    const searchInput = document.getElementById('searchInput');
    const filterRole = document.getElementById('filterRole');
    const filterProject = document.getElementById('filterProject');
    const filterStatus = document.getElementById('filterStatus');
    const filterProvince = document.getElementById('filterProvince');

    const userTable = document.getElementById('userTableBody');
    const locationTable = document.getElementById('locationTableBody');
    const cameraTable = document.getElementById('cameraTableBody');
    const projectTable = document.querySelector('table tbody'); 

    const activeTableBody = userTable || locationTable || cameraTable || projectTable;
    const noDataRow = document.getElementById('noDataRow');
    const paginationContainer = document.querySelector('.pagination'); 

    if (!activeTableBody) return; 

    const rowsPerPage = 10;
    let currentPage = 1;
    let currentFilteredRows = [];

    const renderPage = (page) => {
        currentPage = page;
        const allRows = activeTableBody.querySelectorAll('tr.data-row');
        allRows.forEach(row => row.style.display = 'none');

        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        
        const rowsToShow = currentFilteredRows.slice(start, end);
        rowsToShow.forEach(row => row.style.display = '');

        renderPaginationControls();
    };

    const renderPaginationControls = () => {
        if (!paginationContainer) return;
        paginationContainer.innerHTML = '';

        const totalPages = Math.ceil(currentFilteredRows.length / rowsPerPage);

        if (totalPages <= 1) return;

        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
        prevLi.innerHTML = `<a class="page-link" href="#" onclick="return false;">&laquo;</a>`;
        prevLi.addEventListener('click', () => { if(currentPage > 1) renderPage(currentPage - 1); });
        paginationContainer.appendChild(prevLi);

        for (let i = 1; i <= totalPages; i++) {
            const li = document.createElement('li');
            li.className = `page-item ${i === currentPage ? 'active' : ''}`;
            const activeClass = i === currentPage ? 'bg-success border-success' : 'text-success';
            li.innerHTML = `<a class="page-link ${activeClass}" href="#" onclick="return false;">${i}</a>`;
            li.addEventListener('click', () => renderPage(i));
            paginationContainer.appendChild(li);
        }

        const nextLi = document.createElement('li');
        nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
        nextLi.innerHTML = `<a class="page-link" href="#" onclick="return false;">&raquo;</a>`;
        nextLi.addEventListener('click', () => { if(currentPage < totalPages) renderPage(currentPage + 1); });
        paginationContainer.appendChild(nextLi);
    };

    const runFilter = () => {
        const searchText = searchInput ? searchInput.value.toLowerCase() : '';
        
        const roleVal = filterRole ? filterRole.value : 'all';
        const projectVal = filterProject ? filterProject.value : 'all';
        const statusVal = filterStatus ? filterStatus.value : 'all';
        const provinceVal = filterProvince ? filterProvince.value : 'all';

        const allRows = activeTableBody.querySelectorAll('tr.data-row'); 
        
        currentFilteredRows = [];

        allRows.forEach(row => {
            const text = row.innerText.toLowerCase();
            
            const dRole = row.getAttribute('data-role');
            const dProject = row.getAttribute('data-project');
            const dStatus = row.getAttribute('data-status');
            const dProvince = row.getAttribute('data-province');

            const matchSearch = text.includes(searchText);
            const matchRole = (!filterRole || roleVal === 'all' || dRole === roleVal);
            const matchProject = (!filterProject || projectVal === 'all' || dProject === projectVal);
            const matchStatus = (!filterStatus || statusVal === 'all' || dStatus === statusVal);
            const matchProvince = (!filterProvince || provinceVal === 'all' || dProvince === provinceVal);

            if (matchSearch && matchRole && matchProject && matchStatus && matchProvince) {
                currentFilteredRows.push(row);
            } else {
                row.style.display = 'none';
            }
        });

        if (noDataRow) {
            if (currentFilteredRows.length === 0) noDataRow.classList.remove('d-none');
            else noDataRow.classList.add('d-none');
        }

        renderPage(1); 
    };

    if(searchInput) searchInput.addEventListener('keyup', runFilter);
    if(filterRole) filterRole.addEventListener('change', runFilter);
    if(filterProject) filterProject.addEventListener('change', runFilter);
    if(filterStatus) filterStatus.addEventListener('change', runFilter);
    if(filterProvince) filterProvince.addEventListener('change', runFilter);

    runFilter();
}

function openProjectModal() {
    if(!projectModalInstance) return;
    document.getElementById('projectForm').reset();
    document.getElementById('projectId').value = ''; 
    document.getElementById('action').value = 'create'; 
    document.getElementById('projectModalTitle').innerText = 'เพิ่มโครงการใหม่';
    projectModalInstance.show();
}

function openEditProject(btn) {
    if(!projectModalInstance) return;
    const id = btn.getAttribute('data-id');
    const name = btn.getAttribute('data-name');

    document.getElementById('projectId').value = id;
    document.getElementById('projectName').value = name;
    document.getElementById('action').value = 'update';
    document.getElementById('projectModalTitle').innerText = 'แก้ไขโครงการ';
    projectModalInstance.show();
}

function saveProject() {
    const form = document.getElementById('projectForm');
    const formData = new FormData(form);

    const name = document.getElementById('projectName').value;
    if(!name || name.trim() === "") {
        Swal.fire({ icon: 'warning', title: 'ข้อมูลไม่ครบถ้วน', text: 'กรุณาระบุชื่อโครงการ' });
        return;
    }

    fetch('/api/project_action.php', { // Use API URL
        method: 'POST',
        body: formData
    })
    .then(response => response.json()) // Expect JSON
    .then(data => {
        if (data.status === 'success') {
            projectModalInstance.hide();
            Swal.fire({ 
                icon: 'success', 
                title: 'บันทึกสำเร็จ!', 
                timer: 1500, 
                showConfirmButton: false 
            }).then(() => location.reload());
        } else {
            Swal.fire('เกิดข้อผิดพลาด', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'ไม่สามารถเชื่อมต่อกับ Server ได้', 'error');
    });
}

function openDeleteModal(id) {
    Swal.fire({
        title: 'ยืนยันการลบ?',
        text: "ข้อมูลนี้จะถูกลบและไม่สามารถกู้คืนได้!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'ลบข้อมูล'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('projectId', id);

            fetch('/api/project_action.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({ title: 'ลบข้อมูลสำเร็จ!', icon: 'success', timer: 1500, showConfirmButton: false })
                    .then(() => location.reload());
                } else {
                    Swal.fire('เกิดข้อผิดพลาด', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'ลบข้อมูลไม่สำเร็จ', 'error');
            });
        }
    });
}

function openAddModal() {
    if(!locationModalInstance) return;
    document.getElementById('locationForm').reset();
    document.getElementById('locationId').value = ''; 
    document.getElementById('action').value = 'create';
    document.getElementById('modalTitle').innerText = 'เพิ่มสถานที่ใหม่';
    locationModalInstance.show();
}

function openEditModal(btn) {
    if(!locationModalInstance) return;
    const id = btn.getAttribute('data-id');
    const address = btn.getAttribute('data-address');
    const province = btn.getAttribute('data-province'); 
    const project = btn.getAttribute('data-project'); 

    document.getElementById('locationId').value = id;
    document.getElementById('addressInput').value = address;
    document.getElementById('provinceInput').value = province;
    document.getElementById('projectInput').value = project;  
    
    document.getElementById('action').value = 'update';
    document.getElementById('modalTitle').innerText = 'แก้ไขข้อมูลสถานที่';
    locationModalInstance.show();
}

function saveLocation() {
    const form = document.getElementById('locationForm');
    const formData = new FormData(form);

    const address = document.getElementById('addressInput').value;
    const proj = document.getElementById('projectInput').value;
    const prov = document.getElementById('provinceInput').value;

    if(address.trim() === "" || proj === "" || prov === "") {
        Swal.fire({ icon: 'warning', title: 'ข้อมูลไม่ครบถ้วน', text: 'กรุณากรอกข้อมูลให้ครบทุกช่อง' });
        return;
    }

    fetch('/api/location_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            locationModalInstance.hide();
            Swal.fire({ icon: 'success', title: 'บันทึกสำเร็จ!', timer: 1500, showConfirmButton: false })
            .then(() => location.reload());
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(err => {
        console.error(err);
        Swal.fire('Error', 'Connect Server Failed', 'error');
    });
}

function openDeleteLocation(id) {
    Swal.fire({
        title: 'ยืนยันการลบ?',
        text: "คุณต้องการลบสถานที่นี้ใช่หรือไม่?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'ลบเลย'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('locationId', id);

            fetch('/api/location_action.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success'){
                    Swal.fire('ลบแล้ว!', 'ลบข้อมูลเรียบร้อย', 'success')
                    .then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            });
        }
    });
}

function openCameraModal() {
    if(!cameraModalInstance) return; 
    document.getElementById('cameraForm').reset();
    document.getElementById('cameraId').value = '';
    document.getElementById('action').value = 'create';
    document.getElementById('cameraModalTitle').innerText = 'เพิ่มกล้องใหม่';
    cameraModalInstance.show();
}

function openEditCamera(btn) {
    if(!cameraModalInstance) return;
    
    const id = btn.getAttribute('data-id');
    const name = btn.getAttribute('data-name');
    const url = btn.getAttribute('data-url');
    const project = btn.getAttribute('data-project');
    const status = btn.getAttribute('data-status');

    document.getElementById('cameraId').value = id;
    document.getElementById('cameraName').value = name;
    document.getElementById('cameraUrl').value = url;
    document.getElementById('cameraProject').value = project;

    const statusSelect = document.getElementById('isActive'); 
    if (statusSelect) {
        statusSelect.value = status; 
    }

    document.getElementById('action').value = 'update';
    document.getElementById('cameraModalTitle').innerText = 'แก้ไขข้อมูลกล้อง';
    cameraModalInstance.show();
}

function saveCamera() {
    const form = document.getElementById('cameraForm');
    const formData = new FormData(form);

    const name = document.getElementById('cameraName').value;
    const project = document.getElementById('cameraProject').value;

    if(name.trim() === "" || project === "") {
        Swal.fire('ข้อผิดพลาด', 'กรุณาระบุชื่อกล้องและเลือกโครงการ', 'warning');
        return;
    }

    fetch('/api/camera_action.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success'){
            cameraModalInstance.hide();
            Swal.fire({ icon: 'success', title: 'บันทึกสำเร็จ!', timer: 1500, showConfirmButton: false })
            .then(() => location.reload());
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(err => {
        console.error(err);
        Swal.fire('Error', 'Connect Server Failed', 'error');
    });
}

function openDeleteCamera(id) {
    Swal.fire({
        title: 'ยืนยันการลบ?',
        text: "คุณต้องการลบกล้องนี้ใช่หรือไม่?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'ลบเลย'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('cameraId', id);

            fetch('/api/camera_action.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success'){
                    Swal.fire('ลบแล้ว!', 'ลบข้อมูลเรียบร้อย', 'success')
                    .then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            });
        }
    });
}

function openUserModal() {
    if (!userModalInstance) return;

    document.getElementById('userForm').reset();
    document.getElementById('userId').value = '';
    document.getElementById('action').value = 'create';
    document.getElementById('userModalTitle').innerText = 'เพิ่มผู้ใช้งาน';
    document.getElementById('username').disabled = false;
    
    const passHint = document.getElementById('passwordHint');
    if(passHint) passHint.classList.add('d-none');

    userModalInstance.show();
}

function openEditUser(btn) {
    if (!userModalInstance) return;

    const id = btn.getAttribute('data-id');
    const username = btn.getAttribute('data-username');
    const firstname = btn.getAttribute('data-firstname');
    const lastname = btn.getAttribute('data-lastname');
    const phone = btn.getAttribute('data-phone');
    const role = btn.getAttribute('data-role');
    const project = btn.getAttribute('data-project');
    const status = btn.getAttribute('data-status');

    document.getElementById('userId').value = id;
    document.getElementById('username').value = username;
    document.getElementById('firstname').value = firstname;
    document.getElementById('lastname').value = lastname;
    document.getElementById('phone').value = phone;
    document.getElementById('roleId').value = role;
    document.getElementById('projectId').value = project;
    
    const activeEl = document.getElementById('isActive');
    if(activeEl) activeEl.value = status;

    document.getElementById('action').value = 'update';
    document.getElementById('userModalTitle').innerText = 'แก้ไขผู้ใช้งาน';
    document.getElementById('username').disabled = true;

    const passHint = document.getElementById('passwordHint');
    if(passHint) passHint.classList.remove('d-none');

    userModalInstance.show();
}

function saveUser() {
    const form = document.getElementById('userForm');
    const formData = new FormData(form);

    const username = document.getElementById('username').value.trim();
    const firstname = document.getElementById('firstname').value.trim();   
    const role = document.getElementById('roleId').value;

    if (username === "" || firstname === "" || role === "") {
        Swal.fire('ข้อผิดพลาด', 'กรุณากรอก Username, ชื่อจริง และเลือกบทบาท', 'warning');
        return;
    }
    
    if (document.getElementById('username').disabled) {
        formData.append('username', username);
    }

    fetch('/api/user_action.php', { 
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            userModalInstance.hide();
            Swal.fire({
                icon: 'success',
                title: 'บันทึกสำเร็จ!',
                timer: 1500,
                showConfirmButton: false
            }).then(() => location.reload());
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(err => {
        console.error(err);
        Swal.fire('Error', 'Connect Server Failed', 'error');
    });
}

function openDeleteUser(id) {
    Swal.fire({
        title: 'ยืนยันการลบ?',
        text: "คุณต้องการลบผู้ใช้งานนี้ใช่หรือไม่?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'ลบเลย'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('userId', id);

            fetch('/api/user_action.php', { 
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire('ลบแล้ว!', 'ลบข้อมูลเรียบร้อย', 'success')
                    .then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'Connect Server Failed', 'error');
            });
        }
    });
}

function toggleNotification(checkbox, name, userId) {
    const isChecked = checkbox.checked; // ส่ง true/false ไปให้ PHP API แปลงเอง
    
    const formData = new FormData();
    formData.append('action', 'toggle_notify');
    formData.append('userId', userId);
    formData.append('status', isChecked); 

    fetch('/api/alert_action.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            if (isChecked) {
                Toast.fire({
                    icon: 'success',
                    title: `เปิดแจ้งเตือน`,
                    text: `เปิดการแจ้งเตือนสำหรับคุณ ${name}`
                });
            } else {
                Toast.fire({
                    icon: 'secondary',
                    title: `ปิดแจ้งเตือน`,
                    text: `ปิดการแจ้งเตือนสำหรับคุณ ${name}`
                });
            }
        } else {
            checkbox.checked = !isChecked;
            Swal.fire('Error', 'บันทึกสถานะไม่สำเร็จ: ' + data.message, 'error');
        }
    })
    .catch(err => {
        console.error(err);
        checkbox.checked = !isChecked;
        Swal.fire('Error', 'ไม่สามารถเชื่อมต่อ Server ได้', 'error');
    });
}