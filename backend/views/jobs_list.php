<?php foreach ($jobs as $job): ?>

<div class="col-md-6 mb-3">

    <div class="card p-3">

        <span class="badge bg-primary">
            <?= htmlspecialchars($job->job_type) ?>
        </span>

        <h5 class="mt-2">
            <?= htmlspecialchars($job->titre) ?>
        </h5>

        <p><?= htmlspecialchars(substr($job->description, 0, 120)) ?>...</p>

        <p>
           <?= htmlspecialchars($job->city . ', ' . $job->country) ?>
        </p>

        <a href="job.php?id=<?= $job->id ?>" class="btn btn-primary w-100">
            View Job
        </a>

    </div>

</div>

<?php endforeach; ?>