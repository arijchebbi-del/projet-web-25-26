/**
 * Global feed page dynamic data fetching script
 */
function addProfileCard(name, photo, description, skills = []) {
  const container = document.querySelector("#profiles");
  if (!container) return;

  const imgHtml = photo
    ? `<img src="${photo}" class="card-img-top" alt="${name}">`
    : `<div class="avatar-fallback">${name[0]}</div>`;
  const skillHtml = skills.map(s => `<span class="badge skill-badge">${s}</span>`).join(" ");
  const card = document.createElement("div");
  card.classList.add("profile-card", "card");
  card.innerHTML = `
    ${imgHtml}
    <div class="card-body">
      <h5 class="card-title">${name}</h5>
      <p class="card-text">
        ${description}<br>
        ${skills.length > 0 ? "Skills: " + skillHtml : ""}
      </p>
      <a href="/frontend/pages/profil.html?id=${name}" class="btn btn-primary w-100">View Profile</a>
    </div>`;
  container.appendChild(card);
}

function addJobCard(title, description, skills = [], link = "#", containerId = "jobs") {
  const container = document.getElementById(containerId);
  if (!container) return;

  const skillHtml = skills.map(s => `<span class="badge skill-badge">${s}</span>`).join(" ");

  const card = document.createElement("div");
  card.classList.add("card");
  card.innerHTML = `
    <div class="card-body">
      <h5 class="card-title">${title}</h5>
      <p class="card-text">${description}<br>${skills.length > 0 ? "Skills: " + skillHtml : ""}</p>
      <a href="${link}" class="btn btn-primary w-100">See more</a>
    </div>
  `;
  container.appendChild(card);
}

function addPostCard(content, authorName, authorAvatar, createdAt) {
  const container = document.getElementById("posts-container");
  if (!container) return;

  const imgHtml = authorAvatar
    ? `<img src="${authorAvatar}" class="post-avatar" alt="${authorName}" style="width:40px;height:40px;border-radius:50%;object-fit:cover;margin-right:10px;">`
    : `<div class="post-avatar-fallback" style="width:40px;height:40px;border-radius:50%;background:#007bff;color:white;display:flex;align-items:center;justify-content:center;margin-right:10px;">${authorName[0]}</div>`;

  const date = new Date(createdAt).toLocaleDateString();

  const card = document.createElement("div");
  card.classList.add("post-card", "card", "mb-3"); 
  card.innerHTML = `
    <div class="card-body">
        <div class="post-header d-flex align-items-center">
        ${imgHtml}
        <div>
            <strong class="text-primary">${authorName}</strong>
            <div class="text-muted small">${date}</div>
        </div>
        </div>
        <div class="post-content mt-3">
        ${content}
        </div>
    </div>
  `;
  container.appendChild(card);
}


async function loadData() {
  try {
    const userMeRes = await authApiFetch('/profile/me');
    let mySkills = [];
    if(userMeRes.ok) {
        const userMe = await userMeRes.json();
        mySkills = (userMe.data && userMe.data.skills) ? userMe.data.skills.map(s => s.name.toLowerCase()) : [];
    }

    // 1. Load users (Profiles)
    const usersRes = await authApiFetch('/users/datatable?length=10');
    if (usersRes.ok) {
      const usersData = await usersRes.json();
      const profilesContainer = document.getElementById("profiles");
      if (profilesContainer) profilesContainer.innerHTML = ''; // Clear dummies
      
      const users = usersData.data || [];
      if (users.length === 0) {
        if (profilesContainer) profilesContainer.innerHTML = '<p class="text-muted p-3">No profiles found.</p>';
      } else {
        
        let shuffled = users.sort(() => 0.5 - Math.random());
        let selected = shuffled.slice(0, 5);

        selected.forEach(u => {
           const name = u.firstName + " " + u.lastName;
           const desc = u.major || "Alumini / INSAT Student";
           const skills = []; 
           addProfileCard(name, u.avatarUrl, desc, skills);
        });
      }
    }

    // 2. Load jobs
    const jobsRes = await authApiFetch('/jobs/datatable?length=50');
    if (jobsRes.ok) {
      const jobsData = await jobsRes.json();
      const jobsContainer = document.getElementById("jobs");
      const internshipsContainer = document.getElementById("internships");
      
      if (jobsContainer) jobsContainer.innerHTML = '';
      if (internshipsContainer) internshipsContainer.innerHTML = '';
      
      const jobs = jobsData.data || [];

      // Sort jobs to prioritize ones matching user skills
      jobs.sort((a, b) => {
          let scoreA = 0; let scoreB = 0;
          let reqA = (a.requirements || "").toLowerCase();
          let reqB = (b.requirements || "").toLowerCase();
          
          mySkills.forEach(s => {
              if (reqA.includes(s)) scoreA++;
              if (reqB.includes(s)) scoreB++;
          });
          return scoreB - scoreA; // descending order of match
      });

      jobs.forEach(j => {
          const isInternship = j.type && j.type.toLowerCase() === 'internship';
          const containerId = isInternship ? "internships" : "jobs";
          const link = "/frontend/pages/post.html?id=" + j.id;
          
          let desc = j.company ? j.company + " - " : "";
          desc += j.location || "Location not specified";
          
          // Add a "Recommended Match" badge if there's overlap in skills
          let matchedSkills = [];
          if (mySkills.length > 0 && j.requirements) {
              let reqText = j.requirements.toLowerCase();
              matchedSkills = mySkills.filter(s => reqText.includes(s));
          }
          if (matchedSkills.length > 0) {
              desc += "<br><span class='text-success small'><i class='bi bi-star-fill'></i> Recommended Match</span>";
          }

          addJobCard(j.title, desc, matchedSkills, link, containerId);
      });
      
      if (jobsContainer && jobsContainer.children.length === 0) {
          jobsContainer.innerHTML = '<p class="text-muted p-3">No jobs found.</p>';
      }
      if (internshipsContainer && internshipsContainer.children.length === 0) {
          internshipsContainer.innerHTML = '<p class="text-muted p-3">No internships found.</p>';
      }
    }

     // 3. Load posts
    const postsRes = await authApiFetch('/posts');
    if (postsRes.ok) {
        const postsData = await postsRes.json();
        const postsContainer = document.getElementById("posts-container");
        if(postsContainer) {
            postsContainer.innerHTML = '';
            const posts = postsData.data || [];
            if(posts.length === 0) {
                postsContainer.innerHTML = '<p class="text-muted p-3">No posts found.</p>';
            } else {
                posts.forEach(p => {
                    const authorName = (p.author && p.author.firstName && p.author.lastName) 
                        ? p.author.firstName + " " + p.author.lastName 
                        : "Unknown";
                    addPostCard(p.content, authorName, p.author ? p.author.avatarUrl : null, p.createdAt);
                });
            }
        }
    }

  } catch (error) {
    console.error("Error loading feed data:", error);
  }
}

document.addEventListener("DOMContentLoaded", () => {
    loadData();
});
