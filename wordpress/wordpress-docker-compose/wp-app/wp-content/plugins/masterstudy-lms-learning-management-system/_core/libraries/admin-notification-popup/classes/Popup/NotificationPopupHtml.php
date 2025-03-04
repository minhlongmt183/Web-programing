<?php


namespace ANP\Popup;

use ANP\NotificationEnqueueControl;

class NotificationPopupHtml {

	public static $popup_html;

	public static function init() {
		do_action( 'anp_popup_items' );
		self::generate_popup_html();
	}

	public static function generate_popup_html() {

		$notifications = NotificationEnqueueControl::getItems();
		$product_name  = get_product_name();
		$plugin_name   = isset( $product_name['plugin_name'] ) ? $product_name['plugin_name'] : '';

		if ( count( NotificationEnqueueControl::getItems() ) > 0 ) {
			$html = '<div class="anp-popup-wrapper ab-sub-wrapper">';
			$html .= '<div class="anp-popup-wrapper-inner">';
			$html .= '<h3>' . esc_html__( 'Notifications', $plugin_name ) . '</h3>';
			$html .= '<div class="anp-items-wrap">';
			$html = self::getNotifyItems( 'main', $html, $notifications );
			$html = self::getNotifyItems( 'second', $html, $notifications );
			if ( 0 === count( $notifications['main'] ) && 0 === count( $notifications['second'] ) ) {
				$html = self::getEmptyItem( $html );
			}
			$html .= '</div></div></div>';

			self::$popup_html = $html;
		}
	}

	public static function getNotifyItems( $priority, $html, $notifications ) {
		if ( ! empty( $notifications[ $priority ] ) ) {
			foreach ( $notifications[ $priority ] as $key => $item ) {
				$html .= $item;
			}
		}

		return $html;
	}

	public static function getEmptyItem( $html ) {
		$product_name = get_product_name();
		$plugin_name  = isset( $product_name['plugin_name'] ) ? $product_name['plugin_name'] : '';

		$html .= '<div class="anp-empty-item">';
		$html .= '<div class="icon-silence">';
		$html .= '<svg width="24" height="27" viewBox="0 0 24 27" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path fill-rule="evenodd" clip-rule="evenodd" d="M10.4836 0H11.1011C11.3467 0 11.5823 0.0975817 11.756 0.271278C11.9297 0.444974 12.0273 0.680557 12.0273 0.926201C12.0273 1.17185 11.9297 1.40743 11.756 1.58112C11.5823 1.75482 11.3467 1.8524 11.1011 1.8524H10.4836C9.63209 1.85192 8.78879 2.01928 8.00197 2.34493C7.21514 2.67058 6.50023 3.14813 5.89809 3.75026C5.29595 4.3524 4.81841 5.06732 4.49276 5.85414C4.16711 6.64096 3.99974 7.48426 4.00023 8.33581V10.5797C4.00019 11.9326 3.67087 13.2652 3.04068 14.4623L2.17252 16.1122C1.978 16.4804 1.86879 16.8876 1.85298 17.3037C1.83718 17.7198 1.91518 18.1341 2.08121 18.516C2.24724 18.8979 2.49705 19.2375 2.81213 19.5097C3.12722 19.782 3.49951 19.9798 3.90143 20.0887C8.65223 21.3483 12.9239 21.3458 17.6611 20.0368L17.666 20.0356C18.0603 19.9287 18.4255 19.7346 18.7346 19.4675C19.0437 19.2005 19.2889 18.8673 19.4519 18.4928C19.6149 18.1182 19.6917 17.7118 19.6765 17.3036C19.6613 16.8954 19.5545 16.4958 19.3641 16.1344L19.3319 16.0677C19.0919 15.5066 18.8139 14.9765 18.5691 14.5101L18.5441 14.4623C17.9139 13.2652 17.5845 11.9326 17.5845 10.5797C17.5845 10.334 17.6821 10.0985 17.8558 9.92476C18.0295 9.75107 18.2651 9.65349 18.5107 9.65349C18.7564 9.65349 18.9919 9.75107 19.1656 9.92476C19.3393 10.0985 19.4369 10.334 19.4369 10.5797C19.4367 11.6322 19.6927 12.6689 20.1828 13.6003C20.4508 14.1091 20.7558 14.6908 21.0164 15.2959C21.3311 15.9024 21.506 16.5717 21.5283 17.2546C21.5506 17.9375 21.4197 18.6167 21.1454 19.2425C20.871 19.8682 20.46 20.4246 19.9425 20.8709C19.4251 21.3171 18.8143 21.6419 18.1551 21.8213C17.8967 21.8928 17.6397 21.9606 17.3838 22.0248C16.8946 23.2799 16.0573 24.3736 14.966 25.1742C13.7477 26.0681 12.2763 26.5504 10.7653 26.5511C9.25429 26.5506 7.78277 26.0683 6.56446 25.1745C5.48493 24.3825 4.65403 23.3038 4.16285 22.0656C3.91755 22.0063 3.67114 21.9438 3.42351 21.8781L3.41857 21.8769C2.74883 21.6953 2.12847 21.3656 1.60334 20.912C1.07822 20.4584 0.661776 19.8925 0.384803 19.2563C0.107829 18.6201 -0.0225867 17.9297 0.00319766 17.2363C0.0289821 16.5429 0.210307 15.8641 0.533766 15.2502L1.40193 13.6003C1.89219 12.6692 2.14783 11.6319 2.14783 10.5797V8.33581C2.14783 6.12502 3.02606 4.00477 4.58933 2.4415C6.1526 0.878235 8.27284 0 10.4836 0ZM6.52385 22.5417C6.8399 22.975 7.22241 23.36 7.66025 23.6812C8.56077 24.3419 9.64845 24.6983 10.7653 24.6987C11.8822 24.6981 12.9697 24.3416 13.8702 23.681C14.3155 23.3543 14.7036 22.9617 15.0227 22.5194C12.1592 22.997 9.39332 23.0014 6.52385 22.5417ZM21.3438 1.8524H20.6041C20.3584 1.8524 20.1228 1.75482 19.9491 1.58112C19.7754 1.40743 19.6779 1.17185 19.6779 0.926201C19.6779 0.680557 19.7754 0.444974 19.9491 0.271278C20.1228 0.0975817 20.3584 0 20.6041 0H23.0739C23.2416 1.21777e-05 23.406 0.0455191 23.5498 0.131669C23.6936 0.217818 23.8114 0.34138 23.8905 0.489179C23.9696 0.636978 24.0071 0.803473 23.999 0.970911C23.9909 1.13835 23.9375 1.30045 23.8445 1.43993L22.3342 3.7048H23.0739C23.3196 3.7048 23.5551 3.80239 23.7288 3.97608C23.9025 4.14978 24.0001 4.38536 24.0001 4.63101C24.0001 4.87665 23.9025 5.11223 23.7288 5.28593C23.5551 5.45963 23.3196 5.55721 23.0739 5.55721H20.6041C20.4364 5.5572 20.2719 5.51169 20.1281 5.42554C19.9843 5.33939 19.8666 5.21583 19.7875 5.06803C19.7084 4.92023 19.6709 4.75373 19.679 4.5863C19.6871 4.41886 19.7405 4.25676 19.8335 4.11727L21.3438 1.8524ZM15.8261 3.08734H14.2639C14.0183 3.08734 13.7827 2.98976 13.609 2.81606C13.4353 2.64236 13.3377 2.40678 13.3377 2.16114C13.3377 1.91549 13.4353 1.67991 13.609 1.50621C13.7827 1.33252 14.0183 1.23493 14.2639 1.23493H17.5575C17.7251 1.23495 17.8896 1.28045 18.0334 1.3666C18.1772 1.45275 18.2949 1.57631 18.374 1.72411C18.4531 1.87191 18.4906 2.03841 18.4825 2.20585C18.4744 2.37328 18.421 2.53539 18.3281 2.67487L15.9953 6.17467H17.5575C17.8031 6.17467 18.0387 6.27226 18.2124 6.44595C18.3861 6.61965 18.4837 6.85523 18.4837 7.10088C18.4837 7.34652 18.3861 7.5821 18.2124 7.7558C18.0387 7.9295 17.8031 8.02708 17.5575 8.02708H14.2639C14.0963 8.02707 13.9318 7.98156 13.788 7.89541C13.6442 7.80926 13.5264 7.6857 13.4473 7.5379C13.3683 7.3901 13.3307 7.2236 13.3388 7.05617C13.3469 6.88873 13.4003 6.72663 13.4933 6.58714L15.8261 3.08734Z" fill="#227AFF"/>
				</svg>';
		$html .= '</div>';
		$html .= '<h4>' . esc_html__( 'So far silence', $plugin_name ) . '</h4>';
		$html .= '<div>' . esc_html__( 'Something important will be here soon', $plugin_name ) . '</div>';
		$html .= '</div>';

		return $html;
	}

	public static function popup_html() {
		return self::$popup_html;
	}
}

NotificationPopupHtml::init();
