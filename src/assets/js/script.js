!function(){
    document.addEventListener('DOMContentLoaded', function(){
        let userRows = document.querySelectorAll('#user-list-table .user-row'),
            i;

        for(i = 0; i < userRows.length; i++) {
            userRows[i].addEventListener('click', function(e){
                let currentDetails = document.querySelector('#user-list-table tr.details'),
                    newDetails,
                    parent = e.target,
                    xhr = new XMLHttpRequest;

                while(!parent.classList.contains('user-row') && (parent = parent.parentElement))
                    ;

                if(currentDetails != null)
                    currentDetails.parentElement.removeChild(currentDetails);

                newDetails = document.createElement('tr');
                newDetails.className = 'details';
                newDetails.innerHTML = '<td colspan="3"><div class="preloader"></div></td>';


                parent.parentElement.insertBefore(newDetails, parent.nextElementSibling);

                xhr.onreadystatechange = function () {
                    if (xhr.readyState != XMLHttpRequest.DONE || xhr.status != 200)
                        return;

                    try {
                        var response = JSON.parse(xhr.response);
                    } catch (e) {
                        // TODO: error|
                    }

                    //if(!response.result)
                        // TODO: error

                    newDetails.innerHTML = '<td colspan="3"><div>ID: ' + response.details.id + '</div>'
                        + '<div>Name: ' + response.details.name + '</div>'
                        + '<div>Username: ' + response.details.username + '</div>'
                        + '<div>Website: ' + response.details.website + '</div>'
                        + '<div>Phone: ' + response.details.phone + '</div></td>';
                }

                xhr.open('GET', vladimir_plugin_url + '/user_details?user_id=' + parent.dataset.userId, true);
                xhr.send();
            });
        }
    });
}();