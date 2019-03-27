var url = '/index.php?';

window.onload = function(){

    // handles showing and hiding of admin forms
    var adminBtn = document.getElementsByClassName('admin-btn')[0];
    adminBtn.addEventListener('click',function(element){
        
        let overlay = document.getElementById('admin-overlay');
        let form = document.getElementById('admin-form');
        let display = overlay.style.getPropertyValue('display');
        if(display == '' || display == 'none')
        {
            overlay.style.setProperty('display','block');
            form.style.setProperty('display','flex');

            // once visible if clicked outside of will close
            form.addEventListener('click',function(){
                
                overlay.style.setProperty('display','none');
                form.style.setProperty('display','none');
            });
            let admin = document.getElementById('admin-options');
            if(admin){
                admin.addEventListener('click',function(e){
                    e.stopPropagation();
                });
            }else{
                let login = document.getElementById('login-form');
                if(login){
                    login.addEventListener('click',function(e){
                        e.stopPropagation();
                    });
                }
            }
        }
    });
    // query DB for all comments related to each image
    var imageBtn = document.getElementsByClassName('comment-btn');
    var imageTitles = [];
    for(var i=0; i< imageBtn.length; i++)
    {
        var par = commentBtn[i].parentElement.parentElement;
        var titleDiv = par.childNodes[1];
        var title = titleDiv.innerHTML;
        imageTitles.push(title);
    }
    var requestMsg = 'getComments';
    var requestJson = JSON.stringify(imageTitles);
    var urlAppend = requestMsg + '=' + requestJson;
    url += urlAppend;

    request('GET',url);
}
function request(type,url){
    var http = new XMLHttpRequest();
    http.open(type,url);
    http.send();
    http.onreadystatechange=(e)=>{
        //console.log(http.responseText);
    }
}