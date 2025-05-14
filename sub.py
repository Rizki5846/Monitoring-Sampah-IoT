import paho.mqtt.client as mqtt
import json
import requests

LARAVEL_API = "http://localhost:8000/api/data"  # Sesuaikan
MQTT_BROKER = "fedf34b846cf43389053a83eadaf35c1.s1.eu.hivemq.cloud"
MQTT_PORT = 8883

def on_connect(client, userdata, flags, rc):
    print("Connected with result code", rc)
    client.subscribe("iot/tempatsampah")

def on_message(client, userdata, msg):
    try:
        data = json.loads(msg.payload.decode())
        print("Received:", data)

        # Kirim ke Laravel (hanya device_id yang valid akan diproses)
        response = requests.post(LARAVEL_API, json=data)
        print("Laravel response:", response.status_code)

    except Exception as e:
        print("Error:", e)

client = mqtt.Client()
client.username_pw_set("hivemq.webclient.1746850271989", "t>K#AJ89i1@%0yHjeqVU")
client.tls_set()  # Enable TLS
client.on_connect = on_connect
client.on_message = on_message

client.connect(MQTT_BROKER, MQTT_PORT, 60)
client.loop_forever()
