<?php get_header(); ?>
<main id="site-content" role="main">
	<div class="section-inner thin">
		<h1 class="entry-title"><?php the_title(); ?></h1>
        <?php

        $user_list = $vladimir_plugin->get_user_list();

        ?>
		<table id="user-list-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($user_list as $user) { ?>
                    <tr class="user-row" data-user-id="<?= $user['id'] ?>">
                        <td><?= $user['id'] ?></td>
                        <td><?= $user['name']; ?></td>
                        <td><?= $user['email']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
	</div>
</main>
<style>
#user-list-table tr.user-row:hover {
    background-color: #EEE;
    cursor: pointer;
}

#user-list-table tr.details {
    font-size: 1.5rem;
}

#user-list-table tr.details .preloader {
    display: block;
    width: 100%;
    height: 100px;
    background: url(../assets/img/preloader.gif);
    background-repeat: no-repeat;
}

#user-list-table tr.user-row:hover td {
    text-decoration: underline;
}
</style>
<script>
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
                    // TODO: error
                }

                //if(!response.result)
                    // TODO: error

                newDetails.innerHTML = '<td colspan="3"><div>ID: ' + response.details.id + '</div>'
                    + '<div>Name: ' + response.details.name + '</div>'
                    + '<div>Username: ' + response.details.username + '</div>'
                    + '<div>Website: ' + response.details.website + '</div>'
                    + '<div>Phone: ' + response.details.phone + '</div></td>';
            }

            xhr.open('GET', '/user_details?user_id=' + parent.dataset.userId, true);
            xhr.send();
        });
    }
</script>
<?php get_footer(); ?>