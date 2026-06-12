#include <WiFi.h>
#include <HTTPClient.h>
#include <WiFiClientSecure.h>
#include <OneWire.h>
#include <DallasTemperature.h>

// ======================
// WIFI HOTSPOT HP
// ======================
const char* ssid = "yaja";
const char* password = "aaaaaaaa";

// ======================
// SERVER DATABASE
// ======================
// Jika pakai Ngrok:
String serverName = "http://192.168.211.6/kolam_ikan/kirim.php";

// Jika pakai IP laptop lokal, gunakan contoh ini:
// String serverName = "http://10.238.139.196/kolam_ikan/kirim.php";

// ======================
// KONFIGURASI PIN
// ======================
#define RELAY_PIN 27
#define TURBIDITY_PIN 34
#define TDS_PIN 32
#define ONE_WIRE_BUS 4

// ======================
// KALIBRASI TURBIDITY
// Air bersih = 4095
// Air kotor  = 3324 - 3530
// ======================
int batasKeruhON  = 500;
int batasKeruhOFF = 1500;

// ======================
// KALIBRASI TDS
// ======================
int batasTDS_OFF = 200;      // TDS <= 200 ppm = NORMAL, pompa OFF
int batasTDS_ON = 300;       // TDS >= 300 ppm = SEDANG, pompa ON
int batasTDSTinggi = 500;    // TDS >= 500 ppm = TINGGI, pompa ON

// ======================
// RELAY ACTIVE LOW
// LOW  = Pompa ON
// HIGH = Pompa OFF
// ======================
#define POMPA_ON  LOW
#define POMPA_OFF HIGH

bool pompaNyala = false;

// ======================
// SENSOR SUHU
// ======================
OneWire oneWire(ONE_WIRE_BUS);
DallasTemperature sensors(&oneWire);

// ======================
// FUNGSI BACA ANALOG STABIL
// ======================
int bacaAnalogStabil(int pin) {
  for (int i = 0; i < 5; i++) {
    analogRead(pin);
    delay(5);
  }

  long total = 0;

  for (int i = 0; i < 20; i++) {
    total += analogRead(pin);
    delay(10);
  }

  return total / 20;
}

void setup() {
  Serial.begin(115200);

  pinMode(RELAY_PIN, OUTPUT);
  digitalWrite(RELAY_PIN, POMPA_OFF);

  sensors.begin();

  analogReadResolution(12);
  analogSetAttenuation(ADC_11db);

  WiFi.begin(ssid, password);

  Serial.print("Menghubungkan ke WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println();
  Serial.println("WiFi Terhubung");
  Serial.print("IP ESP32: ");
  Serial.println(WiFi.localIP());

  Serial.println("Sistem Monitoring dan Filtrasi Air Aktif");
}

void loop() {
  // ======================
  // BACA SUHU
  // ======================
  sensors.requestTemperatures();
  float suhuC = sensors.getTempCByIndex(0);

  // ======================
  // BACA TURBIDITY
  // ======================
  int turbidity = bacaAnalogStabil(TURBIDITY_PIN);

  // ======================
  // BACA TDS
  // ======================
  int nilaiTDSAnalog = bacaAnalogStabil(TDS_PIN);
  float teganganTDS = nilaiTDSAnalog * (3.3 / 4095.0);

  float tdsValue = (133.42 * teganganTDS * teganganTDS * teganganTDS
                  - 255.86 * teganganTDS * teganganTDS
                  + 857.39 * teganganTDS) * 0.5;

  if (tdsValue < 0) {
    tdsValue = 0;
  }

  // ======================
  // VALIDASI TDS
  // Jika analog mentok 4095, dianggap tidak valid
  // ======================
  bool tdsValid = true;

  if (nilaiTDSAnalog >= 4090) {
    tdsValid = false;
    tdsValue = 0;
  }

  // ======================
  // STATUS AIR
  // ======================
  String statusAir;

  if (turbidity <= batasKeruhON) {
    statusAir = "KERUH";
  }
  else if (turbidity >= batasKeruhOFF) {
    statusAir = "JERNIH";
  }
  else {
    statusAir = "SEDANG";
  }

  // ======================
  // STATUS TDS
  // <= 200  = NORMAL
  // 201-499 = SEDANG
  // >= 500  = TINGGI
  // ======================
  String statusTDS;

  if (!tdsValid) {
    statusTDS = "TIDAK_VALID";
  }
  else if (tdsValue <= batasTDS_OFF) {
    statusTDS = "NORMAL";
  }
  else if (tdsValue < batasTDSTinggi) {
    statusTDS = "SEDANG";
  }
  else {
    statusTDS = "TINGGI";
  }

  // ======================
  // LOGIKA POMPA ON
  // Pompa ON jika air keruh ATAU TDS >= 300 ppm
  // ======================
  if ((turbidity <= batasKeruhON || (tdsValid && tdsValue >= batasTDS_ON)) && !pompaNyala) {
    Serial.println("Status: AIR KERUH / TDS SEDANG-TINGGI -> Pompa ON");

    digitalWrite(RELAY_PIN, POMPA_ON);
    pompaNyala = true;

    delay(10000);
  }

  // ======================
  // LOGIKA POMPA OFF
  // Pompa OFF jika air jernih DAN TDS <= 200 ppm
  // ======================
  if ((turbidity >= batasKeruhOFF && (!tdsValid || tdsValue <= batasTDS_OFF)) && pompaNyala) {
    Serial.println("Status: AIR JERNIH DAN TDS NORMAL -> Pompa OFF");

    digitalWrite(RELAY_PIN, POMPA_OFF);
    pompaNyala = false;
  }

  String statusPompa = pompaNyala ? "ON" : "OFF";

  // ======================
  // SERIAL MONITOR
  // ======================
  Serial.print("Suhu: ");

  if (suhuC == -127.00) {
    Serial.print("Sensor suhu tidak terbaca");
  } else {
    Serial.print(suhuC);
    Serial.print(" C");
  }

  Serial.print(" | Turbidity: ");
  Serial.print(turbidity);

  Serial.print(" | TDS: ");
  Serial.print(tdsValue);
  Serial.print(" ppm");

  Serial.print(" | Analog TDS: ");
  Serial.print(nilaiTDSAnalog);

  Serial.print(" | Tegangan TDS: ");
  Serial.print(teganganTDS);
  Serial.print(" V");

  Serial.print(" | Status Air: ");
  Serial.print(statusAir);

  Serial.print(" | Status TDS: ");
  Serial.print(statusTDS);

  Serial.print(" | Pompa: ");
  Serial.println(statusPompa);

  // ======================
  // KIRIM DATA KE DATABASE
  // ======================
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;

    String url = serverName;
    url += "?suhu=" + String(suhuC);
    url += "&turbidity=" + String(turbidity);
    url += "&tds=" + String(tdsValue);
    url += "&status_air=" + statusAir;
    url += "&status_tds=" + statusTDS;
    url += "&status_pompa=" + statusPompa;


    int httpResponseCode;

    if (url.startsWith("https://")) {
      WiFiClientSecure client;
      client.setInsecure();
      http.begin(client, url);
      httpResponseCode = http.GET();
    } else {
      http.begin(url);
      httpResponseCode = http.GET();
    }

    Serial.print("HTTP Response code: ");
    Serial.println(httpResponseCode);

    String payload = http.getString();
    Serial.println(payload);

    http.end();
  } else {
    Serial.println("WiFi terputus");
  }

  Serial.println("--------------------------");

  delay(3000);
}