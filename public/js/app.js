$(document).ready(function() {
    // Theme toggle
    $('#theme-toggle').click(function(e) {
        e.preventDefault();
        const currentTheme = localStorage.getItem('theme') || 'light';
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        
        $('html').attr('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        
        $(this).find('i').toggleClass('bi-moon bi-sun');
    });

    // Load saved theme
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        $('html').attr('data-theme', savedTheme);
        $('#theme-toggle i').toggleClass('bi-moon bi-sun', savedTheme === 'dark');
    }

    // Auto logout timer
    let timeout;
    const sessionTimeout = {{ config('session.lifetime') * 60 * 1000 }}; // Convert to milliseconds

    function resetTimer() {
        clearTimeout(timeout);
        timeout = setTimeout(logout, sessionTimeout);
    }

    function logout() {
        $('#logout-form').submit();
    }

    // Reset timer on user activity
    $(document).on('mousemove keypress click', resetTimer);
    
    // Initial timer start
    resetTimer();

    // DataTables initialization
    $('.datatable').DataTable({
        responsive: true,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search...",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            paginate: {
                first: '<i class="bi bi-chevron-double-left"></i>',
                previous: '<i class="bi bi-chevron-left"></i>',
                next: '<i class="bi bi-chevron-right"></i>',
                last: '<i class="bi bi-chevron-double-right"></i>'
            }
        }
    });

    // Tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // File input enhancement
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });

    // Modal forms
    $('.modal-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const url = form.attr('action');
        
        $.ajax({
            url: url,
            method: form.attr('method'),
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    form.closest('.modal').modal('hide');
                    toastr.success(response.message);
                    setTimeout(() => location.reload(), 1000);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(key => {
                        toastr.error(errors[key][0]);
                    });
                } else {
                    toastr.error('An error occurred. Please try again.');
                }
            }
        });
    });

    // Delete confirmation
    $('.delete-btn').on('click', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');
        
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // Sidebar toggle for mobile
    $('[data-widget="pushmenu"]').on('click', function(e) {
        e.preventDefault();
        $('.main-sidebar').toggleClass('open');
    });
});