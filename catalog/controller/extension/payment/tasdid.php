<?php
//  Author: Muamal Helali
//  E-Mail : dev.muamal@gmail.com
class ControllerExtensionPaymentTasdid extends Controller
{

    const TEST_BASE_URL = 'https://api-uat.tasdid.net/';

    const BASE_URL = 'https://api.tasdid.net/';

    public $request_url;

    public $token;

    // Override parent method to set the request url and token.
    public function __construct($regisry)
    {
        parent::__construct($regisry);

        $this->request_url = $this->config->get('payment_tasdid_env') == 'TEST' ? static::TEST_BASE_URL : static::BASE_URL;

        $this->getToken();
    }

    // Get a token from the API
    public function getToken()
    {
        $curl = curl_init();

        $username = $this->config->get('payment_tasdid_username');

        $passeord = $this->config->get('payment_tasdid_password');

        $request = [
            'username' => $username,
            'password' => $passeord
        ];

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => "{$this->request_url}v1/api/Auth/Token",
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

        $this->token = $response['token'];

        return $response;
    }

    // Show the tasdid page.
    public function index()
    {
        return $this->load->view('extension/payment/tasdid', []);
    }

    // Confirm a payment
    public function confirm()
    {
        $json = array();

        if ($this->session->data['payment_method']['code'] == 'tasdid') {

            $this->load->model('checkout/order');

            $order_id = $this->session->data['order_id'];

            $order_info = $this->model_checkout_order->getOrder($order_id);

            $customerName = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];

            $email = $order_info['email'];

            $phone = $order_info['telephone'];

            $curl = curl_init();

            $value_currency = $this->config->get('payment_tasdid_currencies')[$order_info['currency_code']] ?? 1;

            $params = [
                //Be careful, do not modify or delete anything from the code below
                'payId' => '',
                'customerName' => $customerName,
                'ClientId' => 225,
                'dueDate' => date('Y-m-d', strtotime(date('Y-m-d') . ' + 5 days')),
                'phoneNumber' => $phone,
                'serviceId' => $this->config->get('payment_tasdid_service_id'),
                'amount' => $order_info['total'] * $value_currency,
                'note' => '',

            ];

            curl_setopt_array($curl, [
                CURLOPT_URL => $this->request_url . 'v1/api/Provider/AddBill',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_POSTFIELDS => json_encode($params),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $this->token
                ],
            ]);

            $response = curl_exec($curl);

            $err = curl_error($curl);

            curl_close($curl);

            $data['pay_link'] = '';
            $id = '';

            if ($err) {
                $json['error'] = 'An error occured. Contact site administrator, please!';

            } else {
                $response = json_decode($response, true);

                if ($response['error']) {
                    $json['error'] = $response['message'];
                } else {

                    $id = $response['data']['payId'];

                    $url = $this->config->get('payment_tasdid_env') == 'TEST' ? 'https://pay-uat.tasdid.net/' : 'https://pay.tasdid.net/';

                    $this->db->query('UPDATE `' . DB_PREFIX . "order` SET `tasdid_pay_id` = '" . $this->db->escape($id) . "' WHERE `order_id` = $order_id");

                    $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_tasdid_order_status_before_pay_id'));

                    $this->cart->clear();

                    unset($this->session->data['shipping_method']);
                    unset($this->session->data['shipping_methods']);
                    unset($this->session->data['payment_method']);
                    unset($this->session->data['payment_methods']);
                    unset($this->session->data['guest']);
                    unset($this->session->data['comment']);
                    unset($this->session->data['order_id']);
                    unset($this->session->data['coupon']);
                    unset($this->session->data['reward']);
                    unset($this->session->data['voucher']);
                    unset($this->session->data['vouchers']);
                    unset($this->session->data['totals']);

                    $json['redirect'] = "$url?id=" . $id . '&url=' . $this->config->get('payment_tasdid_redirect_url');
                }
            }

        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));

    }

    // Webhook handler.
    public function webhook()
    {
        $time = time();

        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data) || $data == null) {
            echo "Connection error";
            die;
        }



        $Key = strtoupper(md5($this->config->get('payment_tasdid_username') . '|' . $data['PayId'] . '|' . $data['Status']));

        if ($Key == $data['Key']) {

            if ($data && $data['Status'] == 3) {
                $query = $this->db->query('SELECT * FROM `' . DB_PREFIX . "order` WHERE `tasdid_pay_id` = '" . $data['PayId'] . "'");

                $order = $query->row;

                $this->load->model('checkout/order');

                $this->model_checkout_order->addOrderHistory($order['order_id'], $this->config->get('payment_tasdid_order_status_id'));

                echo 'Success';
                die;
            } else
                echo "Unauthorized";

        } else

            echo 'ERROR !';
        die;
    }
}