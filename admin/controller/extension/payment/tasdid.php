<?php
//  Author: Muamal Helali
//  E-Mail : dev.muamal@gmail.com

class ControllerExtensionPaymentTasdid extends Controller
{

	private $error = array();

	// Generate the URLs to the API - UAT. Tasdid. net.
	const TEST_BASE_URL = 'https://api-uat.tasdid.net/';

	const BASE_URL = 'https://api.tasdid.net/';


	// Displays the marketplace extension settings page.
	public function index()
	{
		$this->load->language('extension/payment/tasdid');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		// Sets the error warning.
		// Redirects to the marketplace extension.
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('payment_tasdid', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
		}

		// Sets the error warning.
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}


		$data['breadcrumbs'] = array();
		// Gets the breadcrumbs for the current language

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		// Displays the breadcrumbs for a marketplace extension
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
		);

		// Get the breadcrumbs for the payment
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/payment/tasdid', 'user_token=' . $this->session->data['user_token'], true)
		);

		// Adds a link to the payment tasdid page
		$data['action'] = $this->url->link('extension/payment/tasdid', 'user_token=' . $this->session->data['user_token'], true);

		// Creates a payment link for the marketplace extension.
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);


		// Sets the error redirect url.
		if (isset($this->error['error_redirect_url'])) {
			$data['error_redirect_url'] = $this->error['error_redirect_url'];
		} else {
			$data['error_redirect_url'] = '';
		}

		// Sets the payment_tasdid_total config.
		if (isset($this->request->post['payment_tasdid_total'])) {
			$data['payment_tasdid_total'] = $this->request->post['payment_tasdid_total'];
		} else {
			$data['payment_tasdid_total'] = $this->config->get('payment_tasdid_total');
		}

		// Get payment_tasdid_username from config.

		if (isset($this->request->post['payment_tasdid_username'])) {
			$data['payment_tasdid_username'] = $this->request->post['payment_tasdid_username'];
		} else {
			$data['payment_tasdid_username'] = $this->config->get('payment_tasdid_username');
		}

		// Sets the payment_tasdid_env variable to be used in the request.
		if (isset($this->request->post['payment_tasdid_env'])) {
			$data['payment_tasdid_env'] = $this->request->post['payment_tasdid_env'];
		} else {
			$data['payment_tasdid_env'] = $this->config->get('payment_tasdid_env');
		}

		// Get the payment tasdid service id.
		if (isset($this->request->post['payment_tasdid_service_id'])) {
			$data['payment_tasdid_service_id'] = $this->request->post['payment_tasdid_service_id'];
		} else {
			$data['payment_tasdid_service_id'] = $this->config->get('payment_tasdid_service_id');
		}

		// Sets the payment_tasdid_redirect_url from the config
		if (isset($this->request->post['payment_tasdid_redirect_url'])) {
			$data['payment_tasdid_redirect_url'] = $this->request->post['payment_tasdid_redirect_url'];
		} else {
			$data['payment_tasdid_redirect_url'] = $this->config->get('payment_tasdid_redirect_url');
		}

		// Get the payment_tasdid_password configuration.
		if (isset($this->request->post['payment_tasdid_password'])) {
			$data['payment_tasdid_password'] = $this->request->post['payment_tasdid_password'];
		} else {
			$data['payment_tasdid_password'] = $this->config->get('payment_tasdid_password');
		}

		// Get payment_tasdid_order_status_id from config
		if (isset($this->request->post['payment_tasdid_order_status_id'])) {
			$data['payment_tasdid_order_status_id'] = $this->request->post['payment_tasdid_order_status_id'];
		} else {
			$data['payment_tasdid_order_status_id'] = $this->config->get('payment_tasdid_order_status_id');
		}

		// Sets the payment_tasdid_order_status_before_pay_id in the config
		if (isset($this->request->post['payment_tasdid_order_status_before_pay_id'])) {
			$data['payment_tasdid_order_status_before_pay_id'] = $this->request->post['payment_tasdid_order_status_before_pay_id'];
		} else {
			$data['payment_tasdid_order_status_before_pay_id'] = $this->config->get('payment_tasdid_order_status_before_pay_id');
		}

		// Order status.
		$this->load->model('localisation/order_status');

		// Get order statuses
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		// Get the payment_tasdid_geo_zone id.
		if (isset($this->request->post['payment_tasdid_geo_zone_id'])) {
			$data['payment_tasdid_geo_zone_id'] = $this->request->post['payment_tasdid_geo_zone_id'];
		} else {
			$data['payment_tasdid_geo_zone_id'] = $this->config->get('payment_tasdid_geo_zone_id');
		}

		// Set the payment_tasdid_currencies configuration.
		if (isset($this->request->post['payment_tasdid_currencies'])) {
			$data['payment_tasdid_currencies'] = $this->request->post['payment_tasdid_currencies'];
		} else {
			$data['payment_tasdid_currencies'] = $this->config->get('payment_tasdid_currencies');
		}
		// Set the payment_tasdid_redirect_debug data
		if (isset($this->request->post['payment_tasdid_redirect_debug'])) {
			$data['payment_tasdid_redirect_debug'] = $this->request->post['payment_tasdid_redirect_debug'];
		} else {
			$data['payment_tasdid_redirect_debug'] = $this->config->get('payment_tasdid_redirect_debug');
		}


		// Loads the localisation currencies from the database
		$this->load->model('localisation/currency');

		$results = $this->model_localisation_currency->getCurrencies([]);

		$data['currencies'] = [];

		foreach ($results as $result) {
			$data['currencies'][] = array(
				'currency_id' => $result['currency_id'],
				'title' => $result['title'],
				'code' => $result['code'],
			);
		}

		// Load the localisation geo zone model.
		$this->load->model('localisation/geo_zone');

		// Get GeoZones data
		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		// Get payment_tasdid_status from config
		if (isset($this->request->post['payment_tasdid_status'])) {
			$data['payment_tasdid_status'] = $this->request->post['payment_tasdid_status'];
		} else {
			$data['payment_tasdid_status'] = $this->config->get('payment_tasdid_status');
		}

		// Get payment_tasdid_sort_order from config.
		if (isset($this->request->post['payment_tasdid_sort_order'])) {
			$data['payment_tasdid_sort_order'] = $this->request->post['payment_tasdid_sort_order'];
		} else {
			$data['payment_tasdid_sort_order'] = $this->config->get('payment_tasdid_sort_order');
		}

		// Adds the header and footer to the controller.
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		// Sets the payment tasdid output.
		$this->response->setOutput($this->load->view('extension/payment/tasdid', $data));
	}

	// Returns the url for the post.
	private function getUrl()
	{
		return $this->request->post['payment_tasdid_env'] == 'TEST' ? static::TEST_BASE_URL : static::BASE_URL;
	}

	// Authenticate the Tasdid account.

	private function loginTasdid()
	{
		$curl = curl_init();

		$username = $this->request->post['payment_tasdid_username'];

		$passeord = $this->request->post['payment_tasdid_password'];

		$request = [
			'username' => $username,
			'password' => $passeord
		];

		// cURL options array
		curl_setopt_array(
			$curl,
			array(
				CURLOPT_URL => "{$this->getUrl()}v1/api/Auth/Token",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => json_encode($request),
				CURLOPT_HTTPHEADER => array(
					'Content-Type: application/json'
				),
			)
		);

		$response = curl_exec($curl);

		curl_close($curl);

		$response = json_decode($response, true);

		return $response;
	}

	// Get a service.
	private function getService($token = '')
	{
		$curl = curl_init();

		curl_setopt_array(
			$curl,
			array(
				CURLOPT_URL => "{$this->getUrl()}v1/api/Provider/GetService?serviceId=" . $this->request->post['payment_tasdid_service_id'],
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_HTTPHEADER => [
					'Content-Type: application/json',
					'Authorization: Bearer ' . $token
				],
			)
		);

		$response = curl_exec($curl);

		curl_close($curl);

		$response = json_decode($response, true);

		return $response ?? false;
	}

	// Checks if the current user has permission to modify the payment.
	protected function validate()
	{
		if (!$this->user->hasPermission('modify', 'extension/payment/tasdid')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$loginTasdid = $this->loginTasdid();

		if (!empty($loginTasdid['error'])) {
			$this->error['warning'] = $loginTasdid['message'];
		}

		$token = $loginTasdid['token'] ?? '';

		if (empty($loginTasdid['error']) && !$this->getService($token)) {
			$this->error['warning'] = 'Invalid Service';
		}

		if (filter_var($this->request->post['payment_tasdid_redirect_url'], FILTER_VALIDATE_URL) === FALSE) {
			$this->error['error_redirect_url'] = 'Invalid Url';
		}


		return !$this->error;
	}

	// Installs the payment id
	public function install()
	{
		$this->db->query("ALTER TABLE " . DB_PREFIX . "order ADD COLUMN IF NOT EXISTS tasdid_pay_id VARCHAR(255) AFTER order_id");
	}

}