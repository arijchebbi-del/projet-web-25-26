

<?php
session_start();

require_once '../../backend/config/ConnexionDB.php';


if (!isset($_SESSION['email'])) {
    header("Location: /frontend/pages/login.php");
    exit();
}

$query  = trim($_GET['query']  ?? '');
$filter = trim($_GET['filter'] ?? 'all');
$results = [];

if ($query !== '') {
    try {
        $conn = ConnexionDB::getInstance();
        $params = [':query' => '%' . $query . '%'];

        if ($filter === 'all') {
            $userSql = "
                SELECT
                    'user' AS result_type,
                    i.id AS result_id,
                    CONCAT(i.nom, ' ', i.prenom) AS title,
                    CASE
                        WHEN i.promo_year IS NULL THEN 'Promo —'
                        ELSE CONCAT('Promo ', i.promo_year)
                    END AS subtitle,
                    u.avatar_url AS avatar_url,
                    GROUP_CONCAT(DISTINCT s.name ORDER BY s.name SEPARATOR ',') AS skills
                FROM insatien i
                LEFT JOIN users       u  ON u.insatien_id = i.id
                LEFT JOIN parcours    p  ON p.id          = i.parcours_id
                LEFT JOIN filieres    f  ON f.id          = p.filiere_id
                LEFT JOIN user_skills us ON us.user_id    = u.id
                LEFT JOIN skills      s  ON s.id          = us.skill_id
                WHERE i.nom LIKE :query
                   OR i.prenom LIKE :query
                   OR i.email LIKE :query
                   OR p.name LIKE :query
                   OR f.name LIKE :query
                   OR s.name LIKE :query
                GROUP BY i.id, i.nom, i.prenom, i.promo_year, u.avatar_url
            ";

            $jobSql = "
                SELECT
                    'job' AS result_type,
                    j.id AS result_id,
                    j.titre AS title,
                    j.entreprise AS subtitle,
                    NULL AS avatar_url,
                    NULL AS skills
                FROM jobs j
                WHERE j.titre LIKE :query
                   OR j.entreprise LIKE :query
                   OR j.description LIKE :query
                   OR j.requirements LIKE :query
                   OR j.responsibilities LIKE :query
            ";

            $postSql = "
                SELECT
                    'post' AS result_type,
                    p.id AS result_id,
                    CONCAT(i.nom, ' ', i.prenom) AS title,
                    LEFT(p.content, 140) AS subtitle,
                    u.avatar_url AS avatar_url,
                    NULL AS skills
                FROM posts p
                INNER JOIN users u ON u.id = p.user_id
                INNER JOIN insatien i ON i.id = u.insatien_id
                WHERE p.content LIKE :query
            ";

            $sql = $userSql . " UNION ALL " . $jobSql . " UNION ALL " . $postSql . " ORDER BY result_type, title";
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Base SELECT — always fetch avatar + aggregated skills
            $sql = "
                SELECT
                    i.id,
                    i.nom,
                    i.prenom,
                    i.promo_year,
                    p.name          AS parcours_name,
                    f.name          AS filiere_name,
                    u.avatar_url,
                    GROUP_CONCAT(DISTINCT s.name ORDER BY s.name SEPARATOR ',') AS skills
                FROM insatien i
                LEFT JOIN users      u  ON u.insatien_id = i.id
                LEFT JOIN parcours   p  ON p.id          = i.parcours_id
                LEFT JOIN filieres   f  ON f.id          = p.filiere_id
                LEFT JOIN user_skills us ON us.user_id   = u.id
                LEFT JOIN skills     s  ON s.id          = us.skill_id
            ";

            // WHERE clause depends on selected filter
            switch ($filter) {
                case 'promo':
                    if (ctype_digit($query)) {
                        $sql .= " WHERE i.promo_year = :promo_year";
                    } else {
                        $sql .= " WHERE i.nom LIKE :query
                                     OR i.prenom LIKE :query
                                     OR i.email LIKE :query";
                    }
                    break;
                case 'skills':
                    $sql .= " WHERE s.name LIKE :query";
                    break;
                case 'filiere':
                    $sql .= " WHERE f.name LIKE :query";
                    break;
                case 'parcours':
                    $sql .= " WHERE p.name LIKE :query";
                    break;
                default: // search by name
                    $sql .= " WHERE i.nom    LIKE :query
                                 OR i.prenom LIKE :query
                                 OR i.email  LIKE :query";
                    break;
            }

            $sql .= " GROUP BY i.id, i.nom, i.prenom, i.promo_year,
                                p.name, f.name, u.avatar_url";
            $stmt = $conn->prepare($sql);
            if ($filter === 'promo' && ctype_digit($query)) {
                $params[':promo_year'] = (int)$query;
            }
            $stmt->execute($params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Alumini | Research</title>
    <link rel="stylesheet" href="/frontend/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/frontend/assets/css/recherche.css">
    <link rel="stylesheet" href="/frontend/assets/css/footer_navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<script>
function loadComponent(id, file, callback) {
    fetch(file)
        .then(res => res.text())
        .then(data => {
            document.getElementById(id).innerHTML = data;
            if (callback) callback();
        })
        .catch(err => console.error("Error loading:", file, err));
}
loadComponent("navbar", "/frontend/components/navbar.php", function () {
    initTheme();
    setActiveNav();
});
loadComponent("footer", "/frontend/components/footer.php");

function initTheme() {
    const btn   = document.getElementById("themeBtn");
    const saved = localStorage.getItem("theme");
    if (saved === "dark") {
        document.documentElement.setAttribute("data-theme", "dark");
        if (btn) btn.innerHTML = '<i class="fa-solid fa-moon"></i>';
    }
    if (btn) btn.onclick = toggleTheme;
}
function toggleTheme() {
    const root = document.documentElement;
    const btn  = document.getElementById("themeBtn");
    if (root.getAttribute("data-theme") === "dark") {
        root.removeAttribute("data-theme");
        localStorage.setItem("theme", "light");
        if (btn) btn.innerHTML = '<i class="fa-regular fa-moon"></i>';
    } else {
        root.setAttribute("data-theme", "dark");
        localStorage.setItem("theme", "dark");
        if (btn) btn.innerHTML = '<i class="fa-solid fa-moon"></i>';
    }
}
</script>

<div id="navbar"></div>

<div class="container mt-4">

    <!-- Search form — submits GET to self -->
    <form action="/frontend/pages/recherche.php" method="GET">
        <div class="row justify-content-center align-items-center g-2">

            <div class="col-12 col-md-6 col-lg-7">
                <input
                    type="text"
                    name="query"
                    class="form-control"
                    placeholder="Enter keywords or name…"
                    value="<?= htmlspecialchars($query) ?>"
                >
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <select name="filter" class="form-select w-100" id="filter_dropdown">
                    <option value="all"     <?= $filter === 'all'     ? 'selected' : '' ?>>Filter By</option>
                    <option value="promo"   <?= $filter === 'promo'   ? 'selected' : '' ?>>Promo</option>
                    <option value="skills"  <?= $filter === 'skills'  ? 'selected' : '' ?>>Skills</option>
                    <option value="filiere" <?= $filter === 'filiere' ? 'selected' : '' ?>>Filière</option>
                    <option value="parcours"<?= $filter === 'parcours'? 'selected' : '' ?>>Parcours</option>
                </select>
            </div>

            <div class="col-6 col-md-3 col-lg-1">
                <button type="submit" class="btn btn-light w-100">Search</button>
            </div>

        </div>
    </form>

    <!-- Results -->
    <div class="row justify-content-center mt-4">
        <div class="col-sm-12 col-md-8 col-lg-6">
            <div class="bg-white p-4 rounded shadow" id="search_results">
                <p class="text-muted">Your search results will appear here...</p>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="row justify-content-center mt-3">
        <div class="col-auto">
            <nav aria-label="Search pagination">
                <ul class="pagination" id="pagination">
                    <li class="page-item" id="prev-btn">
                        <a class="page-link" href="#" aria-label="Previous">&laquo;</a>
                    </li>
                    <div id="pagination_numbers" class="d-flex"></div>
                    <li class="page-item" id="next-btn">
                        <a class="page-link" href="#" aria-label="Next">&raquo;</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

</div>

<div id="footer"></div>

<script>
// ── Data from PHP ─────────────────────────────────────────
const RESULTS = <?= json_encode($results) ?>;
const QUERY   = <?= json_encode($query)   ?>;

// ── Pagination state ──────────────────────────────────────
const PER_PAGE   = 5;
const TOTAL_PAGES = Math.ceil(RESULTS.length / PER_PAGE);
let   currentPage = 1;

// ── Boot ─────────────────────────────────────────────────
document.addEventListener("DOMContentLoaded", function () {
    if (QUERY === '') return; // nothing searched yet

    if (RESULTS.length === 0) {
        document.querySelector("#search_results").innerHTML =
            '<p class="text-muted">No results found for <strong>' +
            escapeHtml(QUERY) + '</strong>.</p>';
        document.querySelector("#pagination").style.display = "none";
        return;
    }

    buildPagination();
    showPage(1);

    document.querySelector("#prev-btn").addEventListener("click", function (e) {
        e.preventDefault();
        if (currentPage > 1) showPage(currentPage - 1);
    });

    document.querySelector("#next-btn").addEventListener("click", function (e) {
        e.preventDefault();
        if (currentPage < TOTAL_PAGES) showPage(currentPage + 1);
    });
});

// ── Show one page of results ──────────────────────────────
function showPage(page) {
    currentPage = page;
    const container = document.querySelector("#search_results");
    container.innerHTML = "";

    const start = (page - 1) * PER_PAGE;
    const end   = Math.min(start + PER_PAGE, RESULTS.length);

    for (let i = start; i < end; i++) {
        const r = RESULTS[i];
        addCardToPage(r);
    }

    // Update active page button
    document.querySelectorAll("#pagination_numbers .page-item").forEach(li => {
        li.classList.toggle("active", parseInt(li.dataset.page) === page);
    });

    // Disable prev/next at boundaries
    document.querySelector("#prev-btn").classList.toggle("disabled", page === 1);
    document.querySelector("#next-btn").classList.toggle("disabled", page === TOTAL_PAGES);
}

// ── Build page number buttons ─────────────────────────────
function buildPagination() {
    const container = document.querySelector("#pagination_numbers");
    container.innerHTML = "";
    for (let k = 1; k <= TOTAL_PAGES; k++) {
        const li = document.createElement("li");
        li.className    = "page-item";
        li.dataset.page = k;
        li.innerHTML    = `<a class="page-link" href="#">${k}</a>`;
        li.addEventListener("click", function (e) {
            e.preventDefault();
            showPage(k);
        });
        container.appendChild(li);
    }
}

function escapeHtml(str) {
    return str.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;");
}
</script>

<script src="/frontend/assets/js/bootstrap.bundle.min.js"></script>
<script src="/frontend/assets/js/root.js"></script>
<script src="/frontend/assets/js/recherche.js"></script>
</body>
</html>
