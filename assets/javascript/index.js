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
    var scrollPos = window.scrollY;;
    window.scrollTo(0,0);

    let commentsContentContainer = document.getElementsByClassName('comments-container')[0];
    commentsContentContainer.scrollTop;

    if(window.innerWidth > 600)
    {
        document.documentElement.style.overflow = 'hidden';
        document.body.scroll = 'no';
    }


    response = JSON.parse(response);
    let overlay = document.getElementById('image-expanded');
    overlay.style.setProperty('display','flex');
    
    // add image to container
    let imageContainer = overlay.children[1].children[0].children[0];
    imageContainer.src = response.imageSrc;
    // handle comment container title and placing of comments
    let commentsContainer = overlay.children[1].children[1];
    commentsContainer.children[1].children[0].innerHTML = title;

    // add listener to add comment button
    document.getElementById('add-comment-btn').addEventListener('click',function(){
        if(!document.getElementById('comment-form'))
        {
            title = this.parentElement.parentElement.children[1].children[0].textContent;
            // scroll to bottom of comments insert comment section
            commentsContentContainer.children[2].innerHTML = commentsContentContainer.children[2].innerHTML +
            '<div id="comment-form">'+
            '<h4 class="comment-form-title">LEAVE A COMMENT</h4>'+
            '<input class="comment-form-user" type="text" placeholder="UserName" name="comment-name">'+
            '<textarea class="comment-form-text" row="20" cols="50" placeholder="Speak your truth!"></textarea>'+
            '<div id="comment-form-submit">SUBMIT</div>'+
            '</div>';

            // add event listener for when submit is pressed
            let form = document.getElementById('comment-form');
            form.children[3].addEventListener('click',function(){
                let username = form.children[1].value;
                let commentText = form.children[2].value;

                var sendUrl = url +"addComment=''" +'&User='+username+'&Comment='+commentText+
                                    '&title='+title;
    
                request('GET',sendUrl,reloadComments);
            });

        }
        if(window.innerWidth < 600){
            let scrollMax = window.scrollMaxY;
            window.scrollTo(0,scrollMax);

        }else{
            let scrollMax = commentsContentContainer.scrollHeight;
            let clientHeight = commentsContentContainer.clientHeight;
            commentsContentContainer.scrollTop = scrollMax-clientHeight;
        }
    });

    // add listener to exit button
    document.getElementById('exit-btn').addEventListener('click',function(){
        window.scrollTo(0,scrollPos);

        document.documentElement.style.overflow = 'scroll';
        document.body.scroll = "yes";

        if(overlay.style.getPropertyValue('display') == 'flex'){
            overlay.style.setProperty('display','none');
        }
        let commentsContentContainer = document.getElementsByClassName('comments-container')[0];
            commentsContentContainer.children[2].innerHTML = '';
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
function reloadComments(comments){
    
}