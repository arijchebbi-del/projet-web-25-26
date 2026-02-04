const container=document.getElementById('authcontainer');
const registerBtn=document.getElementById('registerBtn');
const loginBtn=document.getElementById('loginBtn');
registerBtn.addEventListener('click',()=>{
    container.classList.add('active');
});
loginBtn.addEventListener('click',()=>{
    container.classList.remove('active');
});