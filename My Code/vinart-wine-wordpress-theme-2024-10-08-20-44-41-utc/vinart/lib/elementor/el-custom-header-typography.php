<?php
namespace VinartTheme\Classes;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Vinart_Page_Header_Typography extends \Elementor\Core\Kits\Documents\Tabs\Tab_Base {

	public function get_id() {
		return 'page-header-typography';
	}

	public function get_title() {
		return esc_html__( 'Page Header Typography', 'vinart' );
	}

	public function get_group() {
		return 'theme-style';
	}

	// Optionally, define an icon and help URL if needed

	protected function register_tab_controls() {
		$header_selector = '{{WRAPPER}} .page-meta';
		$heading_selector = $header_selector . ' .entry-title';

		$this->start_controls_section(
			'page_header_typography_section',
			[
				'label' => esc_html__( 'Page Header Typography', 'vinart' ),
				'tab' => $this->get_id(),
			]
		);

		$this->add_control(
			'page_header_heading',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Heading', 'vinart' ),
			]
		);

		$this->add_control(
			'page_header_heading_color',
			[
				'label' => esc_html__( 'Text Color', 'vinart' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => [],
				'selectors' => [
					$heading_selector => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'page_header_heading_typography',
				'selector' => $heading_selector,
			]
		);

		$this->add_responsive_control(
			'page_header_padding',
			[
				'label' => esc_html__( 'Padding', 'vinart' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					$header_selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'page_header_background',
				'types' => [ 'classic', 'gradient' ],
				'selector' => $header_selector,
				'fields_options' => [
					'background' => [
						'frontend_available' => true,
					],
					'color' => [
						'dynamic' => [],
					],
					'color_b' => [
						'dynamic' => [],
					],
				],
			]
		);

		// Add more controls if needed

		$this->end_controls_section();
	}
}
