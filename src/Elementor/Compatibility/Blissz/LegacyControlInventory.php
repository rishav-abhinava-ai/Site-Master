<?php
/** Source-derived Blissz Elementor persisted-control inventory. @package SiteMaster */

namespace Abhinava\SiteMaster\Elementor\Compatibility\Blissz;

defined( 'ABSPATH' ) || exit;

final class LegacyControlInventory {
	/** @return array<string,array<string,mixed>> */
	public static function all(): array {
		return array(
			'tbm_breadcrumb' => array(
				'controls' => array( 'separator_type', 'separator_text', 'separator_media_icon', 'alignment', 'wrapper_justify_content', 'wrapper_width', 'wrapper_max_width', 'wrapper_margin', 'wrapper_padding', 'wrapper_background', 'wrapper_border_radius', 'items_gap', 'link_color', 'current_color', 'link_hover_color', 'link_decoration', 'separator_color', 'separator_size', 'separator_spacing', 'separator_icon_width', 'separator_icon_height', 'separator_icon_border_radius', 'separator_opacity', 'separator_vertical_align', 'current_character_limit', 'current_padding' ),
				'groups' => array( 'wrapper_border', 'wrapper_box_shadow', 'home_typography', 'page_post_title_typography', 'current_typography' ),
				'defaults' => array( 'separator_type' => 'rankmath', 'alignment' => 'left', 'current_character_limit' => 0 ),
			),
			'tbm-date-widget' => array(
				'controls' => array( 'absolute_format_preset', 'date_time_view', 'text_color', 'padding', 'margin', 'border_radius', 'position', 'display_mode' ),
				'groups' => array( 'typography', 'border' ),
				'defaults' => array( 'absolute_format_preset' => 'india_apac', 'date_time_view' => 'date_time', 'display_mode' => 'flex' ),
			),
			'tbm-post-author-widget' => array(
				'controls' => array( 'author_id_override', 'image_position', 'content_alignment', 'content_vertical_alignment', 'show_avatar', 'show_name', 'name_format', 'name_prefix', 'name_html_tag', 'name_link_type', 'name_link', 'show_bio', 'show_post_count', 'box_padding', 'box_gap', 'box_border_radius', 'avatar_width', 'avatar_height', 'avatar_object_fit', 'avatar_border_radius', 'name_color', 'name_blend_mode', 'name_spacing', 'bio_color', 'bio_alignment', 'bio_link_color', 'bio_link_hover_color', 'bio_columns', 'bio_column_gap', 'bio_paragraph_spacing', 'bio_spacing', 'post_count_color', 'post_count_alignment', 'post_count_vertical_alignment' ),
				'groups' => array( 'box_background', 'box_border', 'box_shadow', 'avatar_border', 'name_typography', 'name_text_shadow', 'bio_typography', 'bio_text_shadow', 'post_count_typography' ),
				'defaults' => array( 'author_id_override' => 0, 'show_avatar' => 'yes', 'show_name' => 'yes', 'name_html_tag' => 'span', 'name_link_type' => 'archive', 'show_bio' => 'no', 'show_post_count' => 'no' ),
			),
			'tbm-post-content' => array(
				'controls' => array( 'text_align', 'text_color', 'link_color', 'link_hover_color', 'paragraph_spacing', 'alternate_template_id', 'alternate_template_position', 'alternate_template_apply_once', 'alternate_template_column_span', 'alternate_template_column_span_tablet', 'alternate_template_column_span_mobile', 'alternate_template_static_position', 'related_post_content', 'related_post_layout_heading', 'related_post_template_id', 'related_post_columns', 'related_post_items_per_page', 'related_post_after_paragraph', 'related_post_equal_height', 'related_post_alternate_templates_heading', 'related_post_enable_alternate_templates', 'related_post_alternate_templates', 'advertisement_html', 'advertisement_after_paragraph', 'enable_advertisements', 'advertisements', 'related_post_style_alignment', 'related_post_style_background_color', 'related_post_style_border_radius', 'related_post_style_margin', 'related_post_style_padding', 'related_post_style_row_gap', 'related_post_style_column_gap', 'image_caption_text_color', 'image_caption_alignment', 'image_caption_spacing' ),
				'groups' => array( 'typography', 'related_post_style_border', 'related_post_style_box_shadow', 'image_caption_typography' ),
				'defaults' => array( 'related_post_content' => 'no', 'enable_advertisements' => 'no', 'text_align' => 'left' ),
			),
			'tbm_post_search' => array(
				'controls' => array( 'placeholder', 'aria_label', 'show_icon', 'search_icon', 'icon_position', 'show_input_icon', 'input_media_icon', 'show_submit_button', 'submit_button_text', 'search_display_mode', 'wrapper_width', 'wrapper_max_width', 'wrapper_alignment', 'form_gap', 'wrapper_background_color', 'wrapper_border_radius', 'wrapper_padding', 'input_text_color', 'input_placeholder_color', 'input_background_color', 'input_border_radius', 'input_padding', 'input_min_height', 'input_height', 'icon_color', 'icon_background_color', 'icon_size', 'icon_padding', 'icon_border_radius', 'input_media_icon_size', 'input_media_icon_spacing', 'input_media_icon_border_radius', 'input_media_icon_opacity', 'submit_button_position', 'submit_button_horizontal_side', 'submit_button_horizontal_offset_right', 'submit_button_horizontal_offset_left', 'submit_button_vertical_offset', 'submit_button_text_color', 'submit_button_background_color', 'submit_button_hover_text_color', 'submit_button_hover_background_color', 'submit_button_padding', 'submit_button_border_radius', 'submit_button_width', 'submit_button_min_width', 'submit_button_min_height', 'focus_border_color', 'focus_background_color' ),
				'groups' => array( 'wrapper_border', 'wrapper_box_shadow', 'input_typography', 'input_border', 'submit_button_typography', 'submit_button_border', 'submit_button_box_shadow', 'focus_box_shadow' ),
				'defaults' => array( 'placeholder' => 'Type and press Enter', 'aria_label' => 'Search', 'show_icon' => 'yes', 'show_submit_button' => 'no', 'search_display_mode' => 'inline' ),
			),
			'tbz_loop_post_block' => array(
				'controls' => array( 'show_image', 'image_source', 'image_layout', 'image_ratio', 'image_width', 'image_height', 'image_object_fit', 'image_object_position', 'show_category', 'category_source', 'link_category', 'show_category_icon', 'category_icon_position', 'category_position', 'category_position_preset', 'category_horizontal_offset', 'category_vertical_offset', 'category_color', 'content_background', 'show_title', 'title_tag', 'link_title', 'title_align', 'title_line_clamp', 'show_excerpt', 'excerpt_length', 'excerpt_align', 'show_author', 'author_name_format', 'show_author_image', 'show_by_text', 'by_text', 'show_date', 'date_format', 'date_icon', 'show_reading_time', 'reading_time_icon', 'meta_separator_icon', 'show_button', 'button_text', 'button_icon', 'button_icon_position', 'card_bg_color', 'card_padding', 'card_fit_to_column', 'card_min_height', 'card_radius', 'card_custom_box_shadow', 'image_radius', 'image_hover_scale', 'content_custom_bg_color', 'content_padding', 'content_margin', 'content_gap', 'content_width', 'content_align', 'content_radius', 'content_body_blur', 'category_custom_bg_color', 'category_custom_text_color', 'category_gap', 'category_alignment', 'category_padding', 'category_radius', 'category_icon_style_heading', 'category_icon_size', 'category_icon_spacing', 'category_icon_radius', 'category_icon_opacity', 'title_color', 'title_hover_color', 'excerpt_color', 'excerpt_line_clamp', 'footer_style_heading', 'footer_padding', 'footer_radius', 'footer_gap', 'footer_direction', 'footer_justify', 'footer_align', 'footer_wrap', 'meta_wrapper_style_heading', 'meta_padding', 'meta_radius', 'meta_gap', 'meta_direction', 'meta_justify', 'meta_align', 'meta_wrap', 'meta_item_gap', 'meta_global_style_heading', 'meta_color', 'meta_link_color', 'meta_link_hover_color', 'author_style_heading', 'author_color', 'author_link_color', 'author_link_hover_color', 'avatar_size', 'avatar_radius', 'date_style_heading', 'date_color', 'date_icon_color', 'reading_style_heading', 'reading_color', 'reading_icon_color', 'icon_separator_style_heading', 'meta_icon_color', 'meta_icon_size', 'separator_color', 'button_alignment', 'button_width', 'button_gap', 'button_icon_size', 'button_text_color', 'button_bg_color', 'button_hover_text_color', 'button_hover_bg_color', 'button_radius', 'button_padding' ),
				'groups' => array( 'thumbnail', 'card_border', 'card_shadow', 'content_body_border', 'content_body_shadow', 'category_typography', 'title_typography', 'excerpt_typography', 'footer_background', 'footer_border', 'meta_background', 'meta_border', 'meta_typography', 'author_typography', 'date_typography', 'reading_typography', 'button_typography', 'button_border' ),
				'defaults' => array( 'show_image' => 'yes', 'show_category' => 'yes', 'show_title' => 'yes', 'title_tag' => 'h3', 'show_excerpt' => 'yes', 'excerpt_length' => 20, 'show_author' => 'yes', 'show_date' => 'yes' ),
			),
			'blissz_loop_category_block' => array(
				'controls' => array( 'show_image', 'image_link', 'image_ratio', 'show_badge', 'badge_position', 'overlay_position', 'link_category', 'show_icon', 'icon_position', 'color_source', 'block_gap', 'block_padding', 'block_background', 'block_radius', 'image_width', 'image_height', 'object_fit', 'image_radius', 'image_hover_zoom', 'badge_bg', 'badge_color', 'badge_padding', 'badge_radius', 'badge_gap', 'overlay_x', 'overlay_y', 'icon_size', 'icon_spacing', 'icon_radius', 'icon_opacity' ),
				'groups' => array( 'thumbnail', 'block_border', 'block_shadow', 'badge_typography' ),
				'defaults' => array( 'show_image' => 'yes', 'image_link' => 'category', 'show_badge' => 'yes', 'badge_position' => 'below', 'link_category' => 'yes', 'show_icon' => 'yes' ),
			),
			'tbz_loop_single_post_block' => array(
				'controls' => array( 'enable_schema', 'show_top_meta', 'image_position', 'show_divider', 'show_image', 'image_source', 'link_image', 'image_ratio', 'image_width', 'image_height', 'image_object_fit', 'image_object_position', 'show_category', 'category_source', 'link_category', 'show_category_icon', 'category_icon_position', 'category_color', 'content_background', 'show_title', 'title_tag', 'link_title', 'title_align', 'title_line_clamp', 'show_excerpt', 'excerpt_length', 'excerpt_align', 'show_author', 'author_name_format', 'show_author_image', 'show_by_text', 'by_text', 'show_date', 'date_format', 'date_icon', 'show_reading_time', 'reading_time_area', 'reading_time_icon', 'meta_separator_icon', 'show_button', 'button_text', 'button_icon', 'button_icon_position', 'card_bg_color', 'card_padding', 'card_margin', 'card_radius', 'card_custom_box_shadow', 'image_margin', 'image_padding', 'image_radius', 'image_hover_scale', 'content_custom_bg_color', 'content_padding', 'content_margin', 'content_gap', 'content_radius', 'content_body_blur', 'top_meta_align', 'top_meta_margin', 'top_meta_gap', 'top_reading_color', 'top_separator_color', 'category_custom_bg_color', 'category_custom_text_color', 'category_gap', 'category_padding', 'category_radius', 'category_icon_style_heading', 'category_icon_size', 'category_icon_spacing', 'category_icon_radius', 'category_icon_opacity', 'title_margin', 'title_padding', 'title_color', 'title_hover_color', 'excerpt_color', 'excerpt_line_clamp', 'divider_color', 'divider_thickness', 'meta_row_margin', 'meta_row_gap', 'author_style_heading', 'author_color', 'author_link_color', 'author_link_hover_color', 'avatar_size', 'avatar_radius', 'date_style_heading', 'date_color', 'date_icon_color', 'reading_style_heading', 'reading_color', 'reading_icon_color', 'icon_separator_style_heading', 'meta_icon_color', 'meta_icon_size', 'separator_color', 'button_text_color', 'button_bg_color', 'button_hover_text_color', 'button_hover_bg_color', 'button_radius', 'button_padding' ),
				'groups' => array( 'thumbnail', 'card_border', 'card_shadow', 'image_shadow', 'content_body_border', 'content_body_shadow', 'top_reading_typography', 'category_typography', 'title_typography', 'excerpt_typography', 'author_typography', 'date_typography', 'reading_typography', 'button_typography', 'button_border' ),
				'defaults' => array( 'enable_schema' => 'yes', 'show_image' => 'yes', 'link_image' => 'no', 'show_category' => 'yes', 'show_title' => 'yes', 'title_tag' => 'h1', 'show_excerpt' => 'yes', 'show_author' => 'yes', 'show_date' => 'yes' ),
			),
		);
	}
	/** @return string[] */
	public static function controls( string $widget_id ): array {
		$all = self::all();
		return $all[ $widget_id ]['controls'] ?? array();
	}
	/** @return array<string,mixed> */
	public static function defaults( string $widget_id ): array {
		$all = self::all();
		return $all[ $widget_id ]['defaults'] ?? array();
	}
}
