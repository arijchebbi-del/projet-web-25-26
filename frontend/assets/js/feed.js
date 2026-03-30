
/**
@param {string} name - Snom insatien
@param {string|null} photo - URL of the photo. if it's null ywari l first letter fallback kima l watsp
@param {string} description - 
@param {string[]} skills - skills*/

/*func to add insatien profile to profile marquee*/
function addProfileCard(name, photo, description, skills = []) {
    /*ylwaej all container snn yekhedh loul par def*/
  const container = document.querySelector(".marquee-track.profiles") || document.querySelector(".marquee-track"); 
/*ychouf ken fama url img wale snn yhot lharf loul taa nom insatien*/
  const imgHtml = photo
    ? `<img src="${photo}" class="card-img-top" alt="${name}">`
    : `<div class="avatar-fallback">${name[0]}</div>`;
    /*yloopi aal skills +span badge skill-badge l kol skill w yjoini binethom b espace*/
  const skillHtml = skills.map(s => `<span class="badge skill-badge">${s}</span>`).join(" ");
  /*ycreati lcard*/
  const card = document.createElement("div");
  card.classList.add("profile-card", "card");
  /*yhot lcontenu html teeha w yzidha ll container*/
  card.innerHTML = `
    ${imgHtml}
    <div class="card-body">
      <h5 class="card-title">${name}</h5>
      <p class="card-text">
        ${description}<br>
        Skills: ${skillHtml}
      </p>
      <a href="/frontend/pages/profil.html" class="btn btn-primary w-100">View Profile</a>
    </div>`;
  container.appendChild(card);
}
addProfileCard("arouja","lmamrouja.jpg","ya nina ya khoulouud",["psy comme slimen","Python","JavaScript"]);

/**
add job
@param {string} title - Job title
@param {string} description 
@param {string[]} skills 
@param {string} link - Link to the job post
@param {string} containerSelector - Selector for the marquee track to add to
 */
function addJobCard(title, description, skills = [], link = "#", containerSelector = ".marquee-track.jobs") {
  const container = document.querySelector(containerSelector) || document.querySelector(".marquee-track");
  const skillHtml = skills.map(s => `<span class="badge skill-badge">${s}</span>`).join(" ");

  const card = document.createElement("div");
  card.classList.add("card");
  card.innerHTML = `
    <div class="card-body">
      <h5 class="card-title">${title}</h5>
      <p class="card-text">${description}<br>Skills: ${skillHtml}</p>
      <a href="${link}" class="btn btn-primary">See more</a>
    </div>
  `;
  container.appendChild(card);
}

/**
 * Scroll function for marquees
 * @param {HTMLElement} button 
 * @param {number} direction 
 */
function scrollMarquee(button, direction) {
  const container = button.parentElement;
  const wrapper = container.querySelector(".marquee-wrapper");
  const scrollAmount = 320;

  wrapper.scrollTo({
    left: wrapper.scrollLeft + (direction * scrollAmount),
    behavior: "smooth"
  });
}


addJobCard("Software Engineer", "Join our team", ["HTML","CSS","JS"], "post.html", ".marquee-track.jobs");

