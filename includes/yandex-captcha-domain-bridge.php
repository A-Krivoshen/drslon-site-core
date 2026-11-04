<?php
/**
 * Yandex SmartCaptcha for WP Domain Checker.
 *
 * The login form is handled by Dr.Slon Toolkit. This file only keeps the
 * domain-checker shortcode on the same Yandex keys: Google reCAPTCHA script
 * and siteverify calls are answered by SmartCaptcha.
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

function krv_ysc_setting_key(string $field, string $constant, string $legacy_option): string
{
    $settings = get_option('dstk_settings');

    if (is_array($settings) && isset($settings['yandex_captcha']) && is_array($settings['yandex_captcha'])) {
        $key = (string) ($settings['yandex_captcha'][$field] ?? '');

        if ($key !== '') {
            return $key;
        }
    }

    if (defined($constant)) {
        $value = (string) constant($constant);

        if ($value !== '') {
            return $value;
        }
    }

    return (string) get_option($legacy_option, '');
}

function krv_ysc_client_key(): string
{
    return krv_ysc_setting_key('client_key', 'KRV_YSC_CLIENT', 'krv_ysc_client');
}

function krv_ysc_server_key(): string
{
    return krv_ysc_setting_key('server_key', 'KRV_YSC_SERVER', 'krv_ysc_server');
}

function krv_ysc_user_ip(): string
{
    foreach (['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'] as $key) {
        if (empty($_SERVER[$key])) {
            continue;
        }

        $raw = sanitize_text_field(wp_unslash((string) $_SERVER[$key]));
        $ip = trim(explode(',', $raw)[0]);

        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return $ip;
        }
    }

    return '';
}

function krv_ysc_request_token(): string
{
    $best = '';

    foreach (['smart-token', 'g-recaptcha-response'] as $key) {
        if (! isset($_POST[$key])) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
            continue;
        }

        $vals = is_array($_POST[$key]) ? $_POST[$key] : [$_POST[$key]]; // phpcs:ignore WordPress.Security.NonceVerification.Missing

        foreach ($vals as $item) {
            $token = preg_replace('/[\x00-\x1F\x7F]/', '', trim((string) wp_unslash($item)));

            if (is_string($token) && strlen($token) > strlen($best)) {
                $best = $token;
            }
        }
    }

    return $best;
}

function krv_ysc_validate(string $token): bool
{
    static $memo = [];

    $token = trim($token);
    $secret = krv_ysc_server_key();

    if ($token === '' || $secret === '') {
        return false;
    }

    if (array_key_exists($token, $memo)) {
        return $memo[$token];
    }

    $response = wp_remote_post(
        'https://smartcaptcha.cloud.yandex.ru/validate',
        [
            'timeout' => 8,
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => [
                'secret' => $secret,
                'token' => $token,
                'ip' => krv_ysc_user_ip(),
            ],
        ]
    );

    if (is_wp_error($response)) {
        $memo[$token] = true;

        return true;
    }

    $code = (int) wp_remote_retrieve_response_code($response);

    if ($code !== 200) {
        $memo[$token] = true;

        return true;
    }

    $data = json_decode((string) wp_remote_retrieve_body($response));
    $ok = is_object($data) && isset($data->status) && $data->status === 'ok';
    $memo[$token] = $ok;

    return $ok;
}

function krv_ysc_is_google_recaptcha_src(string $src): bool
{
    return (bool) preg_match('#google\.com/recaptcha|gstatic\.com/recaptcha|recaptcha/api\.js#i', $src);
}

add_filter(
    'script_loader_src',
    static function ($src, $handle) {
        if (! is_string($src) || $src === '' || ! krv_ysc_is_google_recaptcha_src($src)) {
            return $src;
        }

        if ($handle === 'wdc-google-recaptcha') {
            return 'https://smartcaptcha.cloud.yandex.ru/captcha.js?render=onload&onload=wdcRecaptchaCallback';
        }

        return 'https://smartcaptcha.cloud.yandex.ru/captcha.js';
    },
    5,
    2
);

add_action(
    'wp_enqueue_scripts',
    static function (): void {
        $shim = <<<'JS'
window.wdcRecaptchaCallback = window.wdcRecaptchaCallback || function () {};
(function () {
	function whenReady(fn) {
		if (window.smartCaptcha) { fn(); return; }
		var n = 0;
		var t = setInterval(function () {
			n += 1;
			if (window.smartCaptcha) { clearInterval(t); fn(); }
			if (n > 200) { clearInterval(t); }
		}, 50);
	}
	window.grecaptcha = {
		ready: function (cb) { whenReady(cb); },
		render: function (el, opts) {
			opts = opts || {};
			if (typeof el === "string") { el = document.getElementById(el); }
			if (!el || !window.smartCaptcha) { return 0; }
			return window.smartCaptcha.render(el, {
				sitekey: opts.sitekey,
				hl: "ru",
				invisible: opts.size === "invisible",
				hideShield: false,
				callback: function (token) {
					var ta = el.querySelector("textarea.g-recaptcha-response");
					if (!ta) {
						ta = document.createElement("textarea");
						ta.className = "g-recaptcha-response";
						ta.name = "g-recaptcha-response";
						ta.style.display = "none";
						el.appendChild(ta);
					}
					ta.value = token;
					var cb = opts.callback;
					if (typeof cb === "function") { cb(token); }
					else if (typeof cb === "string" && typeof window[cb] === "function") { window[cb](token); }
				}
			});
		},
		execute: function (id) { if (window.smartCaptcha) { window.smartCaptcha.execute(id); } },
		reset: function (id) { if (window.smartCaptcha) { window.smartCaptcha.reset(id); } },
		getResponse: function (id) { return window.smartCaptcha ? (window.smartCaptcha.getResponse(id) || "") : ""; }
	};
})();
JS;
        wp_register_script('krv-ysc-shim', false, [], '2.1.0', false);
        wp_enqueue_script('krv-ysc-shim');
        wp_add_inline_script('krv-ysc-shim', $shim, 'after');
    },
    1
);

add_action(
    'wp_ajax_wdc_recaptcha',
    static function (): void {
        $token = isset($_REQUEST['response']) ? (string) wp_unslash($_REQUEST['response']) : '';

        if ($token === '') {
            $token = krv_ysc_request_token();
        }

        $ok = krv_ysc_validate($token);
        echo wp_json_encode(
            [
                'success' => $ok,
                'error-codes' => $ok ? [] : ['invalid-input-response'],
            ]
        );
        wp_die();
    },
    1
);

add_action(
    'wp_ajax_nopriv_wdc_recaptcha',
    static function (): void {
        do_action('wp_ajax_wdc_recaptcha');
    },
    1
);

add_filter(
    'option_wdc_redux_options',
    static function ($value) {
        if (! is_array($value)) {
            return $value;
        }

        $client = krv_ysc_client_key();

        if ($client !== '') {
            $value['recaptcha_sitekey'] = $client;
        }

        $value['recaptcha_secretkey'] = '';

        return $value;
    }
);

add_filter(
    'do_shortcode_tag',
    static function ($output, $tag) {
        if ($tag !== 'wpdomainchecker' || ! is_string($output)) {
            return $output;
        }

        $output = preg_replace('#<noscript>.*?</noscript>#si', '', $output);

        return is_string($output) ? $output : '';
    },
    10,
    2
);

add_filter(
    'pre_http_request',
    static function ($preempt, $args, $url) {
        if (! is_string($url) || stripos($url, 'google.com/recaptcha/api/siteverify') === false) {
            return $preempt;
        }

        $token = '';

        if (is_array($args) && ! empty($args['body'])) {
            if (is_array($args['body']) && ! empty($args['body']['response'])) {
                $token = (string) $args['body']['response'];
            } elseif (is_string($args['body'])) {
                parse_str($args['body'], $body);
                $token = isset($body['response']) ? (string) $body['response'] : '';
            }
        }

        if ($token === '') {
            $query = wp_parse_url($url, PHP_URL_QUERY);
            parse_str((string) $query, $qs);
            $token = isset($qs['response']) ? (string) $qs['response'] : '';
        }

        $ok = krv_ysc_validate($token);

        return [
            'headers' => [],
            'body' => wp_json_encode(
                [
                    'success' => $ok,
                    'error-codes' => $ok ? [] : ['invalid-input-response'],
                ]
            ),
            'response' => [
                'code' => 200,
                'message' => 'OK',
            ],
            'cookies' => [],
            'filename' => null,
        ];
    },
    10,
    3
);
