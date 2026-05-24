<?php
session_start();

require_once '../../backend/config/ConnexionDB.php';
require_once '../../backend/repository/jobRepository.php';
require_once '../../backend/service/jobService.php';

$repo = new jobRepository();
$service = new jobService();

$filters = [
    'title' => $_GET['jobTitle'] ?? '',
    'country' => $_GET['country'] ?? '',
    'city' => $_GET['city'] ?? '',
    'type' => $_GET['type'] ?? [],
    'remote' => $_GET['remote'] ?? null,
    'experience' => $_GET['experience'] ?? [],
    'salary' => $_GET['salary'] ?? null
];

list($sql, $params) = $service->buildQuery($filters);
$jobs = $repo->findFilteredJobs($sql, $params);
include '../../backend/views/jobs_list.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<title>Jobs</title>

<link rel="stylesheet" href="/frontend/assets/css/bootstrap.min.css">
<link rel="stylesheet" href="/frontend/assets/css/job.css">
</head>

<body>

<div class="container d-flex gap-4 mt-4">

<form method="GET" style="width:300px;">

<h5>Filters</h5>

<input type="text" name="jobTitle" class="form-control mb-2" placeholder="Job title">

<h6>Type</h6>
<input type="checkbox" name="type[]" value="full-time"> Full-time<br>
<input type="checkbox" name="type[]" value="part-time"> Part-time<br>
<input type="checkbox" name="type[]" value="internship"> Internship<br>

<br>

<input type="checkbox" name="remote" value="1"> Remote

<br><br>

<h6>Experience</h6>
<input type="checkbox" name="experience[]" value="0-1"> <1 year<br>
<input type="checkbox" name="experience[]" value="1-3"> 1-3<br>
<input type="checkbox" name="experience[]" value="3-5"> 3-5<br>

<br>

<input type="range" name="salary" min="100" max="5000">

<br><br>

<button class="btn btn-primary w-100">Apply</button>

</form>

<div class="row flex-grow-1">

<?php include '../../backend/views/jobs_list.php'; ?>

</div>

</div>

</body>
</html>