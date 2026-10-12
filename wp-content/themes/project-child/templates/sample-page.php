<?php
/**
 * Template Name: Trang giới thiệu mẫu
 * Template Post Type: page
 *
 * @package Project_Child
 */

defined( 'ABSPATH' ) || exit;

get_header();
while ( have_posts() ) :
	the_post();
	$content = project_child_get_sample_content( get_the_ID() );
	?>
	<article id="main" class="project-sample">
		<?php get_template_part( 'template-parts/sample/hero', null, $content ); ?>
		<?php if ( '1' === $content['about_visible'] ) : ?>
			<?php get_template_part( 'template-parts/sample/about', null, $content ); ?>
		<?php endif; ?>
		<?php if ( trim( get_the_content() ) ) : ?>
			<div id="noi-dung" class="project-sample__content project-sample__container entry-content">
				<?php the_content(); ?>
				<?php wp_link_pages(); ?>
			</div>
		<?php endif; ?>
		<?php if ( '1' === $content['contact_visible'] ) : ?>
			<?php get_template_part( 'template-parts/sample/contact', null, $content ); ?>
		<?php endif; ?>
	</article>
	<?php
endwhile;
get_footer();
