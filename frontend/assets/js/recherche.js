/**
 * 
 * @param  {...any} skills 
 * @returns string accumulator : a string of html spans with the skill names
 * 
 * This function takes a variable number of skill arguments and generates a string of HTML spans for each skill. Each span is styled with the "badge" and "skill-badge" classes, and contains the name of the skill. The resulting string can be used to display the skills in a visually appealing way on a webpage. 
 */

function configureSkills(...skills) 
{
  let accumulator="";
  skills.forEach(skill => {
    accumulator+=`<span class="badge skill-badge">${skill}</span>`;
  });
  return accumulator;
}



/**
 * 
 * @param {string} name  : name of the student
 * @param {photo} photo  : url of the student's photo, if null a fallback with the first letter of the name will be used
 * @param {string} promo  : promotion of the student
 * @param  {...string} skills :  skills of the student
 * @returns string : a string of html representing a card with the student's information and skills
 * 
 * This function creates an HTML card for a student, displaying their name, photo (or a fallback if no photo is provided), promotion, and skills. The card is structured as a link and includes styling classes for layout and appearance. The skills are displayed using the configureSkills function to generate the appropriate HTML spans.
 */
function createCard(name, photo, promo,...skills) {
  const imgHtml = photo
    ? `<img class="avatar" src="${photo}" alt="${name}">`
    : `<div class="avatar-fallback">${name[0]}</div>`;

  return `
    <a href="" class="result-card card border rounded-3 p-3 mb-2 w-100 d-block">
      <div class="d-flex align-items-center gap-3">
        ${imgHtml}
        <div class="flex-grow-1">
          <p class="fw-medium mb-0">${name}</p>
          <p class="text-muted mb-1">${promo}</p>`
          +configureSkills(...skills)+
        `  
      </div>
        <span class="arrow-icon ms-auto">›</span>
      </div>
    </a>
  `;
}
/**
 * Adds a student card to the page
 * @param {string} name  : name of the student
 * @param {photo} photo  : url of the student's photo, if null a fallback with the first letter of the name will be used
 * @param {string} promo  : promotion of the student
 * @param  {...string} skills :  skills of the student
 * @returns void
 * This function generates an HTML card for a student using the createCard function and appends it to the search results container on the page. If the container currently displays the default message ("Your search results will appear here..."), it replaces that message with the new card. Otherwise, it adds the new card to the existing content in the container.
 */

function addCardToPage(name, photo, promo,...skills) {
  const cardHtml = createCard(name, photo, promo,...skills);
  const container = document.querySelector("#search_results");
  if(container.innerHTML.includes("Your search results will appear here...")) {
    container.innerHTML = cardHtml;
  } else {
    container.innerHTML += cardHtml;
  }
}

