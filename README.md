Documentation de Sms hub
========================

SMS Hub est une plateforme web qui permet d’envoyer par API  des SMS à vos utilisateurs en mode test lors du développement de vos applications. L’utilisation de cette plateforme passe par les étapes suivantes :

Création d’un jeton
------------

La création d’un jeton vous permettra de vous identifier par API lors de vos prochaines requêtes HTTP.




Envoyer un SMS
------------

**URL**:

```text
https://smshub.tuo-adama.com/api/messages
```

Method: **POST**


**Headers**:

```text
Authorization: Bearer {token}
Accept: application/json
```

### Réponses

201: Created


```json
{
	"id": 14,
	"recipient": "<votre numéro>",
	"message": "Votre de vérification est: 3990"
}
```


401:  Unauthorized

```json
{
	"message": "Bad credentials."
}
```

Exemples
------------

Javascript:
```javascript
fetch('https://smshub.tuo-adama.com/api/messages', {
    method: 'POST',
    headers: {
        'Authorization': 'Bearer <votre_token>',
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({ 
        to: '075109XXXX',
        message: 'Votre de vérification est: 3990'
    })
})

.then(response => response.json())

.then(data => console.log(data))

.catch(error => console.error('Erreur:', error));
```

Java:

```java
import java.io.OutputStream;
import java.net.HttpURLConnection;
import java.net.URL;

public class SendMessage {
    public static void main(String[] args) {
        try {
            URL url = new URL("https://smshub.tuo-adama.com/api/messages");
            HttpURLConnection connection = (HttpURLConnection) url.openConnection();
            connection.setRequestMethod("POST");
            connection.setRequestProperty("Authorization", "Bearer <votre_token>");
            connection.setRequestProperty("Content-Type", "application/json");
            connection.setDoOutput(true);

            String jsonInputString = "{\"to\": \"075109XXXX\", \"message\": \"Votre de vérification est: 3990\"}";

            try (OutputStream os = connection.getOutputStream()) {
                byte[] input = jsonInputString.getBytes("utf-8");
                os.write(input, 0, input.length);           
            }

            int code = connection.getResponseCode();
            System.out.println("Response Code: " + code);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }
}
```


**PHP**:

```php
<?php

require 'vendor/autoload.php';

  
use GuzzleHttp\Client;

use GuzzleHttp\Exception\RequestException;

  
$client = new Client();

$url = 'https://smshub.tuo-adama.com/api/messages';

$token = '<votre_token>'; // Remplace par ton vrai token

$data = [
     'to' => '075109XXXX',
     'message' => 'Votre de vérification est: 3990'
];

  
try {
     $response = $client->post($url, [
        'headers' => [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json'
        ],
         'json' => $data
    ]);
    
    $body = $response->getBody();
    
    echo "API response : ";

    print_r(json_decode($body, true));

} catch (RequestException $e) {
    
    echo "Erreur : " . $e->getMessage();
    
}

?>
```


