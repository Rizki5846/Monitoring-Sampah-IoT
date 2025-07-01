import paho.mqtt.client as mqtt
import json
import requests

# Konfigurasi
LARAVEL_API = "http://localhost:8000/api/data"  # Laravel API endpoint
MQTT_BROKER = "fedf34b846cf43389053a83eadaf35c1.s1.eu.hivemq.cloud"
MQTT_PORT = 8883
MQTT_TOPIC = "iot/tempatsampah"
MQTT_USERNAME = "hivemq.webclient.1746850271989"
MQTT_PASSWORD = "t>K#AJ89i1@%0yHjeqVU"

# Fungsi ketika terkoneksi
def on_connect(client, userdata, flags, rc):
    if rc == 0:
        print("✅ Terhubung ke MQTT broker")
        client.subscribe(MQTT_TOPIC)
    else:
        print(f"❌ Gagal terhubung. Kode: {rc}")

# Fungsi ketika menerima pesan
def on_message(client, userdata, msg):
    try:
        payload = msg.payload.decode()
        data = json.loads(payload)
        print("📥 Diterima:", data)

        # Kirim ke Laravel
        response = requests.post(LARAVEL_API, json=data)
        if response.status_code == 200:
            print("✅ Data berhasil dikirim ke Laravel")
        else:
            print(f"❌ Gagal kirim ke Laravel: {response.status_code} | {response.text}")

    except Exception as e:
        print("❌ Error:", e)

# Setup client
client = mqtt.Client()
client.username_pw_set(MQTT_USERNAME, MQTT_PASSWORD)
client.tls_set()  # karena HiveMQ pakai TLS
client.on_connect = on_connect
client.on_message = on_message

# Mulai koneksi
print("🔌 Menghubungkan ke broker...")
client.connect(MQTT_BROKER, MQTT_PORT, 60)
client.loop_forever()
