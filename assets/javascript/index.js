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
    // handles most click events
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
            var container = event.target.parentNode.parentNode.children;
            container = container[1].children;
            var title = container[1].innerHTML;
            
            var requestMsg = 'getExpandedImage';
            var urlAppend = requestMsg + '=' + title;
            var sendUrl = url + urlAppend;

            request('GET',sendUrl,openExpandedImage,title);
        }
    });

}
function request(type,url,callback,passIn){
    var http = new XMLHttpRequest();
    http.open(type,url);
    http.send();
    //  will call multiple times describing its state
    http.onreadystatechange=(e)=>{;
        if(http.readyState == 4)
        {
            callback(http.responseText,passIn);
        }
    }
}
function openExpandedImage(response,title)
{
    response = JSON.parse(response);
    let overlay = document.getElementById('image-expanded');
    overlay.style.setProperty('display','flex');
    
    // add image to container
    let imageContainer = overlay.children[1].children[0].children[0];
    imageContainer.src = response.imageSrc;
    // handle comment container title and placing of comments
    let commentsContainer = overlay.children[1].children[1];
    commentsContainer.children[1].children[0].innerHTML = title;

    // add listener to exit button
    document.getElementById('exit-btn').addEventListener('click',function(){
        if(overlay.style.getPropertyValue('display') == 'flex'){
            overlay.style.setProperty('display','none');
        }
    });

    
    let commentsResponse = response.comments;
    // above is an object so we need to get the actual key names to cycle
    let keys = Object.keys(commentsResponse);

    let commentsHTML = [];

    for(let i=0; i<keys.length;i++){
        for(let j=0; j<commentsResponse[keys[i]].length;j++){
            
            let comment = commentsResponse[keys[i]][j];
            j++;
            let date = commentsResponse[keys[i]][j];

            let HTML = '<div class="comment-msg">'+
                        '<h4 class="comment-user">'+ keys[i] + '</h4>' +
                        '<p class="comment-text">'+ comment + '</p>' +
                        '<h7 class="comment-date">'+ date +'</h7>' +
                        '</div>';

            commentsHTML.push(HTML);
        }
    }
    for(let i=0; i<commentsHTML.length;i++){
        commentsContainer.children[2].innerHTML = commentsContainer.children[2].innerHTML + commentsHTML[i];
    }
}
