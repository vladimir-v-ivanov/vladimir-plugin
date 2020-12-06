<?php get_header(); ?>
<main id="site-content" role="main">
	<div class="section-inner thin">
		<h1 class="entry-title"><?php the_title(); ?></h1>
        <?php

        $user_list = $vladimir_plugin->get_user_list();
        $table_columt_titles = $vladimir_plugin->get_config()['table_column_titles'];

        ?>
		<table id="user-list-table">
            <thead>
                <tr>
                    <th><?= $table_columt_titles['id']; ?></th>
                    <th><?= $table_columt_titles['name']; ?></th>
                    <th><?= $table_columt_titles['email']; ?></th>
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
<?php get_footer(); ?>