#include <OneWire.h>
#include <DallasTemperature.h>

// ======================
// KONFIGURASI PIN
// ======================
#define RELAY_PIN 27
#define TURBIDITY_PIN 34
#define ONE_WIRE_BUS 4

// ======================
// KALIBRASI TURBIDITY
// Berdasarkan hasil uji:
// Air bersih  = 4095
// Air kotor   = 3324 - 3530
// ======================
int batasKeruhON  = 3600;  // Pompa ON jika turbidity <= 3700
int batasKeruhOFF = 3800;  // Pompa OFF jika turbidity >= 3900

// Relay active LOW
#define POMPA_ON  LOW
#define POMPA_OFF HIGH

bool pompaNyala = false;

// Sensor suhu DS18B20
OneWire oneWire(ONE_WIRE_BUS);
DallasTemperature sensors(&oneWire);

void setup() {
  Serial.begin(115200);

  pinMode(RELAY_PIN, OUTPUT);

  // Pompa OFF saat awal
  digitalWrite(RELAY_PIN, POMPA_OFF);

  sensors.begin();

  Serial.println("Sistem Monitoring dan Filtrasi Air Aktif");
}

void loop() {
  // ======================
  // BACA SUHU
  // ======================
  sensors.requestTemperatures();
  float suhuC = sensors.getTempCByIndex(0);

  // ======================
  // BACA TURBIDITY RATA-RATA
  // ======================
  long total = 0;

  for (int i = 0; i < 10; i++) {
    total += analogRead(TURBIDITY_PIN);
    delay(10);
  }

  int turbidity = total / 10;

  // ======================
  // TAMPILKAN DATA
  // ======================
  Serial.print("Suhu: ");
  Serial.print(suhuC);
  Serial.print(" C | Turbidity: ");
  Serial.println(turbidity);

  // ======================
  // AIR KERUH -> POMPA ON
  // ======================
  if (turbidity <= batasKeruhON && !pompaNyala) {
    Serial.println("Status: AIR KERUH -> Pompa ON");

    digitalWrite(RELAY_PIN, POMPA_ON);
    pompaNyala = true;

    // Pompa nyala minimal 10 detik
    delay(10000);
  }

  // ======================
  // AIR JERNIH -> POMPA OFF
  // ======================
  if (turbidity >= batasKeruhOFF && pompaNyala) {
    Serial.println("Status: AIR JERNIH -> Pompa OFF");

    digitalWrite(RELAY_PIN, POMPA_OFF);
    pompaNyala = false;
  }

  // ======================
  // STATUS TAMBAHAN
  // ======================
  if (!pompaNyala) {
    Serial.println("Pompa: OFF");
  } else {
    Serial.println("Pompa: ON");
  }

  Serial.println("--------------------------");

  // Pembacaan tiap 5 detik
  delay(5000);
}
