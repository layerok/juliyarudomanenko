<?php

namespace App\Controllers;

use App\Config;
use App\Models\Customer;
use App\Telegram;
use Core\Controller;
use \Core\View;
use \App\Models\Service;
use \App\Models\Appointment as AppointmentModel;
use \App\Request;

class Appointment extends Controller
{

    public function indexAction()
    {
        View::renderTemplate('Appointment/index.html', [
                "services" => Service::all()->toArray()
            ]
        );
    }

    /**
     * Show the appointment page
     *
     * @return void
     */
    public function showAction()
    {
        /** @var Service $record */
        $record = Service::findOrFail($this->route_params['id'] ?? null);
        $record->description = htmlspecialchars_decode($record->description);

        View::renderTemplate('Appointment/show.html', [
            'service' => $record
        ]);
    }

    /**
     * Show close the deal page
     *
     * @return void
     */
    public function closeTheDealAction()
    {
        /** @var Service $service */
        $service = Service::findOrFail($this->route_params['id'] ?? null);

        View::renderTemplate('Appointment/close-the-deal.html', [
            'service' => $service
        ]);
    }

    /**
     * Send appointment form
     *
     * @return void
     */
    public function sendAction()
    {
        if (!Request::isPost()) {
            throw new \Exception('POST method is not allowed');
        }

        /** @var Service $service */
        $service = Service::findOrFail($this->route_params['id'] ?? null);

        $_POST['service_id'] = $service->id;

        $errors = $this->validate();

//        if (Config::ENABLE_PRODUCTION) {
//            if (empty($this->{'g-recaptcha-response'})) {
//                // Флажок рекапчи не был отмечен
//
//                $errors['recaptcha_failed'] = "Подтвердите, что вы не робот!";
//            }
//        }
//        $verify = curl_init();
//        curl_setopt($verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify?secret=" . Config::SECRET_KEY . "&response=" . $this->{'g-recaptcha-response'});
//        curl_setopt($verify, CURLOPT_POST, true);
//        curl_setopt($verify, CURLOPT_SSL_VERIFYPEER, false);
//        curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
//        $response = curl_exec($verify);
//        $response = json_decode($response, true);
//
//        if ($response['success'] != true) {
//            $errors['recaptcha_failed'] =  "Ответ рекапчи не вернул успех";
//        }

        if (count($errors) > 0) {
            if (Request::isAjax()) {

                $response = [
                    "errors" => $errors
                ];
                echo json_encode($response);

            } else {
                View::renderTemplate('Appointment/close-the-deal.html', [
                    "service" => $service,
                    "errors" => $errors,
                    "post_data" => $_POST
                ]);

            }
            return;
        }

        $appointment = new AppointmentModel($_POST);
        $appointment->purchase_date = date("Y-m-d H:i:s");

        $customer = Customer::findByPhone($_POST['phone']);

        if (!$customer) {
            $customer = new Customer($_POST);
            $customer->save();
        }

        $appointment->customer()->associate($customer);
        $appointment->service()->associate($service);

        $appointment->save();

        if (Config::ENABLE_PRODUCTION) {
            // todo: I'll fix this nightmare, it is already 5 AM at the morning, I need some rest
            $telegram = new Telegram(Config::BOT_TOKEN, Config::CHAT_ID);
            $telegram->send("Запись на прием", [
                "name" => [
                    "value" => $appointment->customer->name ?? "",
                    "description" => "Имя",
                    "emoji" => "\xE2\x9C\x8F"
                ],
                "phone" => [
                    "value" => $appointment->customer->phone ?? "",
                    "description" => "Телефон",
                    "emoji" => "\xF0\x9F\x93\x9E"
                ],
                "format" => [
                    "value" => $appointment->format ?? "",
                    "description" => "Формат встречи",
                    "emoji" => "\xF0\x9F\x93\xA8"
                ]
            ]);
        }

        if (Request::isAjax()) {

            $response = [
                "errors" => $errors
            ];
            echo json_encode($response);

        } else {
            $this->redirect("/appointment/thank-you");

        }

    }

    public function validate(): array
    {
        $errors = [];
        $data = $_POST;
        // Name
        if (empty($data)) {
            $errors[] = 'Введите имя';
        }

        // phone format
        if (!preg_match('/^\+?3?8?\(?0\d{2}\)?\-?\d{3}\-?\d{2}\d{2}$/', $data['phone'])) {
            $errors[] = 'Введите телефон в формате +380xxxxxxx';
        }

        return $errors;
    }

    public function thankYouAction()
    {
        View::renderTemplate('Appointment/thank-you.html');
    }

}