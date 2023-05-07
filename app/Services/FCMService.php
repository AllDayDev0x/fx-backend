<?php

namespace App\Services;

class FCMService
{ 
    public static function send($token, $notification, $data)
    {
        $fields = [
            "registration_ids" => [$token],
            "priority" => 10,
            'notification' => $notification,
            'data' => $data,
            'vibrate' => 1,
            'sound' => 1
        ];

        $headers = [
            'accept: application/json',
            'Content-Type: application/json',
            'Authorization: key=AAAACIRCOzk:APA91bGUejxWYLbTRTOMC_wd2Ha-4uH7RKnNo2_HW80z42W1kk4uYOI7USAJlnHWirl0lmeGMcYm1E1E3SyfP0I1IGrHjZOMOlPwPz_WFMDEXI7CUeMvddRNh-EuX3nOUXaYDdgYkKS2'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        \Log::info("Request Data".print_r($result, true));
        curl_close($ch);
        return $result;
    }
}