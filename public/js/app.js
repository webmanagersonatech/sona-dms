$(document).ready(function () {
    // ================= THEME TOGGLE =================
    $("#theme-toggle").click(function (e) {
        e.preventDefault();

        const currentTheme = localStorage.getItem("theme") || "light";
        const newTheme = currentTheme === "light" ? "dark" : "light";

        $("html").attr("data-theme", newTheme);
        localStorage.setItem("theme", newTheme);

        $(this).find("i").toggleClass("bi-moon bi-sun");
    });

    // Load saved theme
    const savedTheme = localStorage.getItem("theme");
    if (savedTheme) {
        $("html").attr("data-theme", savedTheme);
        $("#theme-toggle i").toggleClass(
            "bi-moon bi-sun",
            savedTheme === "dark",
        );
    }

    // ================= AUTO LOGOUT TIMER (FIXED 🔥) =================
    let timeout;
    const sessionTimeout = 30 * 60 * 1000; // 30 mins (safe fallback)

    function resetTimer() {
        clearTimeout(timeout);
        timeout = setTimeout(logout, sessionTimeout);
    }

    function logout() {
        $("#logout-form").submit();
    }

    // ✅ IMPORTANT FIX: removed "click"
    $(document).on("mousemove keypress keydown scroll", resetTimer);

    // Start timer
    resetTimer();

    // ================= DATATABLE =================
    if ($.fn.DataTable) {
        $(".datatable").DataTable({
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
                    last: '<i class="bi bi-chevron-double-right"></i>',
                },
            },
        });
    }

    // ================= TOOLTIP =================
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((el) => {
        new bootstrap.Tooltip(el);
    });

    // ================= POPOVER =================
    document.querySelectorAll('[data-bs-toggle="popover"]').forEach((el) => {
        new bootstrap.Popover(el);
    });

    // ================= FILE INPUT =================
    $(".custom-file-input").on("change", function () {
        let fileName = $(this).val().split("\\").pop();
        $(this).next(".custom-file-label").addClass("selected").html(fileName);
    });

    // ================= MODAL FORM AJAX =================
    $(".modal-form").on("submit", function (e) {
        e.preventDefault();

        const form = $(this);

        $.ajax({
            url: form.attr("action"),
            method: form.attr("method"),
            data: form.serialize(),

            success: function (response) {
                if (response.success) {
                    form.closest(".modal").modal("hide");

                    Swal.fire({
                        icon: "success",
                        title: response.message,
                        timer: 2000,
                        showConfirmButton: false,
                    });

                    setTimeout(() => location.reload(), 1000);
                }
            },

            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;

                    Object.keys(errors).forEach((key) => {
                        Swal.fire({
                            icon: "error",
                            title: errors[key][0],
                        });
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Something went wrong",
                    });
                }
            },
        });
    });

    // ================= DELETE CONFIRM =================
    $(".delete-btn").on("click", function (e) {
        e.preventDefault();

        const form = $(this).closest("form");

        Swal.fire({
            title: "Are you sure?",
            text: "You can't undo this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, delete!",
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // ================= SIDEBAR TOGGLE =================
    $("#toggleSidebar").on("click", function (e) {
        e.preventDefault();
        $("#sidebar").toggleClass("collapsed");
    });
});
