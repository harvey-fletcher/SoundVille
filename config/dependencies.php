<?php

    class Dependencies{
        function confirmCaptcha( $response ){
            $secret = "6LcOKn4UAAAAAL-6A-Q7c2wN3zfJVhYz_1KjpAov";

            $data = array(
                    "secret" => $secret,
                    "response" => $response
                );

            $verify = curl_init();

            curl_setopt($verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
            curl_setopt($verify, CURLOPT_POST, true);
            curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($verify, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
            $response = json_decode( curl_exec($verify), true );

            return $response['success'];
        }

    }
