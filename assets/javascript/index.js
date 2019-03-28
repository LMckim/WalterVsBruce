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
    // handles most shit i guess
    document.addEventListener('click',function(event){
        // handle getting comments
        if(event.target.matches('.image-card-overlay')){
            var container = event.target.parentNode.children;
            container = container[1].children;
            var title = container[1].innerHTML;
            
            var requestMsg = 'getExpandedImage';
            var urlAppend = requestMsg + '=' + title;
            var sendUrl = url + urlAppend;

            request('GET',sendUrl,openExpandedImage,title);

        }else if(event.target.matches('.card-title')){
            var title = event.target.innerHTML;
            
            var requestMsg = 'getComments';
            var urlAppend = requestMsg + '=' + title;
            var sendUrl = url + urlAppend;

            request('GET',sendUrl);
        }
    });

}
function request(type,url,callback,title){
    var http = new XMLHttpRequest();
    http.open(type,url);
    http.send();
    //  will call multiple times describing its state
    http.onreadystatechange=(e)=>{;
        if(http.readyState == 4)
        {
            callback(http.responseText,title);
        }
    }
}
function openExpandedImage(response,title)
{
    response = JSON.parse(response);
    let overlay = document.getElementById('image-expanded');
    overlay.style.setProperty('display','block');
    
    let imageContainer = overlay.children[1].children[0];
    let commentsContainer = overlay.children[1].children[1];

}
