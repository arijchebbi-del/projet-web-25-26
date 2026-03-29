function configureSkills(...skills) 
{
  let accumulator="";
  skills.forEach(skill => {
    accumulator+=`<span class="badge skill-badge">${skill}</span>`;
  });
  return accumulator;
}


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

function addCardToPage(name, photo, promo,...skills) {
  const cardHtml = createCard(name, photo, promo,...skills);
  const container = document.querySelector("#search_results");
  if(container.innerHTML.includes("Your search results will appear here...")) {
    container.innerHTML = cardHtml;
  } else {
    container.innerHTML += cardHtml;
  }
}

