<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="/frontend/assets/js/auth.js"></script>
  <script>
    requireAuth();
  </script>
    <title>Alumini | Jobs</title>
    <link rel="stylesheet" href="/frontend/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/frontend/assets/css/footer_navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/frontend/assets/css/job.css">
</head>
<body>

<div id="navbar"></div>
<div class="page-content">
<div class="container">
  
  <div class="filter-header">
    <h2 class="filter">Filter</h2>
    <button type="button" id="clear" class="btn">Clear All</button>
  </div>

  <div class="slide_bar">
    <h5>Job Type</h5>
    <div class="form-checks-wrapper">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" id="fulltime">
        <label class="form-check-label" for="fulltime">Full-time</label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" id="parttime">
        <label class="form-check-label" for="parttime">Part-time</label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" id="internship">
        <label class="form-check-label" for="internship">Internship</label>
      </div>
    </div>
  </div>
<div class="slide_bar">
  <div class="form-check form-switch">
    <input class="form-check-input" type="checkbox" role="switch" id="remoteCheck">
    <label class="form-check-label" for="remoteCheck">Open to remote</label>
  </div>
</div>
  <div class="salary-section">
    <h5>Range Salary</h5>
    <input type="range" class="form-range" min="100" max="5000" value="2500" id="range4">
    <output id="rangeValue">2500 DT</output>
  </div>
  <div class="slide_bar">
    <h5>Experience</h5>
    <div class="form-checks-wrapper">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" id="exp1">
        <label class="form-check-label" for="exp1">Less than a year</label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" id="exp2">
        <label class="form-check-label" for="exp2">1-3 years</label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" id="exp3">
        <label class="form-check-label" for="exp3">3-5 years</label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" id="exp4">
        <label class="form-check-label" for="exp4">More than 5 years</label>
      </div>
    </div>
  </div>

</div>
<div class="right_container">
  <div class="search_up d-flex align-items-end gap-2 mb-4">

    <div class="mb-3 flex-grow-1">
      <input type="text" class="form-control" id="jobTitle" placeholder="Job Title">
    </div>

    <div class="mb-3">
     
      <select class="form-select" id="country">
        <option selected>Country</option>
        <option value="1">Tunisia</option>
        <option value="2">Morocco</option>
        <option value="3">France</option>
      </select>
    </div>

    <div class="mb-3">
     
      <select class="form-select" id="city">
        <option selected>City</option>
        <option value="1">Tunis</option>
        <option value="2">Sfax</option>
        <option value="3">Sousse</option>
      </select>
    </div>

    <div class="mb-3">
      <button class="btn btn-primary" style="height: 38px; margin-top: 28px;">Search</button>
    </div>
  </div>

  <div class="card job-post-card" id="jobDetailsContainer"><div class="card-body"><p>Loading job details...</p></div></div>
</div>
</div>
<div id="footer"></div>



<script>
async function loadJobDetails() {
    const params = new URLSearchParams(window.location.search);
    const jobId = params.get('id');
    const container = document.getElementById('jobDetailsContainer');

    if (!jobId) {
        container.innerHTML = '<div class="card-body"><p class="text-danger">No job ID provided.</p></div>';
        return;
    }

    try {
        const response = await authApiFetch('/jobs/' + jobId);
        const data = await response.json();

        if (data.ok && data.data) {
            const job = data.data;
            const salaryText = (job.salaryMin ? job.salaryMin : '') + (job.salaryMax ? ' - ' + job.salaryMax : '') + (job.salaryMin || job.salaryMax ? ' ' + job.currency : 'Unpaid / Negotiable');
            const expText = job.experienceYears || job.experienceYears === 0 ? job.experienceYears + ' Years' : 'Not specified';
            const locationText = job.location || (job.city && job.country ? job.city + ', ' + job.country : (job.country ? job.country : 'Location not specified'));

            let displayHtml = '<div class="card-body">';
            displayHtml += '<h2 class="job-title">' + escapeHtml(job.title) + '</h2>';
            displayHtml += '<h5 class="company">' + escapeHtml(job.company || 'Unknown Company') + ' &bull; ' + escapeHtml(locationText) + '</h5>';
            displayHtml += '<p class="job-meta">';
            displayHtml += '  <span class="job-type">' + escapeHtml(job.type || 'Full-time') + '</span> &bull; ';
            displayHtml += '  <span class="job-mode">' + (job.remote ? 'Remote' : 'On-site') + '</span> &bull; ';
            displayHtml += '  <span class="experience">' + escapeHtml(expText) + '</span> &bull; ';
            displayHtml += '  <span class="salary">' + escapeHtml(salaryText) + '</span>';
            displayHtml += '</p><hr>';
            displayHtml += '<h4>Description</h4>';
            displayHtml += '<p style="white-space: pre-wrap;">' + escapeHtml(job.description || 'No description provided.') + '</p>';
            if (job.responsibilities) {
                displayHtml += '<h4>Responsibilities</h4>';
                displayHtml += '<p style="white-space: pre-wrap;">' + escapeHtml(job.responsibilities) + '</p>';
            }
            if (job.requirements) {
                displayHtml += '<h4>Requirements</h4>';
                displayHtml += '<p style="white-space: pre-wrap;">' + escapeHtml(job.requirements) + '</p>';
            }
            displayHtml += '<hr><div class="job-actions">';
            displayHtml += '  <button class="btn btn-primary" onclick="alert(\'Application link not strictly available in DB yet! You would redirect to job.link\')">Apply Now</button>';
            displayHtml += '</div></div>';
            container.innerHTML = displayHtml;
        } else {
            container.innerHTML = '<div class="card-body"><p class="text-danger">Job not found.</p></div>';
        }
    } catch (error) {
        console.error('Error fetching job details', error);
        container.innerHTML = '<div class="card-body"><p class="text-danger">Failed to load job details.</p></div>';
    }
}

function escapeHtml(text) {
    if (!text) return '';
    return String(text).replace(/[&<>'"`]/g, 
        tag => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            "'": '&#39;',
            '"': '&quot;',
            '`': '&#x60;'
        }[tag] || tag)
    );
}

document.addEventListener('DOMContentLoaded', loadJobDetails);
</script>
</body>
<script src="/frontend/assets/js/bootstrap.bundle.min.js"></script>
<script src="/frontend/assets/js/root.js"></script>
<script src="/frontend/assets/js/job.js"></script>
<script>
    loadComponent("navbar", "/frontend/components/navbar.html", function() {
        initTheme();
        setActiveNav();
    });
    loadComponent("footer", "/frontend/components/footer.html");
  initSalaryRange();
</script>
  <script>
    function scrollMarquee(button, direction) {
      const container = button.parentElement;
      const marqueeWrapper = container.querySelector('.marquee-wrapper');
      const scrollAmount = 320; 
      
      let currentScroll = marqueeWrapper.scrollLeft || 0;
      let newScroll = currentScroll + (direction * scrollAmount);
      
      marqueeWrapper.scrollTo({
        left: newScroll,
        behavior: 'smooth'
      });
    }
  </script>

</html>




