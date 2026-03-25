(function() {
    'use strict';

    // ===== DOM Elements =====
    const elements = {
        sidebar: document.getElementById('sidebar'),
        toggleBtn: document.getElementById('toggleSidebar'),
        globalSearch: document.getElementById('globalSearch'),
        logoutForm: document.getElementById('logout-form'),
        html: document.documentElement
    };

    // ===== Sidebar Toggle =====
    if (elements.toggleBtn && elements.sidebar) {
        elements.toggleBtn.addEventListener('click', () => {
            elements.sidebar.classList.toggle('active');
        });

        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 1024) {
                if (!elements.sidebar.contains(e.target) && !elements.toggleBtn.contains(e.target)) {
                    elements.sidebar.classList.remove('active');
                }
            }
        });
    }

    // ===== Global Search =====
    if (elements.globalSearch) {
        let timeout;
        let searchController = new AbortController();

        elements.globalSearch.addEventListener('input', (e) => {
            clearTimeout(timeout);
            const query = e.target.value.trim();

            if (query.length < 2) return;

            timeout = setTimeout(() => {
                searchController.abort();
                searchController = new AbortController();

                fetch(`/search?q=${encodeURIComponent(query)}`, {
                    signal: searchController.signal
                }).catch(() => {});
            }, 500);
        });
    }

    // ===== SESSION TIMEOUT (Already using SweetAlert ✅) =====
    const sessionTimeout = 120 * 60 * 1000;
    let timeoutId;

    const resetSessionTimer = () => {
        clearTimeout(timeoutId);

        if (sessionTimeout > 0) {
            timeoutId = setTimeout(() => {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Session Timeout',
                        text: 'Your session is about to expire!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Stay Logged In',
                        cancelButtonText: 'Logout'
                    }).then((result) => {
                        if (!result.isConfirmed && elements.logoutForm) {
                            elements.logoutForm.submit();
                        } else {
                            resetSessionTimer();
                        }
                    });
                }
            }, sessionTimeout - 30000);
        }
    };

    ['mousedown', 'keydown', 'scroll', 'touchstart'].forEach(event => {
        document.addEventListener(event, resetSessionTimer, { passive: true });
    });

    resetSessionTimer();

    // ===== ✅ GLOBAL SWEETALERT FUNCTIONS =====

    // Success
    window.showSuccess = (message) => {
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: message
        });
    };

    // Error
    window.showError = (message) => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message
        });
    };

    // Warning
    window.showWarning = (message) => {
        Swal.fire({
            icon: 'warning',
            title: 'Warning',
            text: message
        });
    };

    // Toast (you already had this, improved)
    window.showToast = (message, type = 'success') => {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: type,
            title: message,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    };

    // ✅ DELETE CONFIRMATION (NEW)
    window.confirmDelete = (formId) => {
        Swal.fire({
            title: 'Are you sure?',
            text: "You can't undo this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    };

    // ===== DataTable =====
    if (document.querySelector('.datatable')) {
        const initDataTables = () => {
            if (typeof $.fn.DataTable === 'undefined') return;

            $('.datatable').each(function() {
                if (!$.fn.DataTable.isDataTable(this)) {
                    $(this).DataTable({
                        responsive: true,
                        pageLength: 10
                    });
                }
            });
        };

        document.readyState === 'loading'
            ? document.addEventListener('DOMContentLoaded', initDataTables)
            : initDataTables();
    }

})();