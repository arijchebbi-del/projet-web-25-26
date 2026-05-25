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
/* Type opportunity buttons to fix the active state */
const buttons = document.querySelectorAll('.cp-type-btn');

buttons.forEach(btn => {
    btn.addEventListener('click', () => {

        // remove active from all
        buttons.forEach(b => b.classList.remove('active'));

        // add active to clicked
        btn.classList.add('active');

        // check the radio inside
        btn.querySelector('input').checked = true;
    });
});

const jobTypeButtons = document.querySelectorAll('#jobTypeChips .cp-chip');

jobTypeButtons.forEach(btn => {
    btn.addEventListener('click', () => {

        // remove active from all
        jobTypeButtons.forEach(b => b.classList.remove('active'));

        // activate clicked one
        btn.classList.add('active');

    });
});
const workModeButtons = document.querySelectorAll('#workModeChips .cp-chip');

workModeButtons.forEach(btn => {
    btn.addEventListener('click', () => {

        // remove active from all
        workModeButtons.forEach(b => b.classList.remove('active'));

        // activate clicked one
        btn.classList.add('active');

    });
});