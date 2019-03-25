var url = '/index.php?';

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

    // query DB for all comments related to each image
    var commentBtn = document.getElementsByClassName('comment-btn');
    var imageTitles = [];
    for(var i=0; i< commentBtn.length; i++)
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
function request(type,url)
{
    var http = new XMLHttpRequest();
    http.open(type,url);
    http.send();
    http.onreadystatechange=(e)=>{
        console.log(http.responseText);
    }
}