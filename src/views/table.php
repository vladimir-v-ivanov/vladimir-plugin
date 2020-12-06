<?php get_header(); ?>
<main id="site-content" role="main">
	<div class="section-inner thin">
		<h1 class="entry-title"><?php the_title(); ?></h1>
        <?php

        $user_list = $vladimir_plugin->get_user_list();
        $table_columt_titles = $vladimir_plugin->get_config()['table_column_titles'];

        ?>
        <?php if(count($user_list)) { ?>
		<table id="user-list-table">
            <thead>
                <tr>
                    <th><?= htmlspecialchars($table_columt_titles['id']); ?></th>
                    <th><?= htmlspecailchars($table_columt_titles['name']); ?></th>
                    <th><?= htmlspecialchars($table_columt_titles['email']); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($user_list as $user) { ?>
                    <tr class="user-row" data-user-id="<?= $user['id'] ?>">
                        <td><?= htmlspecialchars($user['id']); ?></td>
                        <td><?= htmlspecialchars($user['name']); ?></td>
                        <td><?= htmlspecialchars($user['email']); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php } else { ?>
            <div class="not-found">Users not found.</div>
        <?php } ?>
	</div>
</main>
<script>
const vladimir_plugin_url = <?= json_encode(get_site_url()); ?>;
</script>
<?php get_footer(); ?>