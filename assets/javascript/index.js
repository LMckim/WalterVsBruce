window.onload = function(){

    // handles showing and hiding of login form
    var adminBtn = document.getElementsByClassName('admin-btn')[0];
    adminBtn.addEventListener('click',function(){
        let adminForm = document.getElementById('admin-form');

        if(adminForm.style.getPropertyValue('display') == 'block'){
            adminForm.style.setProperty('display','none');
        }else{
            adminForm.style.setProperty('display','block');
        }
    });

}