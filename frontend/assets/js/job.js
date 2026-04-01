let jobsTable = null;

function initSalaryRange() {
    const range = document.getElementById("range4");
    const output = document.getElementById("rangeValue");

    if (!range || !output) {
        return;
    }

    const renderValue = function () {
        output.textContent = range.value + " DT";
    };

    range.addEventListener("input", renderValue);
    range.addEventListener("change", renderValue);
    renderValue();
}

function collectJobFilters() {
    const selectedTypes = [];
    if (document.getElementById("fulltime") && document.getElementById("fulltime").checked) {
        selectedTypes.push("full-time");
    }
    if (document.getElementById("parttime") && document.getElementById("parttime").checked) {
        selectedTypes.push("part-time");
    }
    if (document.getElementById("internship") && document.getElementById("internship").checked) {
        selectedTypes.push("internship");
    }

    const selectedExperience = [];
    ["exp1", "exp2", "exp3", "exp4"].forEach(function (id) {
        const el = document.getElementById(id);
        if (el && el.checked) {
            selectedExperience.push(id);
        }
    });

    const remoteCheck = document.getElementById("remoteCheck");
    const salaryRange = document.getElementById("range4");
    const title = document.getElementById("jobTitle");
    const country = document.getElementById("country");
    const city = document.getElementById("city");

    return {
        jobType: selectedTypes.join(","),
        experience: selectedExperience.join(","),
        remote: remoteCheck && remoteCheck.checked ? "true" : "",
        maxSalary: salaryRange ? salaryRange.value : "",
        title: title ? title.value.trim() : "",
        country: country ? country.value : "",
        city: city ? city.value : "",
    };
}

function formatSalary(row) {
    if (row.salaryMin === null || row.salaryMax === null) {
        return "Negotiable";
    }

    const min = Number(row.salaryMin).toLocaleString();
    const max = Number(row.salaryMax).toLocaleString();
    return min + " - " + max + " " + (row.currency || "TND");
}

function formatRemote(row) {
    return row.remote ? "Remote" : "On-site";
}

function ensureDataTablesDependency() {
    return typeof window.jQuery !== "undefined"
        && typeof window.jQuery.fn !== "undefined"
        && typeof window.jQuery.fn.DataTable !== "undefined";
}

function initJobsDataTable() {
    const table = document.getElementById("jobsTable");
    if (!table || !ensureDataTablesDependency()) {
        return;
    }

    const apiBase = window.authApiBase || "http://127.0.0.1:8000/api";

    jobsTable = window.jQuery("#jobsTable").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: apiBase + "/jobs/datatable",
            type: "GET",
            xhrFields: {
                withCredentials: true,
            },
            data: function (request) {
                return Object.assign(request, collectJobFilters());
            },
        },
        columns: [
            { data: "title" },
            { data: "company" },
            { data: "type" },
            {
                data: null,
                render: function (_, __, row) {
                    return formatRemote(row);
                },
            },
            { data: "location" },
            {
                data: null,
                render: function (_, __, row) {
                    return formatSalary(row);
                },
            },
            {
                data: "experienceYears",
                render: function (value) {
                    if (value === null || typeof value === "undefined") {
                        return "-";
                    }
                    return value + " yrs";
                },
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (_, __, row) {
                    return '<a class="btn btn-sm btn-outline-primary" href="/frontend/pages/post.html?id=' + row.id + '">See more</a>';
                },
            },
        ],
        order: [[0, "asc"]],
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50],
    });
}

function resetJobFilters() {
    ["fulltime", "parttime", "internship", "remoteCheck", "exp1", "exp2", "exp3", "exp4"].forEach(function (id) {
        const el = document.getElementById(id);
        if (el) {
            el.checked = false;
        }
    });

    const range = document.getElementById("range4");
    if (range) {
        range.value = "2500";
    }

    const title = document.getElementById("jobTitle");
    if (title) {
        title.value = "";
    }

    const country = document.getElementById("country");
    if (country) {
        country.selectedIndex = 0;
    }

    const city = document.getElementById("city");
    if (city) {
        city.selectedIndex = 0;
    }

    initSalaryRange();
}

function bindJobFilters() {
    const searchBtn = document.getElementById("search");
    if (searchBtn) {
        searchBtn.addEventListener("click", function () {
            if (jobsTable) {
                jobsTable.ajax.reload();
            }
        });
    }

    const clearBtn = document.getElementById("clear");
    if (clearBtn) {
        clearBtn.addEventListener("click", function () {
            resetJobFilters();
            if (jobsTable) {
                jobsTable.ajax.reload();
            }
        });
    }
}

function initJobPage() {
    initSalaryRange();
    initJobsDataTable();
    bindJobFilters();
}
