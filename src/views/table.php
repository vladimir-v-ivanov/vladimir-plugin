<?php get_header(); ?>
<main id="main" class="site-main" role="main">
    <article id="post-1" class="post-1 post type-post status-publish format-standard hentry category-uncategorized entry">
        <header class="entry-header alignwide">
            <h1 class="entry-title"><?php the_title(); ?></h1>
        </header>
        <div class="entry-content">
            <?php $user_list = $vladimir_plugin->get_user_list(); ?>
            <?php if( count( $user_list ) ) : ?>
            <table id="user-list-table">
                <thead>
                    <tr>
                        <th><?php echo __( 'ID', 'vladimir-plugin' ); ?></th>
                        <th><?php echo __( 'Name', 'vladimir-plugin' ); ?></th>
                        <th><?php echo __( 'E-Mail', 'vladimir-plugin' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach( $user_list as $user ) { ?>
                        <tr class="user-row" data-user-id="<?php echo esc_html( $user[ 'id' ] ); ?>">
                            <td><?php echo esc_html( $user[ 'id' ] ); ?></td>
                            <td><?php echo esc_html( $user[ 'name' ] ); ?></td>
                            <td><?php echo esc_html( $user[ 'email' ] ); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php else : ?>
            <div class="not-found"><?php echo __( 'Users not found', 'vladimir-plugin' ); ?></div>
            <?php endif; ?>
        </div>
    </article>
</main>
<?php get_footer(); ?>