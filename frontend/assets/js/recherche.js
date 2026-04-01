let usersTable = null;

function collectSearchFilters() {
    const keyword = document.getElementById("search_keyword");
    const filterDropdown = document.getElementById("filter_dropdown");
    const filterValue = document.getElementById("filter_value");

    return {
        q: keyword ? keyword.value.trim() : "",
        filterBy: filterDropdown ? filterDropdown.value : "",
        filterValue: filterValue ? filterValue.value.trim() : "",
    };
}

function ensureDataTablesDependency() {
    return typeof window.jQuery !== "undefined"
        && typeof window.jQuery.fn !== "undefined"
        && typeof window.jQuery.fn.DataTable !== "undefined";
}

function formatUserName(row) {
    const fullName = [row.firstName || "", row.lastName || ""].join(" ").trim();
    return fullName || "Unknown";
}

function formatSkills(row) {
    if (!row.skills || !row.skills.length) {
        return "-";
    }
    return row.skills.join(", ");
}

function initResearchTable() {
    if (!ensureDataTablesDependency() || !document.getElementById("usersTable")) {
        return;
    }

    const apiBase = window.authApiBase || "http://127.0.0.1:8000/api";

    usersTable = window.jQuery("#usersTable").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: apiBase + "/users/datatable",
            type: "GET",
            xhrFields: {
                withCredentials: true,
            },
            data: function (request) {
                return Object.assign(request, collectSearchFilters());
            },
        },
        columns: [
            {
                data: null,
                render: function (_, __, row) {
                    return formatUserName(row);
                },
            },
            { data: "promoYear" },
            { data: "filiere" },
            { data: "parcours" },
            {
                data: null,
                render: function (_, __, row) {
                    return formatSkills(row);
                },
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (_, __, row) {
                    return '<a class="btn btn-sm btn-outline-primary" href="/frontend/pages/profil.html?id=' + row.id + '">View profile</a>';
                },
            },
        ],
        order: [[0, "asc"]],
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50],
    });
}

function bindResearchFilters() {
    const form = document.getElementById("researchForm");
    const searchButton = document.getElementById("search_button");
    const filterDropdown = document.getElementById("filter_dropdown");

    if (form) {
        form.addEventListener("submit", function (event) {
            event.preventDefault();
            if (usersTable) {
                usersTable.ajax.reload();
            }
        });
    }

    if (searchButton) {
        searchButton.addEventListener("click", function () {
            if (usersTable) {
                usersTable.ajax.reload();
            }
        });
    }

    if (filterDropdown) {
        filterDropdown.addEventListener("change", function () {
            const filterValue = document.getElementById("filter_value");
            if (!filterValue) return;

            if (filterDropdown.value === "promo") {
                filterValue.placeholder = "Example: 2026";
            } else if (filterDropdown.value === "skills") {
                filterValue.placeholder = "Example: PHP";
            } else if (filterDropdown.value === "filiere") {
                filterValue.placeholder = "Example: IIA";
            } else if (filterDropdown.value === "parcours") {
                filterValue.placeholder = "Example: Software Engineering";
            } else {
                filterValue.placeholder = "Filter value";
            }
        });
    }
}

function initResearchPage() {
    initResearchTable();
    bindResearchFilters();
}
