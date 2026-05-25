function addSkill(numberSkill) 
{
    
    const lastSkillInput = document.querySelector(`#skillInput${numberSkill - 1}`);
    const skillInputCreated = document.createElement("input");
    skillInputCreated.type = "text";
    skillInputCreated.classList.add("cp-input");
    skillInputCreated.id = `skillInput${numberSkill}`;
    skillInputCreated.placeholder = "Type a skill and press Enter…";

lastSkillInput.insertAdjacentElement("afterend", skillInputCreated);
}


const addSkillBtn = document.querySelector("#addSkillBtn");
let numberSkill = 2;

addSkillBtn.addEventListener("click", () => {   
    addSkill(numberSkill);
    numberSkill++;
});